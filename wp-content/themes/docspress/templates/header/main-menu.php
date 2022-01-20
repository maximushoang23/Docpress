<?php
/**
 * Header Main Menu Template
 *
 */
?>

<?php if ( class_exists( 'Thim_Mega_Menu' ) && Thim_Mega_Menu::menu_is_enabled( 'primary' ) ) : ?>
	<div class="mega-menu-wrapper">
		<?php wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s'
			)
		);
		?>
	</div>
<?php else: ?>

	<ul id="primary-menu" class="navbar">
		<?php
		$using_custom_heading = false;

		if ( is_singular() ) {
			$using_custom_heading = get_post_meta( get_the_ID(), 'thim_custom_menu_header', true );

			if ( !empty( $using_custom_heading ) ) {
				wp_nav_menu( array(
					'menu_id'    => $using_custom_heading,
					'container'  => false,
					'items_wrap' => '%3$s'
				) );
			}
		}

		if ( empty( $using_custom_heading ) ) {
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s'
				) );
			} else {
				wp_nav_menu( array(
					'theme_location' => '',
					'container'      => false,
					'items_wrap'     => '%3$s'
				) );
			}
		}

		?>
	</ul>
<?php endif; ?>