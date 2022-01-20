<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 */
?>
<h2 style="text-align: center" class="vc_custom_heading">Documentation Collection</h2>
<div class="row list-item-docs">

<?php


$mypages = get_pages();
foreach( $mypages as $page ) {
	echo '<div class="item-docs col-sm-4"><div class="inner-item">';
 	$link_demo = get_post_meta($page->ID, 'link_demo_preview', true) ? get_post_meta($page->ID, 'link_demo_preview', true) : '#' ;
	?>
	<h2><a href="<?php echo get_page_link( $page->ID ); ?>"><?php echo $page->post_title; ?></a></h2>
	<a href="<?php echo get_page_link( $page->ID ); ?>">
 	<?php
	if ( get_the_post_thumbnail( $page->ID, 'full' ) ) {
		echo get_the_post_thumbnail( $page->ID, 'full' );
	}else{
		echo '<img src="'.trailingslashit( get_template_directory_uri() ) .'/assets/images/default-preview.jpg">';
	}
 	?>
	</a>
	<p style="text-align: center;"><a class="btn btn-read-more" href="<?php echo get_page_link( $page->ID ); ?>" target="_blank"><i class="vc_btn3-icon fa fa-book"></i> Documentation</a><a class="btn btn_preview" href="<?php echo esc_url($link_demo)?>" target="_blank"><i class="vc_btn3-icon fa fa-shopping-cart"></i> Buy Now</a></p>
	<?php echo '</div></div>';
}
?>

</div>