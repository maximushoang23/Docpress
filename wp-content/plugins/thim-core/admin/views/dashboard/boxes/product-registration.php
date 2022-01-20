<?php
global $thim_dashboard;
$theme_data    = $thim_dashboard['theme_data'];
$theme_name    = $theme_data['name'];
$purchase_link = $theme_data['purchase_link'];
?>

<div class="tc-registration-wrapper text-center">
	<div class="tc-steps">
		<span class="icomoon icon-lock"></span>
		<span class="icomoon icon-chevron-right next"></span>
		<span class="icomoon icon-mouse-left"></span>
		<span class="icomoon icon-chevron-right next"></span>
		<span class="icomoon icon-unlock"></span>
	</div>

	<h3><?php esc_html_e( 'You\'re almost finished!', 'thim-core' ); ?></h3>
	<!--
	<form action="<?php echo esc_url( Thim_Product_Registration::get_url_auth() ); ?>" method="post">
		<input type="hidden" name="theme" value="<?php echo esc_attr( $theme_name ); ?>">
		<input type="hidden" name="site" value="<?php echo esc_attr( home_url( '/' ) ); ?>">
		<input type="hidden" name="return_url" value="<?php echo esc_url( Thim_Product_Registration::get_url_verify_callback() ); ?>">
		<button class="button button-primary tc-button activate-btn"><?php esc_html_e( 'Activate', 'thim-core' ); ?></button>
	</form>
-->
	<form action="<?php echo esc_url( Thim_Dashboard::get_link_main_dashboard( array( 'thim-activate-theme' => 1 ) ) ); ?>" method="post">
<!--		<div class="or">--><?php //esc_html_e( 'or', 'thim-core' ); ?><!--</div>-->
		<input type="hidden" name="theme" value="<?php echo esc_attr( $theme_name ); ?>">
		<input type="text" name="token" id="input-token" class="widefat" placeholder="<?php esc_attr_e( 'Enter personal token and press Enter', 'thim-core' ); ?>">
	</form>
	<p class="guide"><?php printf( __( 'You can create <a href="%s" target="_blank">personal token</a> at here.' ), esc_url( 'https://build.envato.com/create-token/?purchase:download=t&purchase:verify=t&purchase:list=t' ) ); ?></p>
</div>