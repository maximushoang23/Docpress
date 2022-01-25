<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #main-content div and all content after.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?>

</div><!-- #main-content -->

<?php
$hidden_footer = get_post_meta(get_the_ID(), 'thim_hidden_footer_header', true);
if($hidden_footer != '1'){?>
    <footer id="colophon" class="site-footer">
		<?php thim_footer_layout(); ?>
    </footer><!-- #colophon -->
<?php }
?>

</div><!-- content-pusher -->
</div><!-- wrapper-container -->

<?php wp_footer(); ?>

<?php do_action( 'thim_space_body' ); ?>

</body>
</html>
