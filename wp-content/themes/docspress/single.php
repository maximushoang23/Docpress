<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
?>
    <div class="page-content">
		<?php
		while ( have_posts() ) : the_post();
			$sections = get_post_meta( get_the_ID(), 'thim_items_docs', true );
 			if ( isset($sections) && $sections[0]['title'] == '' ) {
				the_content();
			}
		endwhile; // end of the loop. ?>
    </div>

<?php
if ( isset( $sections ) && ! empty( $sections ) && count( $sections ) > 0 ) {
	?>
    <div id="wrapper" class="documentor-wrap">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="doc-menu sticky-sidebar">
            <div class="theiaStickySidebar">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <ul class="sidebar-nav doc-list-front">
					<?php
					while ( have_posts() ) : the_post();
						$sections = get_post_meta( get_the_ID(), 'thim_items_docs', true );
						$i        = 1;

						foreach ( $sections as $section ) {
							if ( isset($section['sub_section']) && $section['sub_section'] == true ) {
								$class = ' sub-section';
							} else {
								$class = '';
							}
							if(isset($section['title'])){
								echo '<li class="doc-actli' . $class . '"><a href="#sections-' . $i . '">' . $section['title'] . '</a></li>';
							}
							$i ++;
						}
					endwhile; // End of the loop.
					?>
                </ul>
            </div>

        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" class="doc-sec-container no-padding">
			<?php
			while ( have_posts() ) : the_post();
				$sections = get_post_meta( get_the_ID(), 'thim_items_docs', true );
				$i        = 1;
				foreach ( $sections as $section ) {
					echo '<div id="sections-' . $i . '" class="documentor-section">';
					echo '<h3 class="doc-sec-title">' . $section['title'] . '</h3>';
					if ( isset($section['type']) && $section['type'] == 'text' ) {
						echo wpautop( $section['text'] );
					} else {
						$post = isset($section['page']) ? get_post( $section['page'] ) : '';
						echo apply_filters( 'the_content', $post->post_content );
					}
					echo '</div>';
					$i ++;
				}
			endwhile; // End of the loop.
			?>
        </div>
        <div class="clear"></div>
    </div>
<?php } ?>