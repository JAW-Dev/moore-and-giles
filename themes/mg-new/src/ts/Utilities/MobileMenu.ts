import { Main } from "../Main";

// Set constants.
const mobileMenuClass: string = "mobile-menu-active";
const navChildOfBase: string = "nav-wrap-child-of-";

export class MobileMenu {
	moveMobileLevelActionElements(): void {
		let secondLevelNavs = document.querySelectorAll(
			".nav-woo_bags-mobile .nav-child-wrap-list"
		);
		let mobileNavSecondLevelWrap = document.querySelector(
			".mobile-nav-container .mobile-nav-second-level .target"
		);

		for (let i = 0; i < secondLevelNavs.length; i++) {
			let nav = secondLevelNavs[i];
			let classes = jQuery(nav)
				.attr("class")
				.split(" ");
			let base: string = "";

			classes.forEach(cssClass => {
				if (cssClass.indexOf(navChildOfBase) !== -1) {
					base = cssClass.split(navChildOfBase)[1];
				}
			});

			let preppedNav = {
				slug: base,
				nav: jQuery(nav)
			};

			jQuery(mobileNavSecondLevelWrap).append(preppedNav.nav);
		}
	}

	setMobileMenuCloseOpenActions(): void {
		let menuBtn: HTMLElement = <HTMLElement>(
			document.querySelector(".nav-meta .menu")
		);
		let siteWrap: HTMLElement = <HTMLElement>(
			document.querySelector(".site-wrap")
		);
		let mobileMenuCloseBtn: HTMLElement = <HTMLElement>(
			document.querySelector(".mobile-nav-controls .close-icon-wrap")
		);

		let openMobileMenu: () => void = () =>
			jQuery(siteWrap).addClass(mobileMenuClass);
		let closeMobileMenu: () => void = () => {
			jQuery(siteWrap).removeClass(mobileMenuClass);
			this.resetMobileMenu();
		};

		menuBtn.addEventListener("click", openMobileMenu);
		mobileMenuCloseBtn.addEventListener("click", closeMobileMenu);
		window.addEventListener("resize", () => {
			if (
				window.innerWidth >=
				Main.instance.siteSettings.breakpoints["tablet-large"].num
			) {
				closeMobileMenu();
			}
		});
	}

	setMobileLevelClickActions(): void {
		let mobileLinks = jQuery(
			".nav-woo_bags-mobile .heading.menu-item.menu-item-has-children > a"
		);
		let mobileNavContainer = jQuery(".mobile-nav-container");
		let navMetaTarget = jQuery(
			".mobile-nav-meta-links-target .class-target"
		);
		let navViewAllTarget = jQuery(".mobile-nav-second-level .view-all");
		let secondLevelTargets = jQuery(
			".mobile-nav-second-level .target .nav-wrap"
		);

		mobileLinks.each((index, mobileLink) => {
			jQuery(mobileLink).on("click", e => {
				e.preventDefault();
				let data = jQuery(mobileLink)
					.parent()
					.data();
				let targetList = jQuery(
					`.mobile-nav-container [data-parent="${data.slug}"]`
				);

				secondLevelTargets.each((index, secondLevelTarget) => {
					jQuery(secondLevelTarget).removeClass("show");
				});

				targetList.addClass("show");

				navMetaTarget.text(data.title);
				jQuery(navMetaTarget).attr("href", data.link);
				jQuery(navViewAllTarget).attr("href", data.link);
				mobileNavContainer.addClass("next-level");
			});
		});
	}

	resetMobileMenu(): void {
		let mobileNavContainer = document.querySelector(
			".mobile-nav-container"
		);
		jQuery(mobileNavContainer).removeClass("next-level");
	}

	setMobileLevelBackAction(): void {
		let mobileNavBack = document.querySelector(
			".mobile-nav-container .back-icon-wrap"
		);
		mobileNavBack.addEventListener("click", this.resetMobileMenu);
	}
}
