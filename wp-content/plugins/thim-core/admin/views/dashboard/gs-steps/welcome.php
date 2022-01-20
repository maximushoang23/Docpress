<?php
$theme_metadata = Thim_Theme_Manager::get_metadata();
?>

<div class="top">
	<div class="row">
		<div class="col-md-6">
			<h2>Welcome to <?php echo esc_html( $theme_metadata['name'] ); ?></h2>

			<p class="cation">
				<?php echo esc_html( $theme_metadata['description'] ); ?>
			</p>

			<p>
				The following short steps are designed to show you how <strong><?php echo esc_html( $theme_metadata['name'] ); ?></strong> works so that you can start creating amazing layouts.
			</p>

			<p>Enjoy the ride!</p>
		</div>
		<div class="col-md-6">
			<iframe width="100%" height="315" src="https://www.youtube.com/embed/z0aYu1AZHpE" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>
</div>

<div class="bottom">
	<button class="button button-primary tc-button tc-run-step">Next step →</button>
</div>