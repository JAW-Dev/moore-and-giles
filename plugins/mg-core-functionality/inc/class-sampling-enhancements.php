<?php

class MG_Sampling_Enhancements {
	public function __construct() {
		add_action('shopp_themeapi_purchase_sortitems', array($this, 'sort_items'), 0, 3);
	}

	function sort_items($result, $options, $Purchase) {
		usort($Purchase->purchased, array($this, 'cmp') );

		return $result;
	}

	function cmp($a, $b) {
		return strcmp($a->name . ' ' . $a->optionlabel, $b->name . ' ' . $b->optionlabel);
	}
}