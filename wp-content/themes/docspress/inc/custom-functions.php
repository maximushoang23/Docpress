<?php
/**
 * Custom Functions
 */

/**
 * Check a plugin active
 *
 * @param $plugin_var
 *
 * @return bool
 */
function thim_plugin_active( $plugin_var ) {
	return in_array( $plugin_var . '/' . $plugin_var . '.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}

/**
 * Get header layouts
 *
 * @return string CLASS for header layouts
 */
function thim_header_layout_class() {
	if ( get_theme_mod( 'header_position', 'default' ) === 'default' ) {
		echo ' header-default';
	} else {
		echo ' header-overlay';
	}

	if ( get_theme_mod( 'show_sticky_menu', true ) ) {
		echo ' sticky-header';
	}

	if ( get_theme_mod( 'sticky_menu_style', 'same' ) === 'custom' ) {
		echo ' custom-sticky';
	} else {
		echo '';
	}

	if ( isset( $thim_options['header_retina_logo'] ) && get_theme_mod( 'header_retina_logo' ) ) {
		echo ' has-retina-logo';
	}
}

/**
 * Get Header Logo
 *
 * @return string
 */
if ( !function_exists( 'thim_header_logo' ) ) {
	function thim_header_logo() {
		$thim_options         = get_theme_mods();
		$thim_logo_src        = THIM_URI . "assets/images/logo.png";
		$thim_mobile_logo_src = THIM_URI . "assets/images/logo.png";
		$thim_retina_logo_src = '';

		if ( isset( $thim_options['header_logo'] ) && $thim_options['header_logo'] <> '' ) {
			$thim_logo_src = get_theme_mod( 'header_logo' );
			if ( is_numeric( $thim_logo_src ) ) {
				$logo_attachment = wp_get_attachment_image_src( $thim_logo_src, 'full' );
				$thim_logo_src   = $logo_attachment[0];
			}
		}

		if ( isset( $thim_options['mobile_logo'] ) && $thim_options['mobile_logo'] <> '' ) {
			$thim_mobile_logo_src = get_theme_mod( 'mobile_logo' );
			if ( is_numeric( $thim_mobile_logo_src ) ) {
				$logo_attachment      = wp_get_attachment_image_src( $thim_mobile_logo_src, 'full' );
				$thim_mobile_logo_src = $logo_attachment[0];
			}
		}

		echo '<a class="no-sticky-logo" href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '" rel="home">';
		echo '<img class="logo" src="' . esc_url( $thim_logo_src ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';

		if ( get_theme_mod( 'header_retina_logo', false ) ) {
			$thim_retina_logo_src = get_theme_mod( 'header_retina_logo' );
			if ( is_numeric( $thim_retina_logo_src ) ) {
				$logo_attachment      = wp_get_attachment_image_src( $thim_retina_logo_src, 'full' );
				$thim_retina_logo_src = $logo_attachment[0];
			}
			echo '<img class="retina-logo" src="' . esc_url( $thim_retina_logo_src ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
		}

		echo '<img class="mobile-logo" src="' . esc_url( $thim_mobile_logo_src ) . '" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" />';
		echo '</a>';
	}
}
add_action( 'thim_header_logo', 'thim_header_logo' );

/**
 * Get Header Sticky logo
 *
 * @return string
 */
if ( !function_exists( 'thim_header_sticky_logo' ) ) {
	function thim_header_sticky_logo() {
		if ( get_theme_mod( 'header_sticky_logo' ) != '' ) {
			$thim_logo_stick_logo     = get_theme_mod( 'header_sticky_logo' );
			$thim_logo_stick_logo_src = $thim_logo_stick_logo; // For the default value
			if ( is_numeric( $thim_logo_stick_logo ) ) {
				$logo_attachment = wp_get_attachment_image_src( $thim_logo_stick_logo, 'full' );
				if ( $logo_attachment ) {
					$thim_logo_stick_logo_src = $logo_attachment[0];
				} else {
					$thim_logo_stick_logo_src = THIM_URI . 'assets/images/sticky-logo.png';
				}
			}
			$thim_logo_size = @getimagesize( $thim_logo_stick_logo_src );
			$logo_size      = $thim_logo_size[3];
			$site_title     = esc_attr( get_bloginfo( 'name', 'display' ) );
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '" rel="home" class="sticky-logo">
					<img src="' . $thim_logo_stick_logo_src . '" alt="' . $site_title . '" ' . $logo_size . ' /></a>';
		} else {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '" rel="home" class="sticky-logo">' . esc_attr( get_bloginfo( 'name' ) ) . '</a>';
		}
	}
}
add_action( 'thim_header_sticky_logo', 'thim_header_sticky_logo' );

/**
 * Get Page Title Content For Single
 *
 * @return string HTML for Page title bar
 */
function thim_get_single_page_title_content() {
	$post_id = get_the_ID();

	if ( get_post_type( $post_id ) == 'post' ) {
		$categories = get_the_category();
	} elseif ( get_post_type( $post_id ) == 'attachment' ) {
		echo '<h2 class="title">' . esc_html__( 'Attachment', 'thim-docspress' ) . '</h2>';

		return;
	} else {// Custom post type
		$categories = get_the_terms( $post_id, 'taxonomy' );
	}
	if ( !empty( $categories ) ) {
		echo '<h2 class="title">' . esc_html( $categories[0]->name ) . '</h2>';
	}
}

/**
 * Get Page Title Content For Date Format
 *
 * @return string HTML for Page title bar
 */
function thim_get_page_title_date() {
	if ( is_year() ) {
		echo '<h2 class="title">' . esc_html__( 'Year', 'thim-docspress' ) . '</h2>';
	} elseif ( is_month() ) {
		echo '<h2 class="title">' . esc_html__( 'Month', 'thim-docspress' ) . '</h2>';
	} elseif ( is_day() ) {
		echo '<h2 class="title">' . esc_html__( 'Day', 'thim-docspress' ) . '</h2>';
	}

	$date  = '';
	$day   = intval( get_query_var( 'day' ) );
	$month = intval( get_query_var( 'monthnum' ) );
	$year  = intval( get_query_var( 'year' ) );
	$m     = get_query_var( 'm' );

	if ( !empty( $m ) ) {
		$year  = intval( substr( $m, 0, 4 ) );
		$month = intval( substr( $m, 4, 2 ) );
		$day   = substr( $m, 6, 2 );

		if ( strlen( $day ) > 1 ) {
			$day = intval( $day );
		} else {
			$day = 0;
		}
	}

	if ( $day > 0 ) {
		$date .= $day . ' ';
	}
	if ( $month > 0 ) {
		global $wp_locale;
		$date .= $wp_locale->get_month( $month ) . ' ';
	}
	$date .= $year;
	echo '<div class="description">' . esc_attr( $date ) . '</div>';
}

/**
 * Get Page Title Content
 *
 * @return string HTML for Page title bar
 */
if ( !function_exists( 'thim_page_title_content' ) ) {
	function thim_page_title_content() {
		if ( is_front_page() ) {// Front page
			echo '<h2 class="title">' . get_bloginfo( 'name' ) . '</h2>';
			echo '<div class="description">' . get_bloginfo( 'description' ) . '</div>';
		} elseif ( is_home() ) {// Post page
			echo '<h2 class="title">' . esc_html__( 'Blog', 'thim-docspress' ) . '</h2>';
			echo '<div class="description">' . get_bloginfo( 'description' ) . '</div>';
		} elseif ( is_page() ) {// Page
			echo '<h2 class="title">' . get_the_title() . '</h2>';
		} elseif ( is_single() ) {// Single
			thim_get_single_page_title_content();
		} elseif ( is_author() ) {// Author
			echo '<h2 class="title">' . esc_html__( 'Author', 'thim-docspress' ) . '</h2>';
			echo '<div class="description">' . get_the_author() . '</div>';
		} elseif ( is_search() ) {// Search
			echo '<h2 class="title">' . esc_html__( 'Search', 'thim-docspress' ) . '</h2>';
			echo '<div class="description">' . get_search_query() . '</div>';
		} elseif ( is_tag() ) {// Tag
			echo '<h2 class="title">' . esc_html__( 'Tag', 'thim-docspress' ) . '</h2>';
			echo '<div class="description">' . single_tag_title( '', false ) . '</div>';
		} elseif ( is_category() ) {// Archive
			echo '<h2 class="title">' . esc_html__( 'Category', 'thim-docspress' ) . '</h2>';
			echo '<div class="description">' . single_cat_title( '', false ) . '</div>';
		} elseif ( is_404() ) {
			echo '<h2 class="title">' . esc_html__( 'Page Not Found!', 'thim-docspress' ) . '</h2>';
		} elseif ( is_date() ) {
			thim_get_page_title_date();
		}
	}
}
add_action( 'thim_page_title_content', 'thim_page_title_content' );

/**
 * Get breadcrumb for page
 *
 * @return string
 */
function thim_get_breadcrumb_items_other() {
	global $author;
	$userdata   = get_userdata( $author );
	$categories = get_the_category();
	if ( is_front_page() ) { // Do not display on the homepage
		return;
	}
	if ( is_home() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_title() ) . '">' . esc_html__( 'Blog', 'thim-docspress' ) . '</span></li>';
	} else if ( is_category() ) { // Category page
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">' . esc_html( $categories[0]->cat_name ) . '</span></li>';
	} else if ( is_tag() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( single_term_title( '', false ) ) . '">' . esc_html( single_term_title( '', false ) ) . '</span></li>';
	} else if ( is_year() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_time( 'Y' ) ) . '">' . esc_html( get_the_time( 'Y' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></li>';
	} else if ( is_author() ) { // Auhor archive
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( $userdata->display_name ) . '">' . esc_attr__( 'Author', 'thim-docspress' ) . ' ' . esc_html( $userdata->display_name ) . '</span></li>';
	} else if ( get_query_var( 'paged' ) ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr__( 'Page', 'thim-docspress' ) . ' ' . get_query_var( 'paged' ) . '">' . esc_html__( 'Page', 'thim-docspress' ) . ' ' . esc_html( get_query_var( 'paged' ) ) . '</span></li>';
	} else if ( is_search() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr__( 'Search results for:', 'thim-docspress' ) . ' ' . esc_attr( get_search_query() ) . '">' . esc_html__( 'Search results for:', 'thim-docspress' ) . ' ' . esc_html( get_search_query() ) . '</span></li>';
	} elseif ( is_404() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr__( '404 Page', 'thim-docspress' ) . '">' . esc_html__( '404 Page', 'thim-docspress' ) . '</span></li>';
	}
}

/**
 * Get content breadcrumbs
 *
 * @return string
 */
if ( !function_exists( 'thim_breadcrumbs' ) ) {
	function thim_breadcrumbs() {
		global $post;
		if ( is_front_page() ) { // Do not display on the homepage
			return;
		}
		$categories   = get_the_category();
		$thim_options = get_theme_mods();
		$icon         = '';
		if ( isset( $thim_options['breadcrumb_icon'] ) ) {
			$icon = html_entity_decode( get_theme_mod( 'breadcrumb_icon' ) );
		}
		// Build the breadcrums
		echo '<ul itemprop="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList" id="breadcrumbs" class="breadcrumbs">';
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( home_url() ) . '" title="' . esc_attr__( 'Home', 'thim-docspress' ) . '"><span itemprop="name">' . esc_html__( 'Home', 'thim-docspress' ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
		if ( is_single() ) { // Single post (Only display the first category)
			if ( isset( $categories[0] ) ) {
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" title="' . esc_attr( $categories[0]->cat_name ) . '"><span itemprop="name">' . esc_html( $categories[0]->cat_name ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
			}
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</span></li>';
		} else if ( is_page() ) {
			// Standard page
			if ( $post->post_parent ) {
				$anc = get_post_ancestors( $post->ID );
				$anc = array_reverse( $anc );
				// Parent page loop
				foreach ( $anc as $ancestor ) {
					echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_permalink( $ancestor ) ) . '" title="' . esc_attr( get_the_title( $ancestor ) ) . '"><span itemprop="name">' . esc_html( get_the_title( $ancestor ) ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
				}
			}
			// Current page
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_title() ) . '"> ' . esc_html( get_the_title() ) . '</span></li>';
		} elseif ( is_day() ) {// Day archive
			// Year link
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '"><span itemprop="name">' . esc_html( get_the_time( 'Y' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
			// Month link
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) ) . '" title="' . esc_attr( get_the_time( 'M' ) ) . '"><span itemprop="name">' . esc_html( get_the_time( 'M' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
			// Day display
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_time( 'jS' ) ) . '"> ' . esc_html( get_the_time( 'jS' ) ) . ' ' . esc_html( get_the_time( 'M' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></li>';

		} else if ( is_month() ) {
			// Year link
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '"><span itemprop="name">' . esc_html( get_the_time( 'Y' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></a><span class="breadcrum-icon">' . ent2ncr( $icon ) . '</span></li>';
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name" title="' . esc_attr( get_the_time( 'M' ) ) . '">' . esc_html( get_the_time( 'M' ) ) . ' ' . esc_html__( 'Archives', 'thim-docspress' ) . '</span></li>';
		}
		thim_get_breadcrumb_items_other();
		echo '</ul>';
	}
}

/**
 * Get list sidebars
 */
if ( !function_exists( 'thim_get_list_sidebar' ) ) {
	function thim_get_list_sidebar() {
		global $wp_registered_sidebars;

		$sidebar_array = array();
		$dp_sidebars   = $wp_registered_sidebars;

		$sidebar_array[''] = esc_attr__( '-- Select Sidebar --', 'thim-docspress' );

		foreach ( $dp_sidebars as $sidebar ) {
			$sidebar_array[$sidebar['name']] = $sidebar['name'];
		}

		return $sidebar_array;
	}
}

/**
 * Turn on and get the back to top
 *
 * @return string HTML for the back to top
 */
if ( !class_exists( 'thim_back_to_top' ) ) {
	function thim_back_to_top() {
		if ( get_theme_mod( 'feature_backtotop', true ) ) {
			?>
			<div id="back-to-top">
				<?php
				get_template_part( 'templates/footer/back-to-top' );
				?>
			</div>
			<?php
		}
	}
}
add_action( 'thim_space_body', 'thim_back_to_top', 10 );

/**
 * Switch footer layout
 *
 * @return string HTML footer layout
 */
if ( !function_exists( 'thim_footer_layout' ) ) {
	function thim_footer_layout() {
		$template_name = 'templates/footer/' . get_theme_mod( 'footer_template', 'default' );
		get_template_part( $template_name );
	}
}

/**
 * Footer Widgets
 *
 * @return bool
 * @return string
 */
if ( !function_exists( 'thim_footer_widgets' ) ) {
	function thim_footer_widgets() {
		if ( get_theme_mod( 'footer_widgets', true ) ) : ?>
			<div class="footer-sidebars">
				<?php
				$col = 12 / get_theme_mod( 'footer_columns', 4 );
				if ( get_theme_mod( 'footer_columns' ) == 5 ) {
					$col = '20';
				}
				for ( $i = 1; $i <= get_theme_mod( 'footer_columns', 4 ); $i ++ ): ?>
					<div class="col-xs-12 col-sm-6 col-md-<?php echo esc_attr( $col ); ?>">
						<?php dynamic_sidebar( 'footer-sidebar-' . $i ); ?>
					</div>
				<?php endfor; ?>
			</div>
		<?php endif;
	}
}


/**
 * Footer Copyright bar
 *
 * @return bool
 * @return string
 */
if ( !function_exists( 'thim_copyright_bar' ) ) {
	function thim_copyright_bar() {
		if ( get_theme_mod( 'copyright_bar', true ) ) : ?>
			<div class="copyright-text">
				<?php
				$copyright_text = get_theme_mod( 'copyright_text', '' );
				echo ent2ncr( $copyright_text );
				?>
			</div>
		<?php endif;
	}
}

/**
 * Footer menu
 *
 * @return bool
 * @return array
 */
if ( !function_exists( 'thim_copyright_menu' ) ) {
	function thim_copyright_menu() {
		if ( get_theme_mod( 'copyright_menu', true ) ) :
			if ( has_nav_menu( 'copyright_menu' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'copyright_menu',
					'container'      => false,
					'items_wrap'     => '<ul id="copyright-menu" class="list-inline">%3$s</ul>',
				) );
			}
		endif;
	}
}

/**
 * Theme Feature: RTL Support.
 *
 * @return @string
 */
if ( !function_exists( 'thim_feature_rtl_support' ) ) {
	function thim_feature_rtl_support() {
		if ( get_theme_mod( 'feature_rtl_support', false ) ) {
			echo " dir=\"rtl\"";
		}
	}

	add_filter( 'language_attributes', 'thim_feature_rtl_support', 10 );
}


/**
 * Theme Feature: Open Graph insert doctype
 *
 * @param $output
 */
if ( !function_exists( 'thim_doctype_opengraph' ) ) {
	function thim_doctype_opengraph( $output ) {
		if ( get_theme_mod( 'feature_open_graph_meta', true ) ) {
			return $output . ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
		}
	}

	add_filter( 'language_attributes', 'thim_doctype_opengraph' );
}

/**
 * Theme Feature: Preload
 *
 * @return bool
 * @return string HTML for preload
 */
if ( !function_exists( 'thim_preloading' ) ) {
	function thim_preloading() {
		$preloading = get_theme_mod( 'theme_feature_preloading', 'off' );
		if ( $preloading != 'off' ) {

			echo '<div id="thim-preloading">';

			switch ( $preloading ) {
				case 'custom-image':
					$preloading_image = get_theme_mod( 'theme_feature_preloading_custom_image', false );
					if ( $preloading_image ) {
						if ( locate_template( 'templates/features/preloading/' . $preloading . '.php' ) ) {
							include locate_template( 'templates/features/preloading/' . $preloading . '.php' );
						}
					}
					break;
				default:
					if ( locate_template( 'templates/features/preloading/' . $preloading . '.php' ) ) {
						include locate_template( 'templates/features/preloading/' . $preloading . '.php' );
					}
					break;
			}

			echo '</div>';

		}
	}

	add_action( 'thim_before_body', 'thim_preloading', 10 );
}

/**
 * Theme Feature: Open Graph meta tag
 *
 * @param string
 */
if ( !function_exists( 'thim_add_opengraph' ) ) {
	function thim_add_opengraph() {
		global $post;

		if ( get_theme_mod( 'feature_open_graph_meta', true ) ) {
			if ( is_single() ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					$img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
					$img_src = esc_attr( $img_src[0] );
				} else {
					$img_src = THIM_URI . 'assets/images/opengraph.png';
				}
				if ( $excerpt = $post->post_excerpt ) {
					$excerpt = strip_tags( $post->post_excerpt );
					$excerpt = str_replace( "", "'", $excerpt );
				} else {
					$excerpt = get_bloginfo( 'description' );
				}
				?>

				<meta property="og:title" content="<?php echo the_title(); ?>" />
				<meta property="og:description" content="<?php echo esc_attr( $excerpt ); ?>" />
				<meta property="og:type" content="article" />
				<meta property="og:url" content="<?php echo the_permalink(); ?>" />
				<meta property="og:site_name" content="<?php echo get_bloginfo(); ?>" />
				<meta property="og:image" content="<?php echo esc_attr( $img_src ); ?>" />

				<?php
			} else {
				return;
			}
		}
	}

	add_action( 'wp_head', 'thim_add_opengraph', 10 );
}


/**
 * Theme Feature: Google theme color
 */
if ( !function_exists( 'thim_google_theme_color' ) ) {
	function thim_google_theme_color() {
		if ( get_theme_mod( 'feature_google_theme', false ) ) { ?>
			<meta name="theme-color" content="<?php echo esc_attr( get_theme_mod( 'feature_google_theme_color', '#333333' ) ) ?>">
			<?php
		}
	}

	add_action( 'wp_head', 'thim_google_theme_color', 10 );
}

/**
 * Responsive: enable or disable responsive
 *
 * @return string
 * @return bool
 */
if ( !function_exists( 'thim_enable_responsive' ) ) {
	function thim_enable_responsive() {
		if ( get_theme_mod( 'enable_responsive', true ) ) {
			echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		}
	}

	add_action( 'wp_head', 'thim_enable_responsive', 1 );
}


/**
 *
 * Display Topbar
 *
 * @return void
 *
 */
if ( !function_exists( 'thim_topbar' ) ) {
	function thim_topbar() {
		$display = get_theme_mod( 'header_topbar_display', true );
		$style   = get_theme_mod( 'header_position', 'default' );
		if ( $display ) {
			echo '<div id="thim-header-topbar" class="style-' . $style . '">';
			get_template_part( 'templates/header/topbar' );
			echo '</div>';
		}
	}

	add_action( 'thim_topbar', 'thim_topbar', 10 );
}


/**
 * Override ajax-loader contact form
 *
 * $return mixed
 */

function thim_wpcf7_ajax_loader() {
	return THIM_URI . 'assets/images/icons/ajax-loader.gif';
}

add_filter( 'wpcf7_ajax_loader', 'thim_wpcf7_ajax_loader' );


/**
 * aq_resize function fake.
 * Aq_Resize
 */
if ( !class_exists( 'Aq_Resize' ) ) {
	function thim_aq_resize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		return $url;
	}
}


/**
 * Get feature image
 *
 * @param int  $width
 * @param int  $height
 * @param bool $link
 *
 * @return string
 */
function thim_feature_image( $width = 1024, $height = 768, $link = true ) {
	global $post;
	if ( has_post_thumbnail() ) {
		if ( $link != true && $link != false ) {
			the_post_thumbnail( $post->ID, $link );
		} else {
			$get_thumbnail = simplexml_load_string( get_the_post_thumbnail( $post->ID, 'full' ) );
			if ( $get_thumbnail ) {
				$thumbnail_src = $get_thumbnail->attributes()->src;
				$img_url       = $thumbnail_src;
				$data          = @getimagesize( $img_url );
				$width_data    = $data[0];
				$height_data   = $data[1];
				if ( $link ) {
					if ( ( $width_data < $width ) || ( $height_data < $height ) ) {
						echo '<div class="thumbnail"><a href="' . esc_url( get_permalink() ) . '" title = "' . get_the_title() . '">';
						echo '<img src="' . $img_url[0] . '" alt= "' . get_the_title() . '" title = "' . get_the_title() . '" />';
						echo '</a></div>';
					} else {
						$image_crop = thim_aq_resize( $img_url[0], $width, $height, true );
						echo '<div class="thumbnail"><a href="' . esc_url( get_permalink() ) . '" title = "' . get_the_title() . '">';
						echo '<img src="' . $image_crop . '" alt= "' . get_the_title() . '" title = "' . get_the_title() . '" />';
						echo '</a></div>';
					}
				} else {
					if ( ( $width_data < $width ) || ( $height_data < $height ) ) {
						return '<img src="' . $img_url[0] . '" alt= "' . get_the_title() . '" title = "' . get_the_title() . '" />';
					} else {
						$image_crop = thim_aq_resize( $img_url[0], $width, $height, true );

						return '<img src="' . $image_crop . '" alt= "' . get_the_title() . '" title = "' . get_the_title() . '" />';
					}
				}
			}
		}
	}
}

/**
 * Add document title before doc menu
 *
 * @param $id
 *
 * @return string
 */
function thim_before_document_menu( $id ) {
	return '<h1 class="entry-title">' . get_the_title( $id ) . '</h1>';
}

add_filter( 'before_document_menu', 'thim_before_document_menu' );

function thim_add_documentor_meta( $meta_box, $prefix ) {
	$meta_box['fields'][] = array(
		'name'    => __( 'Menu Header', 'docspress' ),
		'desc'    => __( 'Select menu to show on header each Documentor', 'docspress' ),
		'id'      => $prefix . 'custom_menu_header',
		'type'    => 'select',
		'tab'  => 'title',
		'options' => thim_get_all_nav_menus(),
	);
	$meta_box['fields'][] = array(
		'name'    => __( 'Hidden Header and Footer', 'docspress' ),
 		'id'      => $prefix . 'hidden_footer_header',
		'type' => 'checkbox',
		'std'  => false,
		'tab'  => 'title',
 	);
	return $meta_box;
}

add_filter( 'thim_metabox_display_settings', 'thim_add_documentor_meta', 1000, 2 );

function thim_get_all_nav_menus() {
	$menus = wp_get_nav_menus();

	$list_menu = array( '' => __( '-- Select Menu --', 'docspress' ) );
	if ( !empty( $menus ) ) {
		foreach ( $menus as $key => $value ) {
			$list_menu[$value->term_id] = $value->name;
		}
	}

	return $list_menu;
}