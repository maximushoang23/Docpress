<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 */
?>

<section class="error-404 not-found">
	<header class="page-header">
		<h2 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'thim-docspress' ); ?></h2>
	</header><!-- .page-header -->

	<div class="page-content">
		<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'thim-docspress' ); ?></p>

		<?php
		get_search_form();
		?>

	</div><!-- .page-content -->
</section><!-- .error-404 -->
