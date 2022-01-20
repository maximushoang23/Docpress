<?php
global $thim_dashboard;

$is_activated   = Thim_Product_Registration::is_active();
$theme_data     = $thim_dashboard['theme_data'];
$changelog_file = $theme_data['changelog_file'];
$network_active = TP::is_active_network();
?>

<div class="tc-dashboard-wrapper wrap">
	<div class="row">

		<div class="col-md-6">
			<?php if ( ! $is_activated ): ?>
				<div class="tc-box">
					<div class="tc-box-header">
						<h2 class="box-title"><?php esc_html_e( 'Product Registration', 'thim-core' ); ?></h2>
					</div>

					<div class="tc-box-body">
						<?php if ( ! $network_active ): ?>
							<div><?php printf( __( 'You need to <a href="%s">network activate</a> Thim Core Plugin to use this feature.', 'thim-core' ), network_admin_url( 'plugins.php' ) ); ?></div>
						<?php else: ?>
							<?php include 'boxes/product-registration.php'; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="tc-box">
					<div class="tc-box-header">
						<?php include 'partials/box-status.php'; ?>
						<h2 class="box-title"><?php esc_html_e( 'Update', 'thim-core' ); ?></h2>
					</div>
					<div class="tc-box-body">
						<?php include 'boxes/update.php' ?>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<div class="col-md-6">
			<div class="tc-box">
				<div class="tc-box-header">
					<h2 class="box-title"><?php esc_html_e( 'Appearance', 'thim-core' ) ?></h2>
				</div>
				<div class="tc-box-body">
					<?php include 'boxes/customize.php'; ?>
				</div>
			</div>

			<?php if ( false ): ?>
				<div class="tc-box">
					<div class="tc-box-header">
						<?php include 'partials/box-status.php'; ?>
						<h2 class="box-title"><?php esc_html_e( 'Support', 'thim-core' ); ?></h2>
					</div>
					<div class="tc-box-body">
						<?php include 'boxes/support.php'; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<div class="col-md-12">
			<div class="tc-box">
				<div class="tc-box-header">
					<h2 class="box-title"><?php esc_html_e( 'Documentation', 'thim-core' ); ?></h2>
				</div>
				<div class="tc-box-body">
					<?php include 'boxes/documentation.php'; ?>
				</div>
			</div>
		</div>

		<?php if ( $changelog_file ): ?>
			<div class="col-md-12">
				<div class="tc-box">
					<div class="tc-box-header">
						<h2 class="box-title"><?php esc_html_e( 'Changelog', 'thim-core' ); ?></h2>
					</div>
					<div class="tc-box-body">
						<div class="tc-changelog-wrapper">
							<div class="versions">
								<?php include $changelog_file; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>