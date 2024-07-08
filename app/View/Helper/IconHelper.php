<?php
App::uses('AppHelper', 'View/Helper');

class IconHelper extends AppHelper {
    public function add($content = '', $icon_only = false, $use_default = false) {
    	$icons = array(
        	"Short Contract" => 'cycle',
			"No fixed term" => 'nolock',
			"No term" => 'nolock',
			"Fair Pricing Promise" => 'guarantee',
        	"AGL Assist Voucher" => 'shopping',
			"Lock-In Rates" => 'lockin',
			"100% Australian Owned and Operated" => 'australia',
			"Lumo Advantage Online Membership" => 'shopping',
			"Low Rates" => 'save',
        	"Monthly Installments" => 'detail',
			"Renewable Energy" => 'eco',
			"Very Environment Focused" => 'eco',
			"Mover's Guarantee" => 'guarantee',
			"Powerful Backing" => 'detail',
			"Leading Energy Company" => 'detail',
        	"Award Winning Service" => 'tick',
			"Tons of payment methods" => 'wallet',
			"Online Monthly Billing" => 'wallet',
        	"100% Australian Owned" => 'australia',
			"Smartphone App" => 'phone',
		);
		foreach ($icons as $key => $value) {
        	if (stripos($content, $key) !== false) {
            	if ($icon_only) {
                	return "<span class='{$value}'></span>";
				} else {
                	return "<span class='{$value}'></span><span class='icon_text'>" . $content . "</span>";
				}
				break;
			}
		}
		// not found?
		if ($icon_only) {
        	if ($use_default && $content) {
	        	return "<span class='tick'></span>";
        	}
			return '';
		} else {
        	if ($use_default && $content) {
	        	return "<span class='tick'></span><span class='icon_text'>" . $content . "</span>";
        	}
			return $content;
		}
	}
}