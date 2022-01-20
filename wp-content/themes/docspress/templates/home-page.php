<?php
/**
 * Template Name: Home Page
 * Template Post Type: post, page

 **/
get_header();
?>

	<div id="home-main-content" class="container site-content" role="main">
		<?php
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		?>
	</div><!-- #home-main-content -->

<?php get_footer(); ?>