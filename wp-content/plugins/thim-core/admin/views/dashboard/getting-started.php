<?php
$steps       = Thim_Getting_Started::get_steps();
$count_steps = count( $steps );

if ( ! $count_steps ) {
	return;
}
?>

<div class="thim-getting-started">
	<header>
		<ul class="tc-controls" data-max="<?php echo esc_attr( count( $steps ) ); ?>">
			<?php foreach ( $steps as $index => $step ):
				$index ++;
				?>
				<li>
					<a class="step" data-position="<?php echo esc_attr( $index ); ?>" data-step="<?php echo esc_attr( $step['key'] ); ?>"
					   title="<?php echo esc_attr( $step['title'] ); ?> <?php printf( __( '(%1$s of %2$s)', 'thim-core' ), $index, $count_steps ); ?>"></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<a href id="skip-step">Skip</a>
	</header>

	<main>
		<?php
		do_action( 'thim_getting_started_main_content' );
		?>
	</main>
</div>