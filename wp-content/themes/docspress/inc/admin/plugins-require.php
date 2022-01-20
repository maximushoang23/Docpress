<?php

function thim_get_all_plugins_require( $plugins ) {
	return array(
		array(
			'name'     => 'Visual Composer',
			'slug'     => 'js_composer',
			'source'   => THIM_DIR . 'inc/plugins/js_composer.zip',
			'required' => true,
			'version'  => '4.12',
			'icon'     => THIM_URI . 'assets/images/plugins/js_composer.png',
		),
		array(
			'name' => 'bbPress',
			'slug' => 'bbpress',
		),
		array(
			'name' => 'MailChimp',
			'slug' => 'mailchimp-for-wp',
		),
		array(
			'name' => 'Contact Form 7',
			'slug' => 'contact-form-7',
		),
		array(
			'name' => 'WooCommerce',
			'slug' => 'woocommerce',
		),
		array(
			'name' => 'LearnPress',
			'slug' => 'learnpress',
		),
		array(
			'slug' => 'yith-woocommerce-wishlist',
			'name' => 'YITH WooCommerce Wishlist',
			'icon' => 'https://ps.w.org/yith-woocommerce-wishlist/assets/icon-128x128.jpg',
		)
	);
}

add_action( 'thim_core_get_all_plugins_require', 'thim_get_all_plugins_require' );