<?php
$plugins  = Thim_Plugins_Manager::get_all_plugins();
$writable = Thim_Plugins_Manager::get_permission();
?>
<div class="wrap plugin-tab">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Filter plugins list', 'thim-core' ); ?></h2>
	<div class="wp-filter">
		<ul class="filter-links">
			<li class="all" data-filter="*"><a href="#all" class="current"><?php esc_html_e( 'All', 'thim-core' ); ?></a></li>
			<li class="required" data-filter=".required"><a href="#required" class=""><?php esc_html_e( 'Required', 'thim-core' ); ?></a></li>
			<li class="recommended" data-filter=".recommended"><a href="#recommended" class=""><?php esc_html_e( 'Recommended', 'thim-core' ); ?></a></li>
		</ul>

		<form class="search-form search-plugins" method="get">
			<input type="hidden" name="tab" value="search">
			<label><span class="screen-reader-text"><?php esc_html_e( 'Search Plugins', 'thim-core' ); ?></span>
				<input type="search" name="s" value="" class="wp-filter-search" placeholder="<?php esc_attr_e( 'Search Plugins', 'thim-core' ); ?>"
				       aria-describedby="live-search-desc">
			</label>
			<input type="submit" id="search-submit" class="button hide-if-js" value="<?php esc_attr_e( 'Search Plugins', 'thim-core' ); ?>"></form>
	</div>
	<br class="clear">

	<form id="plugin-filter" method="post">
		<div class="list-plugins">
			<?php foreach ( $plugins as $plugin_data ):
				$slug = $plugin_data['slug'];
				$plugin_classes = '';

				$plugin_classes .= $plugin_data['required'] ? 'required' : 'recommended';
				$plugin_classes .= ' plugin-card-' . $slug;

				$plugin = new Thim_Plugin();
				$plugin->set_args( $plugin_data );
				$status      = $plugin->get_status();
				$is_wporg    = $plugin->is_wporg();
				$plugin_info = $plugin->get_info();

				$plugin_icon = THIM_CORE_ADMIN_URI . '/assets/images/logo.svg';
				if ( $plugin->get_icon() ) {
					$plugin_icon = $plugin->get_icon();
				} elseif ( $is_wporg ) {
					$plugin_icon = 'https://ps.w.org/' . $plugin->get_slug() . '/assets/icon-128x128.png';
				}

				?>
				<div class="plugin-card <?php esc_attr_e( $plugin_classes ); ?>" id="<?php echo esc_attr( $slug ); ?>">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<?php echo $plugin_data['name']; ?>
								<div class="thickbox open-plugin-details-modal">
									<img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon" alt="<?php echo esc_attr( $plugin_data['name'] ); ?>">
								</div>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons" data-slug="<?php echo esc_attr( $plugin->get_slug() ); ?>">
								<li>
									<?php if ( $status == 'not_installed' ): ?>
										<button type="button" class="button" data-action="install" <?php disabled( $writable, false ); ?> ><?php esc_html_e( 'Install Now', 'thim-core' ); ?></button>
									<?php elseif ( $status == 'inactive' ): ?>
										<button type="button" class="button" data-action="activate"><?php esc_html_e( 'Activate', 'thim-core' ); ?></button>
									<?php else: ?>
										<button type="button" class="button" data-action="deactivate"><?php esc_html_e( 'Deactivate', 'thim-core' ); ?></button>
									<?php endif; ?>
								</li>
							</ul>
						</div>
						<?php if ( $plugin_info ): ?>
							<div class="desc column-description">
								<p><?php echo $plugin_info['Description']; ?></p>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $plugin_info ): ?>
						<div class="plugin-card-bottom">
							<div class="column-downloaded"><?php esc_html_e( 'Version: ', 'thim-core' ); ?><?php echo $plugin_info['Version']; ?></div>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</form>

	<span class="spinner"></span>
</div>