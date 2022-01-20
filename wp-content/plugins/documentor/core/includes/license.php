<?php
function documentor_license() {
	$license 	= get_option( 'documentor_license_key' );
	$status 	= get_option( 'documentor_license_status' );
	?>
	<div class="wrap">
		<h2><?php _e('Documentor License Options'); ?></h2>
		<form method="post" action="options.php">
		
			<?php settings_fields('documentor_license'); ?>
			
			<table class="form-table">
				<tbody>
					<tr valign="top">	
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input id="documentor_license_key" name="documentor_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="documentor_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php _e('License Status'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<span style="display:inline-block;vertical-align:middle;margin-right:10px;color:green;font-weight: bold;line-height: 30px;"><?php _e('Active'); ?></span>
									<?php wp_nonce_field( 'documentor_nonce', 'documentor_nonce' ); ?>
									<input type="submit" class="button-secondary" name="documentor_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
								<?php } else {
									wp_nonce_field( 'documentor_nonce', 'documentor_nonce' ); ?>
									<input type="submit" class="button-secondary" name="documentor_license_activate" value="<?php _e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>	
			<?php submit_button(); ?>
		
		</form>
	<?php
}

function documentor_license_register_option() {
	register_setting('documentor_license', 'documentor_license_key', 'documentor_sanitize_license' );
}
add_action('admin_init', 'documentor_license_register_option');

function documentor_sanitize_license( $new ) {
	$old = get_option( 'documentor_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'documentor_license_status' ); 
	}
	return $new;
}

function documentor_activate_license() {
	if( isset( $_POST['documentor_license_activate'] ) ) {

	 	if( ! check_admin_referer( 'documentor_nonce', 'documentor_nonce' ) ) 	
			return; 

		$license = trim( get_option( 'documentor_license_key' ) );

		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( DOCUMENTOR_ITEM_NAME ),
			'url'       => home_url()
		);
		
		$response = wp_remote_get( add_query_arg( $api_params, DOCUMENTOR_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'documentor_license_status', $license_data->license );

	}
}
add_action('admin_init', 'documentor_activate_license');

function documentor_deactivate_license() {
	if( isset( $_POST['documentor_license_deactivate'] ) ) {

	 	if( ! check_admin_referer( 'documentor_nonce', 'documentor_nonce' ) ) 	
			return; 

		$license = trim( get_option( 'documentor_license_key' ) );

		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( DOCUMENTOR_ITEM_NAME ), 
			'url'       => home_url()
		);

		$response = wp_remote_get( add_query_arg( $api_params, DOCUMENTOR_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license == 'deactivated' )
			delete_option( 'documentor_license_status' );

	}
}
add_action('admin_init', 'documentor_deactivate_license');

function documentor_check_license() {

	global $wp_version;

	$license = trim( get_option( 'documentor_license_key' ) );
		
	$api_params = array( 
		'edd_action' => 'check_license', 
		'license' => $license, 
		'item_name' => urlencode( DOCUMENTOR_ITEM_NAME ),
		'url'       => home_url()
	);

	$response = wp_remote_get( add_query_arg( $api_params, DOCUMENTOR_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );


	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
	} else {
		echo 'invalid'; exit;
	}
}
