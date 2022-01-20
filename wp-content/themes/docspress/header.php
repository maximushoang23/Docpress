<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

?><!DOCTYPE html>
<html itemscope itemtype="http://schema.org/WebPage" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-160411161-4"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'UA-160411161-4');
	</script>
</head>
<body <?php body_class(); ?>>

<?php do_action( 'thim_before_body' ); ?>

<div id="wrapper-container" <?php thim_wrapper_container_class(); ?>>

	<?php do_action( 'thim_topbar' ) ?>

	<header id="masthead" class="site-header affix-top<?php thim_header_layout_class(); ?>">
		<?php get_template_part( 'templates/header/' . get_theme_mod( 'header_style', 'default' ) ); ?>
	</header><!-- #masthead -->

	<nav class="visible-xs mobile-menu-container mobile-effect" itemscope itemtype="http://schema.org/SiteNavigationElement">
		<?php get_template_part( 'templates/header/mobile-menu' ); ?>
	</nav><!-- nav.mobile-menu-container -->

	<div id="main-content">