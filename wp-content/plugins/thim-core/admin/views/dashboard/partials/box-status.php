<?php if ( ! $is_activated ): ?>
	<div class="tc-box-status lock" title="<?php esc_attr_e( 'You must activate the theme to use this feature', 'thim-core' ); ?>">
		<span class="icomoon icon-lock"></span>
	</div>
<?php else: ?>
	<div class="tc-box-status unlock">
		<span class="icomoon icon-unlock"></span>
	</div>
<?php endif; ?>