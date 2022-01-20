<?php
$plugins_required = Thim_Plugins_Manager::get_all_plugins();
?>

<div class="top">
	<h2><?php esc_html_e( 'Install plugins required', 'thim-core' ); ?></h2>

	<form class="thim-table-plugins">
		<table class="wp-list-table widefat plugins thim-plugins">
			<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<input id="cb-select-all" type="checkbox" checked>
				</td>
				<th scope="col" id="name" class="manage-column column-name column-primary"><?php esc_html_e( 'Plugin', 'thim-core' ); ?></th>
				<th scope="col" id="description" class="manage-column column-description"><?php esc_html_e( 'Require', 'thim-core' ); ?></th>
				<th scope="col" id="description" class="manage-column column-status"><?php esc_html_e( 'Status', 'thim-core' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ( $plugins_required as $plugin ):
				$thim_plugin = new Thim_Plugin();
				$thim_plugin->set_args( $plugin );
				$is_active = $thim_plugin->is_active();
				?>
				<tr class="<?php echo esc_attr( $is_active ? 'active' : 'inactive' ); ?>" data-plugin="<?php echo esc_attr( $plugin['slug'] ); ?>">
					<th scope="row" class="check-column">
						<input class="thim-input" type="checkbox" name="<?php echo esc_attr( $plugin['slug'] ); ?>"
						       value="<?php echo esc_attr( $plugin['slug'] ); ?>" <?php checked( false, $is_active ); ?> <?php disabled( true, $is_active ); ?>>
					</th>
					<td class="plugin-title column-primary">
						<?php echo esc_html( $plugin['name'] ); ?>
					</td>
					<td class="column-description desc">
						<span class="info"><?php echo esc_html( $plugin['required'] ? __( 'Required', 'thim-core' ) : __( 'Recommend', 'thim-core' ) ); ?></span>
					</td>
					<td class="column-status">
						<div class="import-php">
							<div class="updating-message"><?php echo esc_html( $thim_plugin->get_text_status() ); ?></div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</form>
</div>

<div class="bottom">
	<button class="button button-primary tc-button tc-run-step" data-request="yes"><?php esc_html_e( 'Install and activate', 'thim-core' ); ?></button>
</div>
