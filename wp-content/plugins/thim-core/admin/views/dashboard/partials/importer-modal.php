<?php
global $thim_dashboard;
$links = $thim_dashboard['theme_data']['links'];
?>

<div class="tc-modal-importer md-modal md-effect-16">
	<div class="md-content">
		<h3 class="title"><?php esc_html_e( 'Import Demo', 'thim-core' ); ?> <span class="demo-name"></span><span class="close"></span></h3>
		<div class="main">
			<form id="form-importer">
				<div class="pre-import">
					<h4><?php esc_html_e( 'Pre-import', 'thim-core' ); ?></h4>
					<ul class="options">
						<li class="package plugins obligatory" data-package="plugins">
							<label>
								<input type="checkbox" id="importer-plugins" checked="checked" disabled="disabled">
							</label>
							<div class="heading"><?php esc_html_e( 'Required Plugins', 'thim-core' ); ?></div>
							<div class="description"><?php esc_html_e( 'This will install and active plugins required and it is obligatory.', 'thim-core' ); ?></div>
							<div><?php esc_html_e( 'Plugins required:', 'thim-core' ); ?> <span class="plugins-required"></span></div>
							<span class="package-progress-bar"></span>
						</li>
					</ul>
				</div>

				<div class="import-content">
					<h4><?php esc_html_e( 'Select what type of content you want to import', 'thim-core' ); ?></h4>

					<?php
					$packages = Thim_Importer::get_import_packages();
					if ( count( $packages ) ) :
						?>
						<ul class="options">
							<?php foreach ( $packages as $key => $package ): ?>
								<li class="package <?php echo esc_attr( $key ); ?>"
								    data-required="<?php echo esc_attr( isset( $package['required'] ) ? $package['required'] : '' ); ?>"
								    data-package="<?php echo esc_attr( $key ); ?>">
									<label>
										<input type="checkbox" id="importer-<?php echo esc_attr( $key ); ?>" checked="checked">
									</label>
									<div class="heading"><?php echo esc_html( $package['title'] ); ?></div>
									<div class="description"><?php echo esc_html( $package['description'] ); ?></div>
									<span class="package-progress-bar"></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<div class="footer">
			<button class="button button-primary tc-button" id="start-import" data-text="<?php esc_attr_e( 'Import', 'thim-core' ); ?>"
			        data-importing="<?php esc_attr_e( 'Importing', 'thim-core' ); ?>" data-completed="<?php esc_attr_e( 'Completed', 'thim-core' ); ?>"></button>

			<div class="text-waiting">Enjoy a cup of coffee while you wait for importing :)</div>
		</div>

		<div class="wrapper-finish">
			<div class="full-box">
				<div class="middle notification text-center">
					<span class="icon"></span>
					<div class="details-error">
						<h3></h3>
						<div class="how-to"></div>
						<div class="get-support">
							<a target="_blank" href="<?php echo esc_url( $links['docs'] ); ?>"
							   class="button button-secondary tc-button"><?php esc_html_e( 'Documentation', 'thim-core' ); ?></a>
							<a target="_blank" href="<?php echo esc_url( $links['support'] ); ?>"
							   class="button button-primary tc-button" title="<?php esc_attr_e( 'Get support', 'thim-core' ); ?>"><?php esc_html_e( 'Get support', 'thim-core' ); ?></a>
							<?php echo __( 'with <code>CODE: <span class="error-code"></span></code>', 'thim-core' ); ?>
						</div>
					</div>
					<div class="details-success">
						<h3><?php esc_html_e( 'Hooray! All Done.', 'thim-core' ); ?></h3>
						<p><?php printf( __( 'View <a href="%1$s" target="_blank">your site</a> or return to <a href="%2$s">dashboard</a>.', 'thim-core' ), home_url( '/' ), Thim_Dashboard::get_link_main_dashboard() ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="md-overlay"></div>