<?php
/**
 * Set Default value when theme option not save at first time setup
 *
 */

if ( is_page_template( 'templates/home-page.php' ) ) {
	$file = thim_template_path();
	include $file;

	return;
} else {
	$file = thim_template_path();
	get_header();
	?>
	<section class="content-area">
		<?php
//		get_template_part( 'templates/page-title/page', 'title' );

		do_action( 'thim_wrapper_loop_start' );
		include $file;
		do_action( 'thim_wrapper_loop_end' );
		?>
	</section>
	<?php
	get_footer();
}
?>