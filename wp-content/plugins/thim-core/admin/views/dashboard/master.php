<?php
global $thim_dashboard;
$theme_data   = $thim_dashboard['theme_data'];
$current_page = Thim_Dashboard::get_current_page_key();
$sub_pages    = Thim_Dashboard::get_sub_pages();
?>

<?php do_action( 'before_thim_dashboard_wrapper' ); ?>

<div class="thim-wrapper">
	<header class="tc-header">
		<div class="title">
			<h1 class="name"><?php echo esc_html( $theme_data['name'] . ' Theme Dashboard' ); ?></h1>
			<span class="version"><?php echo esc_html( $theme_data['version'] ); ?></span>
		</div>

		<nav class="nav-tab-wrapper tc-nav-tab-wrapper">
			<?php foreach ( $sub_pages as $key => $sub_page ):
				$prefix = Thim_Dashboard::$prefix_slug;
				$link = admin_url( 'admin.php?page=' . $prefix . $key );
				$title = $sub_page['title'];
				?>
				<a href="<?php echo esc_url( $link ); ?>" class="nav-tab<?php echo ( $key === $current_page ) ? ' nav-tab-active' : ''; ?>"
				   title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></a>
			<?php endforeach; ?>
		</nav>
	</header>

	<div class="notifications">
		<?php do_action( 'thim_dashboard_notifications', $current_page ); ?>
	</div>

	<div class="tc-main">
		<?php
		do_action( "thim_dashboard_before_page_$current_page" );
		?>

		<?php
		do_action( "thim_dashboard_main_page_$current_page" );
		?>

		<?php
		do_action( "thim_dashboard_after_page_$current_page" );
		?>
	</div>
</div>

<?php do_action( 'after_thim_dashboard_wrapper' ); ?>
