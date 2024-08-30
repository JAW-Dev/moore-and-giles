// Import Core Modules
import { ActionSet } from "../Base/ActionSet";
import { Main } from "../Main";

// Import Modules.
import { MobileMenu } from "../Utilities/MobileMenu";
import { ProductQuantity } from "../Utilities/ProductQuantity";
import { Cart } from "../Utilities/Cart";
import { Tooltips } from "../Utilities/Tooltips";
import { LandingGallery } from "../Utilities/LandingGallery";
import { Slick } from "../Utilities/Slick";
import { SimpleCarousel } from "../Utilities/SimpleCarousel";
import { Modals } from "../Utilities/Modals";
import "picturefill";
import { ArchiveHoverImage } from "../Utilities/ArchiveHoverImage";

declare let FWP: any;
declare let mgData: any;
declare let jQuery: any;

export class GlobalActionSet extends ActionSet {
	/**
	 * @private
	 * @type {Slick}
	 */
	private _slick: Slick;

	/**
	 * @private
	 * @type {Slick}
	 */
	private _slickNav: Slick;

	/**
	 * Loads the global site actions
	 */
	load(): void {
		this.temporarilyBlockCartOnLoad();
		jQuery.ajaxSetup({ cache: false });
		this.setMobileMenu();
		this.setHeaderDropdownMenu();
		this.setDropdownSideMenu();
		this.initCartActions();
		this.initProductSummaryActions();
		this.initLandingPageSlider();
		this.mentionsCarousel();
		this.travelHolidaysCarousel();
		this.pastAndUpcomingCarousel();
		this.productImageCarouselAndInfo();
		this.copyCarousel();
		this.customerReviewsCarousel();
		this.setSearchSection();
		this.facetWPTweaks();
		this.wooLoginFormCheckboxStyle();
		this.swapArchiveHoverImage();
		this.swapAddToCartLabel();
		this.initModals();
		this.initProductQuantityControls();
		this.productImageCarouselAndInfoTimer();
		this.initSmoothScrollLink();
	}

	/**
	 * Initialize Modaal Modals
	 *
	 * @author Jason Witt
	 *
	 * @returns void
	 */
	public initModals(): void {
		let modal = new Modals();
        modal.init(".swatch-form");
        modal.video(".video_modal");
    }

    initSmoothScrollLink(): void {
        // Link to and scroll to a section id.
        jQuery('a[href^="#"]').on('click', function(event) {
            var target = jQuery(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                jQuery('html, body')
                    .stop()
                    .animate(
                    {
                        scrollTop: target.offset().top
                    },
                    1000
                    );
                }
        });
	}

	swapArchiveHoverImage(): void {
		let archive_hover_image_swap = new ArchiveHoverImage();
		archive_hover_image_swap.init();
	}

	swapAddToCartLabel(): void {
		jQuery(document.body).on('wvs-items-updated found_variation woocommerce_variation_has_changed', function (event) {
			setTimeout(() => {
				//debugger;

				let selected_variant = jQuery( event.target ).find( 'ul[data-attribute_name^="attribute_"] li.selected' ).data('value');

				if ( ! selected_variant ) {
					selected_variant = jQuery( event.target ).find( 'li.selected' ).data('value');
				}

				let variations = jQuery(event.target).closest('.variations_form').data('product_variations');

				Object.keys(variations).forEach(function (key) {
					Object.keys(variations[key].attributes).forEach(function (a_key) {
						if (variations[key].attributes[a_key] == selected_variant && typeof variations[key].add_to_cart_text !== 'undefined') {
							jQuery(event.target).find('.single_add_to_cart_button').text(variations[key].add_to_cart_text);
						}
					} );
				});
			}, 50);
		});
	}

	/**
	 * Set up the related javascript events to handle the mobile menu
	 */
	setMobileMenu(): void {
		let mobileMenu = new MobileMenu();
		mobileMenu.moveMobileLevelActionElements();
		mobileMenu.setMobileMenuCloseOpenActions();
		mobileMenu.setMobileLevelClickActions();
		mobileMenu.setMobileLevelBackAction();
	}

