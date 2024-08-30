<?php
/*
Plugin Name: MG Leather Portfolio
Version: 1.0
Description: Adds gallery selector to the leather product editor.
Author: Clifton H. Griffin II
Author URI: http://clifgriffin.com
*/

class MG_LeatherPortfolio {
	public function __construct() {
		if ( isset($_GET['id']) && $_GET['id'] != "new" ) {
			add_action( 'admin_head', array($this, 'add_gallery_selector') );
		}

		add_action('shopp_product_saved', array($this, 'save') );
	}

	function save($Product) {
		if( isset($_REQUEST['leather_gallery']) ) {
			$product_id = $Product->id;

			shopp_set_meta($product_id, 'product', 'leather_gallery', $_REQUEST['leather_gallery']);
		}
	}

	function add_gallery_selector() {
		add_meta_box(
	        'mglp_gallery_selector',
	        'Portfolio Gallery',
	        array($this, 'inner'),
	        'toplevel_page_shopp-products',
			'advanced',
			'core'
	    );
	}

	function inner() {
		global $wpdb;
		$galleries = $wpdb->get_results(
    "SELECT gid, title FROM ".$wpdb->prefix."ngg_gallery ORDER BY gid ASC");


		$selected = shopp_meta($_GET['id'], 'product', 'leather_gallery'); ?>

    	<select name="leather_gallery" id="leather_gallery">
    		<option value="">Select a gallery</option>
    		<?php foreach($galleries as $gallery): ?>
			<option value="<?php echo $gallery->gid; ?>" <?php if($gallery->gid == $selected) echo 'selected="selected"'; ?>><?php echo str_replace("LG_", "", $gallery->title); ?></option>
    		<?php endforeach; ?>
    	</select>
    	<?php
	}
}

$MG_LeatherPortfolio = new MG_LeatherPortfolio();
