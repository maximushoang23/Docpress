<?php
global $thim_dashboard;
$links = $thim_dashboard['theme_data']['links'];
?>
<div class="tc-documentation-wrapper">
	<div class="row">
		<div class="col-md-4 box">
			<h3><span class="icomoon icon-library"></span><?php esc_html_e( 'Knowledge base', 'thim-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Suddenly there\'s a problem with your theme and you don\'t know why and how to fix it? Please check this knowledge base before contacting the Forum support.
You can find detailed answers to almost all common issues regarding all themes and plugins usage here.', 'thim-core' ); ?></p>
			<a href="<?php echo esc_url( $links['knowledge'] ); ?>" class="button button-primary tc-button" target="_blank"><?php esc_html_e( 'Knowledge Base', 'thim-core' ); ?></a>
		</div>
		<div class="col-md-4 box">
			<h3><span class="icomoon icon-book2"></span><?php esc_html_e( 'Theme Documentation', 'thim-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Installing and customizing WordPress themes alone for the first time is never a good idea. Let us help you with the detail instructions from our Documentation system.
We also included Step-by-Step guides so that you can work effectively with the theme.', 'thim-core' ); ?></p>
			<a href="<?php echo esc_url( $links['docs'] ); ?>" class="button button-primary tc-button" target="_blank"><?php esc_html_e( 'Documentation', 'thim-core' ); ?></a>
		</div>
		<div class="col-md-4 box">
			<h3><span class="icomoon icon-bubbles"></span><?php esc_html_e( 'Forum support', 'thim-core' ); ?></h3>
			<p class="description"><?php esc_html_e( 'With the purpose of giving you the highest quality of support, we have created a forum support where we can discuss more about the issues you are having.
If any problems arise with the theme, please go to our forum support with the link below and create a topics so that our supporters can help you out.', 'thim-core' ); ?></p>
			<a href="<?php echo esc_url( $links['support'] ); ?>" class="button button-primary tc-button" target="_blank"><?php esc_html_e( 'Support', 'thim-core' ); ?></a>
		</div>
	</div>
</div>