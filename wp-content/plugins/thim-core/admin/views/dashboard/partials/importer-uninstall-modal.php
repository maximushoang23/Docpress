<?php
global $thim_dashboard;
$links = $thim_dashboard['theme_data']['links'];
?>

<div class="tc-modal-importer-uninstall md-modal md-effect-16">
	<div class="md-content">
		<h3 class="title"><?php esc_html_e( 'Uninstall Demo Content', 'thim-core' ); ?><span class="close"></span></h3>
		<div class="main text-center">
			<p>
				<?php esc_html_e( 'If you click "Start", demo content will be deleted. Be careful :)', 'thim-core' ); ?>
			</p>

			<button class="button button-primary tc-button tc-start" title="<?php esc_attr_e( 'Start', 'thim-core' ); ?>"><?php esc_html_e( 'Start', 'thim-core' ); ?></button>
		</div>
	</div>
</div>
<div class="md-overlay"></div>