	/**
	 * Mentions Carousel.
	 *
	 * @return void
	 */
	mentionsCarousel(): void {
		let navableItems: number = jQuery(
			".carousel-mentions .company-shoutouts img"
		).length;

		this.slick = new Slick(
			<JQuery>jQuery(".carousel-mentions .carousel"),
			{
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: false,
				asNavFor: ".carousel-mentions .company-shoutouts"
			},
			<JQuery>jQuery(".carousel-mentions .prev-arrow"),
			<JQuery>jQuery(".carousel-mentions .next-arrow")
		);

		this.slickNav = new Slick(
			<JQuery>jQuery(".carousel-mentions .company-shoutouts"),
			{
				slidesToShow: navableItems,
				focusOnSelect: true,
				asNavFor: ".carousel-mentions .carousel"
			}
		);

		this.slick.run();
		this.slickNav.run();
	}

	/**
	 * Travel Holidays Carousel.
	 *
	 * @return void
	 */
	travelHolidaysCarousel(): void {
		const carousel = new SimpleCarousel();
		carousel.init(
			{
				selector: '.travel-holidays .carousel',
				prevArrow: '.travel-holidays .prev-arrow',
				nextArrow: '.travel-holidays .next-arrow',
				carouselArgs: {
					slidesToShow: 4,
					slidesToScroll: 1,
					dots: false,
				}
			}
		);
	}

	/**
	 * Past and Upcoming Carousel.
	 *
	 * @return void
	 */
	pastAndUpcomingCarousel(): void {
		const carousel = new SimpleCarousel();


		carousel.init(
			{
				selector: '.past-and-upcoming__right-carousel',
				carouselArgs: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					speed: 300,
					arrows: false,
					draggable: false
				}
			}
		);

		carousel.init(
			{
				selector: '.past-and-upcoming__right-small-carousel',
				carouselArgs: {
					slidesToShow: 3,
					slidesToScroll: 1,
					dots: false,
					speed: 260,
					variableWidth: true,
					arrows: false,
					draggable: false
				}
			}
		)

