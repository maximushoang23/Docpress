<?php
global $thim_dashboard;
$theme_data = $thim_dashboard['theme_data'];
?>

<div class="tc-dashboard-wrapper wrap">
	<div class="row">

		<div class="col-md-12">

			<div class="tc-box">
				<div class="tc-box-header">
					<h2 class="box-title"><?php esc_html_e( 'WordPress Environment', 'thim-core' ); ?></h2>
				</div>
				<table class="widefat striped" cellspacing="0">
					<tbody>
					<tr>
						<td><?php esc_html_e( 'Home URL:', 'thim-core' ); ?></td>
						<td><?php echo get_home_url(); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Site URL:', 'thim-core' ); ?></td>
						<td><?php echo get_site_url(); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'WP Version:', 'thim-core' ); ?></td>
						<td><?php echo get_bloginfo( 'version' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Theme Name:', 'thim-core' ); ?></td>
						<td><?php echo $theme_data['name']; ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Theme Version:', 'thim-core' ); ?></td>
						<td><?php echo $theme_data['version']; ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'WP Multisite:', 'thim-core' ); ?></td>
						<td>
							<?php
							if ( is_multisite() ) {
								echo 'Yes';
							} else {
								echo 'No';
							}
							?>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'WP Debug Mode:', 'thim-core' ); ?></td>
						<td>
							<?php
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								echo 'Yes';
							} else {
								echo 'No';
							}
							?>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Language:', 'thim-core' ); ?></td>
						<td><?php echo get_bloginfo( 'language' ); ?></td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="tc-box">
				<div class="tc-box-header">
					<h2 class="box-title"><?php esc_html_e( 'Server Environment', 'thim-core' ); ?></h2>
				</div>
				<table class="widefat striped" cellspacing="0">
					<tbody>
					<tr>
						<td><?php esc_html_e( 'Server Info:', 'thim-core' ); ?></td>
						<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'PHP Version:', 'thim-core' ); ?></td>
						<td>
							<?php
							if ( function_exists( 'phpversion' ) ) {
								echo esc_html( phpversion() );
							}
							?>
						</td>
					</tr>
					<?php if ( function_exists( 'ini_get' ) ) : ?>
						<tr>
							<td><?php esc_html_e( 'PHP Post Max Size:', 'thim-core' ); ?></td>
							<td>
								<?php
								$post_max_size       = ini_get( 'post_max_size' );
								$post_max_size_unit  = substr( $post_max_size, - 1 );
								$post_max_size_value = substr( $post_max_size, 0, - 1 );

								switch ( strtoupper( $post_max_size_unit ) ) {
									case 'P':
										$post_max_size_value *= 1024;
									case 'T':
										$post_max_size_value *= 1024;
									case 'G':
										$post_max_size_value *= 1024;
									case 'M':
										$post_max_size_value *= 1024;
									case 'K':
										$post_max_size_value *= 1024;
								}
								echo size_format( $post_max_size_value );
								if ( $post_max_size_value < 67108864 ) {
									echo ' - <mark class="error">' . __( 'We recommend setting post_max_size to at least <strong>64 MB</strong>.', 'thim-core' ) . '</mark>';
								}
								?>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'PHP Memory Limit:', 'thim-core' ); ?></td>
							<td>
								<?php
								$memory_limit       = ( substr( WP_MEMORY_LIMIT, 0, - 1 ) > substr( ini_get( 'memory_limit' ), 0, - 1 ) ) ? WP_MEMORY_LIMIT : ini_get( 'memory_limit' );
								$memory_limit_unit  = substr( $memory_limit, - 1 );
								$memory_limit_value = substr( $memory_limit, 0, - 1 );

								switch ( strtoupper( $memory_limit_unit ) ) {
									case 'P':
										$memory_limit_value *= 1024;
									case 'T':
										$memory_limit_value *= 1024;
									case 'G':
										$memory_limit_value *= 1024;
									case 'M':
										$memory_limit_value *= 1024;
									case 'K':
										$memory_limit_value *= 1024;
								}

								if ( $memory_limit_value < 128000000 ) {
									echo '<mark class="error">' . sprintf( __( '<strong>%s</strong> - We recommend setting memory to at least <strong>128MB</strong>. <br /> To import demo data, <strong>256MB</strong> of memory limit is required. <br /> Please define memory limit in <strong>wp-config.php</strong> file. To learn how, see: <a href="%s" target="_blank">Increasing memory allocated to PHP.</a>', 'thim-core' ), size_format( $memory_limit_value ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
								} else {
									echo size_format( $memory_limit_value );
									if ( $memory_limit_value < 256000000 ) {
										echo ' - <mark class="error">' . __( 'Your current memory limit is sufficient, but if you need to import demo content, the required memory limit is <strong>256MB.</strong>', 'thim-core' ) . '</mark>';
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'PHP Time Limit:', 'thim-core' ); ?></td>
							<td><?php
								$time_limit = ini_get( 'max_execution_time' );

								if ( $time_limit < 180 && $time_limit != 0 ) {
									echo '<mark>' . sprintf( __( '<strong>%s</strong> - We recommend setting max execution time to at least <strong>180</strong>. <br /> To import demo content, <strong>3000</strong> seconds of max execution time is required.<br />See: <a href="%s" target="_blank">Increasing max execution to PHP</a>', 'thim-core' ), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded' ) . '</mark>';
								} else {
									echo $time_limit;
									if ( $time_limit < 300 && $time_limit != 0 ) {
										echo ' - <mark class="error">' . __( 'Current time limit is sufficient, but if you need import demo content, the required time is <strong>300</strong>.', 'thim-core' ) . '</mark>';
									}
								}
								?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<td><?php _e( 'ZipArchive:', 'thim-core' ); ?></td>
						<td><?php echo class_exists( 'ZipArchive' ) ? 'Yes' : '<mark class="error">ZipArchive is not installed on your server, but is required if you need to import demo content.</mark>'; ?></td>
					</tr>
					<tr>
						<td><?php _e( 'MySQL Version:', 'thim-core' ); ?></td>
						<td>
							<?php
							/** @global wpdb $wpdb */
							global $wpdb;
							echo $wpdb->db_version();
							?>
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Max Upload Size:', 'thim-core' ); ?></td>
						<td>
							<?php

							echo size_format( wp_max_upload_size() );
							if ( wp_max_upload_size() < 64000000 ) {
								echo ' - <mark class="error">' . __( 'We recommend setting upload_max_filesize to at least <strong>64 MB</strong>.', 'thim-core' ) . '</mark>';
							}

							?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>


			<?php

			/**
			 * get active plugins
			 */
			$active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				foreach ( get_site_option( 'active_sitewide_plugins', array() ) as $plugin => $id ) {
					$active_plugins[] = $plugin;
				}
				$active_plugins = array_unique( $active_plugins );
			}

			?>
			<div class="tc-box">
				<div class="tc-box-header">
					<h2 class="box-title"><?php esc_html_e( 'Active Plugins', 'thim-core' ); ?> (<?php echo count( $active_plugins ); ?>)</h2>
				</div>
				<table class="widefat striped" cellspacing="0">
					<tbody>
					<?php
					foreach ( $active_plugins as $plugin ) {

						$plugin_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						if ( ! empty( $plugin_data['Name'] ) ) {
							$plugin_url  = ( isset( $plugin_data['PluginURI'] ) && $plugin_data['PluginURI'] != '' ) ? $plugin_data['PluginURI'] : $plugin_data['AuthorURI'];
							$plugin_name = '<a href="' . esc_url( $plugin_url ) . '" title="' . esc_html__( 'Visit plugin homepage', 'thim-core' ) . '">' . $plugin_data['Name'] . '</a>';
							?>
							<tr>
								<td><?php echo $plugin_name; ?></td>
								<td><?php echo $plugin_data['Version']; ?></td>
								<td><?php printf( _x( 'by %s', 'by author', 'thim-core' ), $plugin_data['Author'] ); ?></td>
							</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
			</div>

		</div>

	</div>
</div>