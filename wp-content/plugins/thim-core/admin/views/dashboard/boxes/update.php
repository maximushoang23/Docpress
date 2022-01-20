<?php
global $thim_dashboard;
$theme_data = $thim_dashboard['theme_data'];
?>

<div class="tc-box-update-wrapper text-center">
	<div class="current-version">
		<strong><?php esc_html_e( 'Current Version:', 'thim-core' ); ?></strong>
		<span><?php echo esc_html( $theme_data['version'] ); ?></span>
	</div>
	<div class="latest-version">
		<strong><?php esc_html_e( 'Latest Version:', 'thim-core' ); ?></strong>
		<span><?php echo esc_html( ( $theme_data['latest_version'] ) ? $theme_data['latest_version'] : $theme_data['version'] ); ?></span>
	</div>

	<button id="tc-check-now" class="button button-secondary tc-button"><?php esc_html_e( 'Check now', 'thim-core' ); ?></button>
	<button id="tc-go-update" class="update button button-primary tc-button" data-href="<?php echo esc_url( Thim_Product_Registration::get_link_go_to_update() ); ?>"
		<?php disabled( false, Thim_Product_Registration::can_update() ); ?>><?php esc_html_e( 'Go to update', 'thim-core' ); ?></button>
</div>