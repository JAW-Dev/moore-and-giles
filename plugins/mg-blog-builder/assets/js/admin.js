function blogBuilder($) {
    var $this = this;
    var settings;

    /**
     * Build out the settings
     */
    function buildSettings() {
        settings = {
            popupCloseButton: $(document.getElementById('blog-builder-close')),
            popupButton: $(document.getElementById('insert-blog-component')),
            popup: $(document.getElementById('obj-blog-builder-wrap')),
            popupOpenClass: 'blog-builder-open',
            popupContent: $(document.getElementById('obj-blog-builder-content')),
            module: $('.blog-builder-module'),
            panel: $(document.getElementById('obj-blog-builder-panel')),
            panelCloseButton: $(document.getElementById('blog-builder-panel-close')),
            panelOpenClass: 'panel-is-open',
            mediaButtonId: 'blog-module-upload-img',
            insertButton: $(document.getElementById('blog-builder-insert'))
        };
    }

    /**
     * Special char encode
     */
    this.specialCharEncode = function(string) {
        return string.replace(/\[/g, "&#91;").replace(/\]/g, "&#93;").replace(/\"/g, "'");
    };

    /**
     * Check to see if popup is open
     */
    this.popupIsOpen = function() {
        if (settings.popup.hasClass('blog-builder-open')) {
            return true;
        }

        return false;
    };

    /**
     * Check to see if settings panel is open
     */
    this.panelIsOpen = function() {
        if (settings.panel.hasClass('panel-is-open')) {
            return true;
        }

        return false;
    };

    /**
     * Check to see if modules settings are being displayed
     */
    this.settingsAreVisible = function() {
        if (settings.module.hasClass('settings-are-visible')) {
            return true;
        }

        return false;
    };

    /**
     * Open the Popup
     */
    this.openPopup = function() {
        settings.popup.addClass(settings.popupOpenClass);
    };

    /**
     * Close the popup
     */
    this.closePopup = function() {
        settings.popup.removeClass(settings.popupOpenClass);
    };

    /**
     * Open the panel
     */
    this.openPanel = function() {
        settings.panel.addClass(settings.panelOpenClass);
        settings.popupContent.addClass(settings.panelOpenClass);
    };

    /**
     * Close the panel
     */
    this.closePanel = function() {
        settings.panel.removeClass(settings.panelOpenClass);
        settings.popupContent.removeClass(settings.panelOpenClass);
    };

    /**
     * Toggle the Popup
     */
    this.togglePopup = function() {
        if ($this.popupIsOpen()) {
            $this.closePopup();
        } else {
            $this.openPopup();
        }
    };

    /**
     * Toggle the panel
     */
    this.togglePanel = function(module) {
        if ($this.panelIsOpen()) {
            jQuery('#settings-' + module).show().siblings().hide();
        } else {
            $this.openPanel();
            jQuery('#settings-' + module).show().siblings().hide();
        }
    };

    /**
     * Open Media Manager
     */
    this.openMediaManager = function(e) {
        var parentId;
        var file_frame;

        parentId = e.currentTarget.parentElement.id;

        if ( file_frame ) {
          	file_frame.open();
          	return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          	title: jQuery( this ).data( 'uploader_title' ),
          	button: {
            	text: jQuery( this ).data( 'uploader_button_text' ),
          	},
          	multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          var attachment = file_frame.state().get('selection').first().toJSON();
          jQuery('#' + parentId + ' input[type="text"]').val(attachment.url);
          jQuery('#' + parentId).next().find('input[type="text"]').val(attachment.id);
        });

        file_frame.open();
    };

    /**
     * Isert component into editor
     */
    this.insertComponent = function() {
        var val;
        var selectedShortcode = $('.module-is-active').find('a').attr('data-module-value');
        var result = $('#blog-builder-shortcode-result');
        var opener = '[blog_module_' + selectedShortcode;
        var closer = ']';

        $('#blog-builder-shortcode-result').val('[blog_module_' + selectedShortcode);
		$('#settings-' + selectedShortcode + ' .blog-module-attr').each(function() {
			if ( $(this).val() !== '' ) {
				var val = $this.specialCharEncode($(this).val());
				$('#blog-builder-shortcode-result').val( $('#blog-builder-shortcode-result').val() + ' ' + $(this).attr('name') + '="' + val + '"' );
			}
		});
		$('#blog-builder-shortcode-result').val($('#blog-builder-shortcode-result').val() + ']');

        window.send_to_editor($('#blog-builder-shortcode-result').val());

        $this.closePanel();
        $this.closePopup();
    };

    /**
     * Load all popup functionality
     */
    this.init = function() {
        buildSettings();

        settings.popupButton.on('click', function(e) {
            e.preventDefault();
            $this.togglePopup();
        });

        settings.popupCloseButton.on('click', function(e) {
            e.preventDefault();
            $this.closePopup();
        });

        settings.module.on('click', function(e) {
            e.preventDefault();
            var selectedModule = jQuery(this).find('a').attr('data-module-value');
            jQuery(this).addClass('module-is-active').siblings().removeClass('module-is-active');
            $this.togglePanel(selectedModule);
        });

        settings.panelCloseButton.on('click', function(e) {
            e.preventDefault();
            $this.closePanel();
        });

        settings.insertButton.on('click', function(e) {
            e.preventDefault();
            $this.insertComponent();
        });

        $(document).on('click', '#' + settings.mediaButtonId, function(e) {
            e.preventDefault();
            $this.openMediaManager(e);
        });
    };
}

var blogBuilder = new blogBuilder(jQuery);

jQuery(document).ready(function() {
    blogBuilder.init();
});
