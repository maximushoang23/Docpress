<?php
/**
 * Header Mobile Menu Template
 *
 * @package Thim_Starter_Theme
 */
?>

<div class="menu-mobile-effect navbar-toggle" data-effect="mobile-effect">
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
</div>

<ul class="nav navbar-nav">
	<?php
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
	?>
</ul>