<?php

if ( class_exists( 'Thim_Core' ) ) {
	return;
}

add_action( 'admin_notices', 'thim_notify_install_plugins' );

function thim_notify_install_plugins() {
	?>
	<div class="notice notice-success is-dismissible">
		<h3>Thim-StarterTheme notice!</h3>
		<p>
			Install theme success. <a
				href="<?php echo esc_url( admin_url( '?thim-install-plugin-require=1' ) ); ?>">
				<?php _e( 'Install and active ThimPress Core to start now!', 'thim-docspress' ); ?>
			</a>
		</p>
	</div>
	<?php
}

function thim_get_core_require() {
	$thim_core = array(
		'name'   => 'Thim Core',
		'slug'   => 'thim-core',
		'source' => THIM_DIR . 'inc/plugins/thim-core.zip',
	);

	return $thim_core;
}

add_action( 'admin_init', 'thim_install_plugin_require' );

function thim_install_plugin_require() {
	$install = isset( $_GET['thim-install-plugin-require'] );

	if ( ! $install ) {
		return;
	}

	require_once THIM_DIR . 'inc/libs/class-thim-plugin.php';

	$plugin_require = thim_get_core_require();

	$plugin = new Thim_Plugin();
	$plugin->set_args( $plugin_require );
	$plugin->install();

	wp_redirect( admin_url( '?thim-active-plugin-require=1' ) );
	exit();
}

add_action( 'admin_init', 'thim_active_plugin_require' );

function thim_active_plugin_require() {
	$active = isset( $_GET['thim-active-plugin-require'] );

	if ( ! $active ) {
		return;
	}

	require_once THIM_DIR . 'inc/libs/class-thim-plugin.php';

	$plugin_require = thim_get_core_require();

	$plugin = new Thim_Plugin();
	$plugin->set_args( $plugin_require );
	$plugin->activate( false );

	if ( is_callable( array( 'TP', 'go_to_dashboard' ) ) ) {
		call_user_func( array( 'TP', 'go_to_dashboard' ) );
	}

	wp_redirect( admin_url() );
	exit();
}