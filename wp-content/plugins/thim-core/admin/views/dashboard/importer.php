<?php
$demo_data     = $args['$demo_data'];
$least_value   = $args['$least_value'];
$current_value = $args['$current_value'];
$qualified     = $args['$qualified'];

$demo_installed = Thim_Importer::get_key_demo_installed();
?>

<div class="tc-importer-wrapper">
	<?php if ( ! Thim_Importer::is_qualified() ): ?>
		<div class="requirements">
			<h3><?php esc_html_e( 'Requirements', 'thim-core' ); ?></h3>
			<table>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Directive', 'thim-core' ); ?></th>
					<th><?php esc_html_e( 'Least Suggested Value', 'thim-core' ); ?></th>
					<th><?php esc_html_e( 'Current Value', 'thim-core' ); ?></th>
				</tr>
				</thead>

				<tbody class="directives">
				<tr>
					<td><?php esc_html_e( 'memory_limit', 'thim-core' ); ?></td>
					<td><?php echo esc_html( $least_value['memory_limit'] ); ?></td>
					<td class="bold <?php echo $qualified['memory_limit'] ? 'qualified' : 'unqualified' ?>"><?php echo esc_html( $current_value['memory_limit'] ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'max_execution_time', 'thim-core' ); ?></td>
					<td><?php echo esc_html( $least_value['max_execution_time'] ); ?></td>
					<td class="bold <?php echo $qualified['max_execution_time'] ? 'qualified' : 'unqualified' ?>"><?php echo esc_html( $current_value['max_execution_time'] ); ?></td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<div class="theme-browser rendered">
		<div class="themes wp-clearfix">
			<?php if ( count( $demo_data ) ): ?>
				<?php foreach ( $demo_data as $key => $demo ): ?>
					<div class="theme thim-demo" data-thim-demo="<?php echo esc_attr( $key ); ?>">
						<?php if ( $demo_installed === $key ): ?>
							<span class="status" id="btn-uninstall" data-text="<?php esc_attr_e( 'Uninstall', 'thim-core' ); ?>" data-install="<?php esc_attr_e( 'Installed', 'thim-core' ); ?>"></span>
						<?php endif; ?>

						<div class="theme-screenshot thim-screenshot">
							<img src="<?php echo esc_url( $demo['screenshot'] ); ?>" alt="">
						</div>

						<h2 class="theme-name"><?php echo esc_html( $demo['title'] ); ?></h2>

						<div class="theme-actions">
							<a class="button button-secondary" href="<?php echo esc_url( $demo['demo_url'] ); ?>" target="_blank"><?php esc_html_e( 'Demo', 'thim-core' ); ?></a>
							<button class="button button-primary action-import"><?php esc_html_e( 'Import', 'thim-core' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<h3 class="text-center"><?php esc_html_e( 'No demo content.', 'thim-core' ); ?></h3>
			<?php endif; ?>
		</div>
	</div>
</div>