		carousel.init(
			{
				selector: '.past-and-upcoming__left-carousel',
				prevArrow: '.past-and-upcoming__carousel-controlls .prev-arrow',
				nextArrow: '.past-and-upcoming__carousel-controlls .next-arrow',
				carouselArgs: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					fade: true,
					speed: 100,
					cssEase: 'linear',
					asNavFor: '.child-carousel',
					draggable: false
				}
			}
		);
	}

	/**
	 * Product Image Carosel And Info Carousel.
	 *
	 * @return void
	 */
	productImageCarouselAndInfo(): void {
		const carousel: any = jQuery('.product-image-carousel-info__carousel');
		const selector: any = jQuery('.product-image-carousel-info__carousel-menu-image');

		carousel.slick({
			dosts: false,
			arrows: false
		});

		jQuery(selector[0]).addClass('selected');

		selector.click(e => {
			e.preventDefault();
			const slideIndex = jQuery(e.target).index();
			carousel[0].slick.slickGoTo(slideIndex);

			selector.each((index, element) => {
				jQuery(element).removeClass('selected');
			})
			jQuery(e.target).addClass('selected');
		});
	}

	/**
	 * Product Image Carosel And Info Timer.
	 *
	 * @return void
	 */
	productImageCarouselAndInfoTimer(): void {
		const countDownDate = new Date(mgData.countDownTime).getTime();

		const func = setInterval(() => {
			const now = new Date().getTime();
			const timeLeft = countDownDate - now;

			const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
			const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
			const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
			const productImageCarouselInfoTimer = jQuery('#product-image-carousel-info-timer');

			if (productImageCarouselInfoTimer.length) {
				jQuery('#timer-days').html(days + '');
				jQuery('#timer-hours').html(hours + '');
				jQuery('#timer-min').html(minutes + '');
				jQuery('#timer-sec').html(seconds + '');
			}

			if (timeLeft < 0) {
				clearInterval(func);
				productImageCarouselInfoTimer.hide();
			}
		}, 1000);
	};

	/**
	 * Travel Holidays Carousel.
	 *
	 * @return void
	 */
	copyCarousel(): void {
		const carousel: any = jQuery('.copy-carousel__carousel');
		const selector: any = jQuery('.copy-carousel__selector-item');

		carousel.slick({
			dosts: false,
			arrows: false,
			speed: 100,
			fade: true,
			cssEase: 'linear'
		});

		jQuery(selector[0]).addClass('selected');

		selector.click(e => {
			e.preventDefault();
			const slideIndex = jQuery(e.target).index();
			carousel[0].slick.slickGoTo(slideIndex);

			selector.each((index, element) => {
				jQuery(element).removeClass('selected');
			})
			jQuery(e.target).addClass('selected');
		});
	}

	/**
	 * Customer Reviews Carousel.
	 *
	 * @return void
	 */
	customerReviewsCarousel(): void {
		const carousel = new SimpleCarousel();
		carousel.init(
			{
				selector: '.customer-reviews .customer-reviews__sidebar_carousel .carousel',
				prevArrow: '.customer-reviews .prev-arrow',
				nextArrow: '.customer-reviews .next-arrow',
				carouselArgs: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					fade: true,
					cssEase: 'linear',
					asNavFor: '.customer-reviews .customer-reviews__image_carousel .carousel',
					adaptiveHeight: true
				}
			}
		);

		carousel.init(
			{
				selector: '.customer-reviews .customer-reviews__image_carousel .carousel',
				carouselArgs: {
					slidesToShow: 1,
					focusOnSelect: true,
					fade: true,
					cssEase: 'linear',
					asNavFor: '.customer-reviews .customer-reviews__sidebar_carousel .carousel'
				}
			}
		);
	}

	/**
	 * Cart actions.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	initCartActions(): void {
		let cart = new Cart();
		cart.init();
		cart.sidebarCartToggle();
		cart.updateCartOnQtyChange();
	}

	/**
	 * Product summary actions.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	initProductSummaryActions(): void {
		Tooltips.init();

		jQuery(document).on("jckqv_open", () => {
			Tooltips.init();
		});
	}

	/**
	 * Set up the related javascript events to handle the header drop down menu
	 */
	setHeaderDropdownMenu(): void {
		// Actors
		let hoverLinks = jQuery('.mega-menu-triggers .top-menu-item');
		let siteHeader = jQuery('.site-header');
		let rightHeader = jQuery('.right-icon-menu-wrap');
		let topBar = jQuery('.site-top-bar');
		let heroBanner = jQuery('.hero-banner');
		let dropDown = jQuery('.mega-menu-content-outer');

		// Actions
		let enterAction = (link, duration: number = 300) => {
			if (!jQuery(link).hasClass('active')) {
				// Set proper active class on main menu item
				jQuery(hoverLinks).removeClass('active');
				jQuery(link).addClass('active');

				// Set proper "sub menu" to active class and fade it in
				let dropId = link.attr('id');
				jQuery('.mega-menu-item-content').removeClass('active').hide();
				jQuery('.mega-menu-item-content#' + dropId).addClass('active').css('display', 'flex').hide().fadeIn(duration);

				// Slide the dropdown down
				jQuery(dropDown).slideDown(duration);


				let content_element = jQuery('.mega-menu-item-content#' + dropId + ' .side-hover-content.active');

				content_element.find('img[data-mg-src!=""]').each(function (i, element) {
					jQuery(this).attr('src', jQuery(this).attr('data-mg-src')).removeAttr('data-mg-src');
				});
			}
		};

		let exitAction = (duration: number = 300) => {
			jQuery(dropDown).slideUp(duration);
			jQuery(hoverLinks).removeClass('active');
		};

		// If the hover link exists, run the action
		if (typeof hoverLinks !== 'undefined' && hoverLinks !== null) {
			jQuery.each(hoverLinks, function (index, element) {
				jQuery(this).on('mouseenter', function () {
					enterAction(jQuery(this))
				});
			});
		}

		// Exit the menu on these actions
		siteHeader.on('mouseleave', () => exitAction());
		rightHeader.on('mouseenter', () => exitAction());
		topBar.on('mouseenter', () => exitAction());

		if (typeof heroBanner !== 'undefined' && heroBanner) {
			heroBanner.on('mouseenter', () => exitAction());
		}

		window.addEventListener('resize', () => {
			if (
				window.innerWidth <
				Main.instance.siteSettings.breakpoints['tablet-large'].num
			) {
				exitAction(0);
			}
		});
	}

	/**
	 * Set up the related javascript events to handle the switching of items in the mega menu
	 */
	setDropdownSideMenu(): void {
		var timeout = null;

		// Actors
		let sideLinks = jQuery('.mega-menu-content-wrap .side-links .side-hover-item');

		// Actions
		let enterAction = (link) => {
			if (!jQuery(link).hasClass('active')) {
				var parentMenu = jQuery(link).parents('.mega-menu-item-content');
				var parentMenuId = parentMenu.attr('id');

				// Set proper active class on main menu item
				jQuery('#' + parentMenuId + ' .side-hover-item').removeClass('active');

				jQuery(link).addClass('active');

				// Set proper "sub menu" to active class and fade it in
				let contentId = link.attr('id');

				let content_element = jQuery('#' + parentMenuId + ' .side-hover-content#' + contentId);
				jQuery('#' + parentMenuId + ' .side-hover-content').removeClass('active');

				content_element.addClass('active');

				content_element.find('img[data-mg-src!=""]').each(function (i, element) {
					jQuery(this).attr('src', jQuery(this).attr('data-mg-src')).removeAttr('data-mg-src');
				});
			}
		};

		// If the hover link exists, run the action
		if (typeof sideLinks !== 'undefined' && sideLinks !== null) {
			jQuery.each(sideLinks, function (link, index) {
				jQuery(this).on('mouseenter', function () {
					timeout = setTimeout(() => {
						enterAction(jQuery(this));
					}, 100);
				});

				jQuery(this).on('mouseleave', function () {
					clearTimeout(timeout);
				});
			});
		}
	}

	/**
	 * Set up the related javascript events to handle the search drop down
	 */
	setSearchSection(): void {
		const searchLink: JQuery = jQuery(".search.menu-item, .close-search");
		const searchInput: JQuery = jQuery(".site-search .site-search-input");
		const siteBody: JQuery = jQuery("body");

		jQuery(searchLink).on("click", function (event) {
			event.preventDefault();
			siteBody.toggleClass("search-active");
			searchInput.focus();
		});
	}

	/**
	 * Set up facetwp tweak
	 */
	facetWPTweaks(): void {
		jQuery(document).on("facetwp-loaded", function () {
			var count = 0;
			jQuery.each(FWP.facets, function (name, vals) {
				if (FWP.facets[name].length > 0) {
					count++;
				}
			});

			if (count > 0) {
				jQuery("body").addClass("fwp-filtered");
			} else {
				jQuery("body").removeClass("fwp-filtered");
			}
		});
	}

	/**
	 * Set up the related javascript events to add a class to the checkbox on the woocommerce login form
	 */
	wooLoginFormCheckboxStyle(): void {
		jQuery(document).ready(function () {
			let loginRemember = jQuery(".woocommerce-form-login #rememberme");

			if (loginRemember.length) {
				jQuery(loginRemember)
					.parents(".form-row")
					.addClass("login-remember-row");
				jQuery(loginRemember)
					.parents("label")
					.addClass("woocommerce-form-login__rememberme");
			}
		});
	}

	/**
	 * Carousel for the landing pages.
	 */
	initLandingPageSlider(): void {
		const landingGallery = new LandingGallery();
		landingGallery.init();
	}

	temporarilyBlockCartOnLoad(): void {
		jQuery('#mg_cart_wrap').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	}

	initProductQuantityControls(): void {
		const productQty = new ProductQuantity();
		productQty.productAmountButtons();
		productQty.resizeInputs();
		productQty.hideStepper();

		jQuery(document.body).on('quick-view-displayed wc_fragments_loaded wc_fragments_refreshed', () => {
			productQty.resizeInputs();
			productQty.hideStepper();
		})
	}

	/**
	 * @returns {Slick}
	 */
	get slick(): Slick {
		return this._slick;
	}

	/**
	 * @param {Slick} value
	 */
	set slick(value: Slick) {
		this._slick = value;
	}

	/**
	 * @returns {Slick}
	 */
	get slickNav(): Slick {
		return this._slickNav;
	}

	/**
	 * @param {Slick} value
	 */
	set slickNav(value: Slick) {
		this._slickNav = value;
	}
}
