<?php // Hook for adding admin menus
if( !class_exists( 'DocumentorAdmin' ) ) {
	class DocumentorAdmin extends Documentor {
		function __construct() {
			if ( is_admin() ) { // admin actions
				add_action('admin_menu', array(&$this, 'documentor_admin_menu'));
				add_action('admin_init', array(&$this, 'documentor_admin_resources'));
				add_action( 'admin_init', array( &$this, 'register_global_settings' ) );
				//hook for updating custom fields
				add_action( 'publish_post', array( &$this, 'update_custom_fields' ) );
				add_action( 'publish_page', array( &$this, 'update_custom_fields' ) );
				add_action( 'edit_post', array( &$this, 'update_custom_fields' ) );
				add_action( 'edit_attachment', array( &$this, 'update_custom_fields' ) ); 
				//delete section when post is deleted
				add_action( 'wp_trash_post', array( &$this, 'doc_delete_section' ) );
				add_filter( 'plugin_action_links',  array( &$this,'documentor_action_links'), 10, 2 );
				//add css in admin header
				add_action( 'admin_head', array( &$this,'admin_css') );

			}
		}
		function admin_css(){ ?>
		     <style>
			     #menu-posts-documentor-sections {
				  display: none !important;
			     }
		     </style>
		<?php
		}
		function documentor_action_links( $links, $file ) {
			if ( $file != DOCUMENTOR_PLUGIN_BASENAME )
				return $links;
			
			$url = Documentor::documentor_admin_url(array('page'=>'documentor-admin'));

			$manage_link = '<a href="' . esc_attr( $url ) . '">'
				. esc_html( __( 'Manage','documentor') ) . '</a>';

			array_unshift( $links, $manage_link );

			return $links;
		}
		// function for adding guides page to wp-admin
		function documentor_admin_menu() {
			// Add a new submenu under Options
			// Documentor 1.3.3 start
			$documentor_global_curr = get_option('documentor_global_options');
			
			$user_level= (isset($documentor_global_curr['user_level'])?$documentor_global_curr['user_level']:'publish_posts');			  
			//print_r($user_level);
			add_menu_page( __('Documentor','documentor'), __('Documentor','documentor'), $user_level,'documentor-admin&action=create-new', array(&$this, 'documentor_guides_page'), Documentor::documentor_plugin_url( 'core/images/logo.png'));
			//add_submenu_page( 'documentor-admin', '', '', 'manage_options','documentor-admin', array(&$this, 'documentor_guides_page'));
			add_submenu_page( 'documentor-admin&action=create-new', __('Create New','documentor'), __('Create New','documentor'),$user_level ,'documentor-admin&action=create-new', array(&$this, 'documentor_guides_page'));
			add_submenu_page( 'documentor-admin&action=create-new', __('Manage Guides','documentor'), __('Manage','documentor'), $user_level,'documentor-admin', array(&$this, 'documentor_guides_page'));
			add_submenu_page( 'documentor-admin&action=create-new', __('Global Settings','documentor'), __('Global Settings','documentor'), 'manage_options','documentor-global-settings', array(&$this, 'documentor_global_settings'));
			add_submenu_page( 'documentor-admin&action=create-new', __('Documentor - License','documentor'), __('License','documentor'), 'manage_options','documentor-license-key', 'documentor_license');
			
			// Documentor 1.3.3 end
			if( function_exists( 'add_meta_box' ) && function_exists('icl_plugin_action_links') ) {
				$post_types = get_post_types(); 
				foreach($post_types as $post_type) {
					add_meta_box( 'documentor_box', __( 'Documentor' , 'documentor'), array(&$this, 'documentor_custom_box'), $post_type, 'advanced' );
				}
			}
			
		}	
		//update custom fields
		function update_custom_fields( $post_id ) {
			//menu title
			if( isset( $_POST['_documentor_menutitle'] ) ) {
				$documentor_menutitle = get_post_meta( $post_id, '_documentor_menutitle', true );
				$post_documentor_menutitle = $_POST['_documentor_menutitle'];
				if( $documentor_menutitle != $post_documentor_menutitle ) {
					update_post_meta( $post_id, '_documentor_menutitle', $post_documentor_menutitle );	
				}
			}
			//section title
			if( isset( $_POST['_documentor_sectiontitle'] ) ) {
				$documentor_sectiontitle = get_post_meta( $post_id, '_documentor_sectiontitle', true );
				$post_documentor_sectiontitle = $_POST['_documentor_sectiontitle'];
				if( $documentor_sectiontitle != $post_documentor_sectiontitle ) {
					update_post_meta( $post_id, '_documentor_sectiontitle', $post_documentor_sectiontitle );	
				}
			}
			//attach WooCommerce product to document
			if( isset( $_POST['documentor_attachid'] ) ) {
				$documentor_attachid = get_post_meta( $post_id, '_documentor_attachid', true );
				$post_documentor_attachid = $_POST['documentor_attachid'];
				if( $documentor_attachid != $post_documentor_attachid ) {
					update_post_meta( $post_id, '_documentor_attachid', $post_documentor_attachid );	
				}
			}
		}
		//add metabox callback function
		function documentor_custom_box() {
			global $post;
			$post_id = $post->ID;
			$documentor_menutitle = get_post_meta($post_id, '_documentor_menutitle', true);
			$documentor_sectiontitle = get_post_meta($post_id, '_documentor_sectiontitle', true);
			$documentor_attachid = get_post_meta($post_id, '_documentor_attachid', true);	
			$post_type = get_post_type($post_id);			
		?>
			<table class="form-table" style="margin: 0;">
				<tr valign="top">
					<td scope="row">
						<label for="documentor_menutitle"><?php _e('Menu Title ','documentor'); ?></label>
					</td>
					<td>
						<input type="text" name="_documentor_menutitle" class="documentor_menutitle" value="<?php echo esc_attr($documentor_menutitle);?>" size="50" />
					</td>
				</tr>
				<tr valign="top">
					<td scope="row">
						<label for="documentor_sectiontitle"><?php _e('Section Title ','documentor'); ?></label>
					</td>
					<td>
						<input type="text" name="_documentor_sectiontitle" class="documentor_sectiontitle" value="<?php echo esc_attr($documentor_sectiontitle);?>" size="50" />
					</td>
				</tr>
				<?php
				if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && $post_type == 'product' ) { 
					$gtitle = '';
					if( !empty( $documentor_attachid ) ) {
						$documentor_attachid = intval($documentor_attachid);
						$guide = new DocumentorGuide( $documentor_attachid );
						$gtitle = $guide->title;
					}
				?>
					<tr valign="top">
						<td scope="row">
							<label for="documentor_prodattach"><?php _e('Attach Guide ','documentor'); ?></label>
						</td>
						<td>
							<input type="text" name="documentor_prodattach" id="documentor_prodattach" size="50" placeholder="<?php _e('Type Guide Name','documentor');?>" value="<?php echo $gtitle;?>" />
							<input type="hidden" name="documentor_attachid" class="documentor-attachid" value="<?php echo $documentor_attachid;?>" />
						</td>
					</tr>
					<script type="text/javascript">
						jQuery( document ).ready( function() {
							//autocomplete for WooCommerce product and Guide attachment
							if( jQuery( "#documentor_prodattach" ).length > 0 ) {
								jQuery( "#documentor_prodattach" ).autocomplete({
									source: function(req, response){
										jQuery.getJSON(ajaxurl+'?callback=?&action=doc_suggest_guides_toattach', req, response);
									},
									select: function(event, ui) {
										jQuery("#documentor_box .documentor-attachid").val(ui.item.id);
									},
									delay: 500,
									minLength: 3
								}); 
							}
						});
					</script>
				<?php
				} ?>
			</table>
		<?php }
		function documentor_admin_resources() {
			if ( isset($_GET['page']) && ( $_GET['page'] == 'documentor-admin' || $_GET['page'] == 'documentor-global-settings' ) ) {
				wp_register_script('jquery', false, false, false, false);
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-autocomplete' ); //autocomplete
				if ( ! did_action( 'wp_enqueue_media' ) ) wp_enqueue_media();
				wp_enqueue_script( 'jquery-nestable', Documentor::documentor_plugin_url( 'core/js/jquery.nestable.js' ), array('jquery'), DOCUMENTOR_VER, false);
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'dataTableCSS', Documentor::documentor_plugin_url( 'core/css/jquery.dataTables.min.css' ), false, DOCUMENTOR_VER, 'all');
				
				wp_enqueue_script( 'dataTableJS', Documentor::documentor_plugin_url( 'core/js/jquery.dataTables.min.js' ),	array('jquery'), DOCUMENTOR_VER, false);
				
				wp_enqueue_style( 'documentor-admin-css', Documentor::documentor_plugin_url( 'core/css/admin.css' ), false, DOCUMENTOR_VER, 'all');
					
				wp_enqueue_script( 'documentor-admin-js', Documentor::documentor_plugin_url( 'core/js/admin.js' ),array('jquery'), DOCUMENTOR_VER, true);
				wp_enqueue_script( 'documentor-modal-js', Documentor::documentor_plugin_url( 'core/js/jquery.leanModal.min.js' ),array('jquery'), DOCUMENTOR_VER, false);
			}
			//register setting of documentor license key on admin_init hook
			register_setting( 'documentor-license-info', 'documentor_license_key' );
	
		}
		function documentor_guides_page() {
			// Create New
			if(isset($_GET['action']) && $_GET['action'] == 'create-new') {
				if ( is_admin() ){ // admin actions
			 		// Settings page only
					if ( isset($_GET['page']) && ('documentor-admin' == $_GET['page']) ) {

					}
			  	}
			  	
			  	global $wpdb,$table_prefix;
				$doc = new Documentor();
				$settings = $doc->default_documentor_settings;
				$sql = "SELECT * FROM ".$table_prefix.DOCUMENTOR_TABLE;
				$guides = $wpdb->get_results($sql);
			  	?>
				
			  	<div class="headdings wrap">
			  		<h2 class="guide_heading"><span class="dashicons dashicons-welcome-add-page"></span>Create New Guide</h2>
			  		<!--<span class="dashicons dashicons-screenoptions"></span>-->
				</div>
				<div class="documentor-newdoc">
				<form method="post" name="documentor_newform" class="documentor-newform">
				<div class="doc-form-row">
					<label for="guidetitle"><?php _e('Guide Name','documentor'); ?></label>
					<input type="text" name="guidetitle" class="doc-form-input" placeholder="Enter Guide name" />
				</div>
					
				<div class="doc-form-row">
					<label for="settings"><?php _e('Apply Settings','documentor'); ?></label>
					<select name="settings" class="doc-form-input">
						<option value="0" selected="selected">Default Settings</option>
						<?php 
						 if( $guides ) {
						 	foreach( $guides as $guide ) {
						 		$row = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM wp_posts WHERE ID = %d", $guide->post_id ) );
						 		echo '<option value="'.esc_attr($guide->doc_id).'">'.$row.'</option>';
						 	}
						 }
						?>
					</select>
				</div>
				<div class="error_msg"></div>
				<input type="submit" name="create" value="Create" class="create-btn" />
				</form>
				</div>
				<?php
				if( isset( $_POST['create'] ) && $_POST['create']=="Create") {
					$guide=new DocumentorGuide();
					$docid=$guide->create();
					if($docid>0){
						$editpage = Documentor::documentor_admin_url(array('page'=>'documentor-admin'))."&action=edit&id=".$docid;
						wp_redirect( $editpage ); exit;
					}
				} 
				
				
			}
			elseif( ( isset($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'delete' || $_GET['action'] == 'export' || $_GET['action'] == 'make_copy' ) ) || ( isset( $_POST['doc-import'] ) && $_POST['doc-import'] == 'Import') ){
					// Edit Document
					$id = isset($_GET['id']) ? $_GET['id'] : 1;
					$guide=new DocumentorGuide($id);
					$documentor_curr = $guide->get_settings();
					if(isset($_POST['save-settings'])) {
						$numarr = array('indexformat', 'navmenu_default', 'navmenu_fsize', 'actnavbg_default', 'sectitle_default', 'sectitle_fsize', 'seccont_default', 'seccont_fsize', 'disable_ajax','suggest_edit', 'feedback', 'related_doc', 'sedit_frmname', 'sedit_frmemail', 'sedit_frmmsgbox', 'sedit_frmcapcha', 'feedback_frmname', 'feedback_frmemail', 'feedback_frmtext', 'feedback_frmcapcha');
						foreach( $_POST['documentor_options'] as $key=>$value ) {
							if(in_array($key,$numarr)) {
								$value = intval($value);
							} else {
								if( is_string( $value ) ) {
									$value = stripslashes($value);
									$value = sanitize_text_field($value);	
								}
							}
							$new_settings_value[$key]=$value;
						}
						if(isset($_POST['documentor_options']['skin']) && $documentor_curr['skin'] != $_POST['documentor_options']['skin'] ) { 
							/* Populate skin specific settings */	
							$skin = $_POST['documentor_options']['skin'];
							$skin_defaults_str='default_settings_'.$skin;
							require_once ( dirname( dirname(__FILE__) ). '/skins/'.$skin.'/settings.php');
							global ${$skin_defaults_str};
							if(count(${$skin_defaults_str})>0){
								foreach(${$skin_defaults_str} as $key=>$value){
									$new_settings_value[$key]=$value;	
								} 
							}
							/* END - Poulate skin specific settings */ 
						}
						$newsettings = json_encode($new_settings_value);
						$newtitle = ( isset( $_POST['guidename'] ) ) ? sanitize_text_field($_POST['guidename']) : ''; 
						$guide->update_settings( $newsettings , $newtitle );
					} 
					if(isset($_POST['save_related'])) {
						$guide->save_related($_POST);
					}
					//delete guide
					if( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) {
						$id = isset($_GET['id']) ? $_GET['id'] : '';
						if( !empty( $id ) ) {
							$guide->delete();
							$editpage = Documentor::documentor_admin_url(array('page'=>'documentor-admin'));
							wp_redirect( $editpage ); exit;
						}
					}	
					//export document
					if( isset($_GET['action']) && $_GET['action'] == 'export' ) {
						$id = isset($_GET['id']) ? $_GET['id'] : '';
						if( !empty( $id ) ) {
							$guide->export();
							$editpage = Documentor::documentor_admin_url(array('page'=>'documentor-admin'));
							wp_redirect( $editpage ); exit;
						}
					}
					//import document
					if( isset( $_POST['doc-import'] ) && $_POST['doc-import'] == 'Import') {
						$xml_mimetypes = array('text/xml','application/xml');
						if ($_FILES['doc_import_file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['doc_import_file']['tmp_name']) && in_array($_FILES['doc_import_file']['type'], $xml_mimetypes) ) { 
							$xml = simplexml_load_file($_FILES['doc_import_file']['tmp_name']);
							$documentor_curr = $xml->settings;						
							$documentor_curr = json_decode(json_encode($documentor_curr), TRUE); 
							foreach( $documentor_curr as $k => $v ) {
								$inner = explode( '|' , $v );
								if( (strpos($k,'fontgsubset') !== false && empty($v)) || (strpos($k,'tfontgsubset') !== false && empty($v)) ) {
									$documentor_curr[$k]=array();
								} elseif( (strpos( $k,'fontgsubset' ) !== false && count( $inner ) == 1) || (strpos( $k,'tfontgsubset' ) !== false && count( $inner ) == 1)) {
									$documentor_curr[$k] = array($v);
								} else if( $k == 'guide' && empty($v) ) {
									$documentor_curr[$k]=array();
								} elseif( $k == 'guide' && count( $inner ) == 1 ) {
									$documentor_curr[$k] = array($v);
								} else {
									if( count( $inner ) > 1) { 
										if( $k == 'button' ) {
											foreach ( $inner as $key => $value ) {
												$inner[ $key+1 ] = $value;
											}
											$inner[0] = '0';
											$documentor_curr[$k] = $inner;
										}	
										else {
											$documentor_curr[$k] = $inner;
										}
									} else {
										$documentor_curr[$k] = $v;
									}
								}
							}
							
							$doc = new Documentor();
							$default_documentor_settings = $doc->default_documentor_settings;
							//Merging default settings with skin specific settings
							$skin=$documentor_curr['skin'];
							$skin_defaults_str='default_settings_'.$skin;
							require_once(dirname(dirname (__FILE__)) . '/skins/'.$skin.'/settings.php');
							$default_settings=array_merge($default_documentor_settings, ${$skin_defaults_str});
							$documentor_curr=array_merge($default_settings, $documentor_curr);
							/*foreach($default_settings as $key=>$value) {
								  if(!isset($documentor_curr[$key])) {
									 $documentor_curr[$key] = $value;
								  }
							}*/
							
							foreach( $default_documentor_settings as $key => $value ){
								if( !isset( $documentor_curr[$key] ) ) $documentor_curr[$key]=$value;
							}
							$settings = json_encode($documentor_curr);
							$doc_title = json_decode(json_encode($xml->title), TRUE);
							$doc_title=$doc_title[0];
							$orderString = json_decode(json_encode($xml->sections_order), TRUE);
							$orderString=$orderString[0];
							//insert document
							global $wpdb, $table_prefix;							
							//*1.4
							$post = array(
									'post_title'    => $doc_title,					
									'post_type'	=> 'guide',
									'post_status'	=> 'publish'					
							);
							$postid = wp_insert_post( $post );
							add_post_meta($postid,'_doc_settings',$settings,true);	
							//add_post_meta($postid,'_doc_sections_order',$orderString,true);	
							$wpdb->insert( 
								$table_prefix.DOCUMENTOR_TABLE, 
								array(
									'post_id'=> $postid
								), 
								array( 
									'%d'
								) 
							);
							$docid=$wpdb->insert_id;
							//*1.4
							//insert sections
							foreach( $xml->sections->item as $item ) {
								$post_title = (string)$item->post_title;
								$post_content = (string)$item->post_content;
								$post_type = $item->post_type;
								$menu_title = (string)$item->menu_title;
								$section_title = (string)$item->section_title;
								$slug = (string)$item->slug;
								//make a slug unique
								$slug = $slug.'-2';
								// Create post object
								$doc_post = array(
								  'post_title'    => $post_title,
								  'post_content'  => $post_content,
								  'post_status'   => 'publish',
								  'post_type'   => $post_type
								 );

								// Insert the post into the database
								$postid = wp_insert_post( $doc_post );
																
								//update post meta
								update_post_meta( $postid, '_documentor_menutitle', $menu_title );
								update_post_meta( $postid, '_documentor_sectiontitle', $section_title );
								
								if( $post_type == 'inline' ) $type = 0;
								else if( $post_type == 'post' ) $type = 1;
								else if( $post_type == 'page' ) $type = 2;
								else if( $post_type == 'nav_menu_item' ) $type = 3;
								else $type = 4;
								
								//insert into sections table
								$wpdb->insert( 
									$table_prefix.DOCUMENTOR_SECTIONS, 
									array(
										'doc_id' => $docid,
										'post_id' => $postid,
										'type'	=> $type,
										'slug' => $slug
									), 
									array( 
										'%d',
										'%d', 
										'%s',
										'%s'
									) 
								);
								$secid = $wpdb->insert_id;
								$orderString = str_replace( ':'.$item->order.'}', ':'.$secid.'#}', $orderString);
								$orderString = str_replace( ':'.$item->order.',', ':'.$secid.'#,', $orderString);
							} 
							$orderString = str_replace( '#', '', $orderString );
							
							//*1.4
							 $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $docid ) );
							update_post_meta($post_id,'_doc_sections_order',$orderString);
     						}
     						$editpage = Documentor::documentor_admin_url(array('page'=>'documentor-admin'));
						wp_redirect( $editpage ); exit;
					}
					/* Make a copy of document */
					if( isset($_GET['action']) && $_GET['action'] == 'make_copy' ) {
						$id = isset($_GET['id']) ? $_GET['id'] : '';
						if( !empty( $id ) ) {
							global $wpdb, $table_prefix;
							$doc = new DocumentorGuide($id);
							$documentor_curr= $doc->get_settings();
							//*1.4
							$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $id ) );
							//$orderString = $doc->sections_order;
							
							$orderString= get_post_meta($post_id,'_doc_sections_order',true);
							$settings = json_encode($documentor_curr);
							
							
							$post = array(
									'post_title'=> 'Copy of '.$doc->title,					
									'post_type'=> 'guide',
									'post_status'=> 'publish'					
							);
							$postid = wp_insert_post( $post );
							add_post_meta($postid,'_doc_settings',$settings,true);
							add_post_meta($postid,'_doc_sections_order',$orderString,true);		
							$wpdb->insert( 
								$table_prefix.DOCUMENTOR_TABLE, 
								array(
									'post_id'=> $postid
								), 
								array( 
									'%d'
								) 
							);
							$docid=$wpdb->insert_id;
							
							$sections = $doc->get_sections();
							foreach( $sections as $section ) {
								$pid = $section->post_id;
								$postdata = get_post( $pid );
								if( $postdata != NULL ) {
									$post_title = $postdata->post_title;
									$menu_title = get_post_meta( $pid, '_documentor_menutitle', true );
									$section_title = get_post_meta( $pid, '_documentor_sectiontitle', true );
									$post_content = $postdata->post_content;
									$post_type = ( get_post_type($pid) != NULL ) ? get_post_type($pid): '';
									$slug = $section->slug;
									//make a slug unique
									$slug = $slug.'-2';
									// Create post object
									$doc_post = array(
									  'post_title'    => $post_title,
									  'post_content'  => $post_content,
									  'post_status'   => 'publish',
									  'post_type'   => $post_type
									 );

									// Insert the post into the database
									$postid = wp_insert_post( $doc_post );
																
									//update post meta
									update_post_meta( $postid, '_documentor_menutitle', $menu_title );
									update_post_meta( $postid, '_documentor_sectiontitle', $section_title );
								
									if( $post_type == 'inline' ) $type = 0;
									else if( $post_type == 'post' ) $type = 1;
									else if( $post_type == 'page' ) $type = 2;
									else if( $post_type == 'nav_menu_item' ) $type = 3;
									else $type = 4;
								
									//insert into sections table
									$wpdb->insert( 
										$table_prefix.DOCUMENTOR_SECTIONS, 
										array(
											'doc_id' => $docid,
											'post_id' => $postid,
											'type'	=> $type,
											'slug' => $slug
										), 
										array( 
											'%d',
											'%d', 
											'%s',
											'%s'
										) 
									);
									$secid = $wpdb->insert_id;
									$orderString = str_replace( ':'.$section->sec_id.'}', ':'.$secid.'#}', $orderString);
									$orderString = str_replace( ':'.$section->sec_id.',', ':'.$secid.'#,', $orderString);
								}
							}
							$orderString = str_replace( '#', '', $orderString );
							
							update_post_meta($docid,'_doc_sections_order',$orderString);
						}
						$editpage = Documentor::documentor_admin_url(array('page'=>'documentor-admin'));
						wp_redirect( $editpage ); exit;
					}
					$guide->admin_view();
			} else {
				//Default Guides Page
				//enqueue scripts required for preview
				wp_enqueue_script( 'documentor_fixedjs', Documentor::documentor_plugin_url( 'core/js/jquery.lockfixed.js' ), array('jquery'), DOCUMENTOR_VER, false);
				wp_enqueue_script( 'documentor_wowjs', Documentor::documentor_plugin_url( 'core/js/wow.js' ), array('jquery'), DOCUMENTOR_VER, false);
				wp_enqueue_script( 'doc_js', Documentor::documentor_plugin_url( 'core/js/documentor.js' ), array( 'jquery', 'jquery-ui-autocomplete' ) );
				wp_localize_script( 'doc_js', 'DocAjax', array( 'docajaxurl' => admin_url( 'admin-ajax.php' ) ) );
				 ?>
				 <script type="text/javascript">
				 	var documentorPreview = true;
				 </script>
				<div class="wrap documentor-container" id="documentor_create" style="clear:both;">
					<h2 class="top_heading_eb"><span class="dashicons dashicons-menu"></span> <?php _e('Manage Guides','documentor'); ?>
						<div class="documentor_menu wrap">
							<a href="<?php echo Documentor::documentor_admin_url(array('page'=>'documentor-admin'));?>&action=create-new" class="add-new-h2">Create New</a>
							<a href="#documentor_import" rel="leanModal" class="add-new-h2">Import</a>
						</div>
					</h2>
					<table class="widefat display no-wrap dataTable" id="datatable" >
						<thead>
							<tr class="even">
								<th class="documentid-column">#</th>
								<th class="documentname-column"><?php _e('Title','documentor'); ?></th>
								<th><?php _e('Shortcode','documentor'); ?></th>
								<th><?php _e('Template Tag','documentor'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr class="even">
								<th class="documentid-column">#</th>
								<th class="documentname-column"><?php _e('Title','documentor'); ?></th>
								<th><?php _e('Shortcode','documentor'); ?></th>
								<th><?php _e('Template Tag','documentor'); ?></th>
							</tr>
						</tfoot>
						
						<?php
						
						global $wpdb,$table_prefix;
						$doc = new Documentor();
						$settings = $doc->default_documentor_settings;
						$docus = $wpdb->get_results( "SELECT doc_id FROM ".$table_prefix.DOCUMENTOR_TABLE );
						
						if( count($docus) > 0 ) {
							$i=1;
							foreach($docus as $docu) {
								$guide=new DocumentorGuide($docu->doc_id);      
								?>
								<tr>
								<td width="10%" ><?php echo $i ?></td>
								<td>
								<?php echo $guide->doc_title; ?>
								<div class="document_action plugins"><a href="<?php echo Documentor::documentor_admin_url(array('page'=>'documentor-admin'));?>&action=edit&id=<?php echo $guide->docid; ?>"><?php _e('Edit','documentor');?></a> | <a href="#TB_inline&width=1400&height=550&inlineId=documentor-preview&docid=<?php echo $guide->docid; ?>" title="Preview" class="thickbox doc-preview-lnk"><?php _e('Preview','documentor');?></a> | <a class="delete" href="<?php echo Documentor::documentor_admin_url(array('page'=>'documentor-admin'));?>&action=delete&id=<?php echo $guide->docid; ?>" onclick="return confirmDocDelete()" name="delete_slider" ><?php _e('Delete','documentor');?></a> | <a href="<?php echo Documentor::documentor_admin_url(array('page'=>'documentor-admin'));?>&action=export&id=<?php echo $guide->docid; ?>"><?php _e('Export','documentor');?></a> | <a href="<?php echo Documentor::documentor_admin_url(array('page'=>'documentor-admin'));?>&action=make_copy&id=<?php echo $guide->docid; ?>" onclick="return confirmDocCopy()"><?php _e('Copy','documentor');?></a>
								</div>
								</td>
								<td>[documentor <?php echo $guide->docid; ?>]</td>
								<td><?php echo "&lt;?php if(function_exists('get_documentor')){ get_documentor('".$guide->docid."'); }?&gt;"; ?></td>
								</tr>
						
								<?php
								$i=$i+1;
							 }						
						  } ?>
						<input type="hidden" id="doc_curr_preview" class="doc_curr_preview" value="" />
					</table>
				</div>
				<div class="clrleft"></div>
				<!--import form start-->
				<div id="documentor_import" class="documentor-import">
					<form action="" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
						<input type="file" name="doc_import_file" id="doc_import_file">
						<input type="submit" value="Import" name="doc-import" title="Import Document" class="button-primary">
					</form>		
				</div>
				<!--import form end-->
				<!-- preview -->
				<?php add_thickbox(); ?>
				<div id="documentor-preview" style="display: none;">
					<div class="documentor-preview-inner"><?php //echo do_shortcode( '[documentor 1]' ) ?></div>
				</div>
		<?php }
		}
		//global settings
		function documentor_global_settings() { 
			$documentor_global_curr = get_option('documentor_global_options');
			
			$doc = new Documentor();
			$global_options = $doc->documentor_global_options;
			$group='documentor-global-group';
			$documentor_global_options = 'documentor_global_options';
			foreach( $global_options as $key=>$value ) {
				if( !isset( $documentor_global_curr[$key] ) ) 
					$documentor_global_curr[$key]='';
			}
			
			?>
			<div class="global_settings">
				<h2> <?php _e('Documentor Global Settings','documentor'); ?> </h2>
				<form name="documentor_global_settings" method="post" action="options.php">
					<?php settings_fields($group); ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('Enable Inline Sections','documentor'); ?></th>
							<td>
								<div class="eb-switch eb-switchnone">
									<input type="hidden" name="<?php echo $documentor_global_options;?>[custom_post]" class="hidden_check" id="documentor_custom_post" value="<?php echo esc_attr($documentor_global_curr['custom_post']);?>">
									<input id="documentor_custompost" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked("1", $documentor_global_curr['custom_post']); ?> >
									<label for="documentor_custompost"></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Supported Post Types for adding sections','documentor'); ?></th>
							<td>
								<select name="<?php echo $documentor_global_options;?>[custom_posts][]" multiple="multiple" size="3" style="min-height:6em;">
								<?php 
								$args=array(
								  'public'   => true
								); 
								$output = 'objects'; // names or objects, note names is the default
								$post_types=get_post_types($args,$output); 
								
								$exclude_pts = array('attachment','revision','nav_menu_item');
								foreach($exclude_pts as $exclude_pt)
   									 unset($post_types[$exclude_pt]);
								
								$custom_posts_arr=$documentor_global_curr['custom_posts'];
								if(!isset($custom_posts_arr) or !is_array($custom_posts_arr) ) $custom_posts_arr=array();
										foreach($post_types as $post_type) { ?>
										  <option value="<?php echo $post_type->name;?>" <?php if(in_array($post_type->name,$custom_posts_arr)){echo 'selected';} ?>><?php echo $post_type->labels->name;?></option>
										<?php } ?>
								</select>
							</td>
						</tr>
						<?php // Documentor 1.3.3- start ?>
						<tr valign="top">
							<th scope="row"><?php _e('Minimum User Level to create and manage guides','documentor'); ?></th>
							<td><select name="<?php echo $documentor_global_options;?>[user_level]" id="documentor_user_level">
							<option value="manage_options"<?php if ($documentor_global_curr['user_level'] == "manage_options"){ echo "selected";}?> ><?php _e('Administrator','documentor'); ?></option>
							
							<option value="edit_others_posts" <?php if ($documentor_global_curr['user_level'] == "edit_others_posts"){ echo "selected";}?> ><?php _e('Editor and Admininstrator','documentor'); ?></option>
							<option value="publish_posts" <?php if ($documentor_global_curr['user_level'] == "publish_posts"){ echo "selected";}?> ><?php _e('Author, Editor and Admininstrator','documentor'); ?></option>
							<option value="edit_posts" <?php if ($documentor_global_curr['user_level'] == "edit_posts"){ echo "selected";}?> ><?php _e('Contributor, Author, Editor and Admininstrator','documentor'); ?></option>
							</select>
							</td>
						</tr>
						<?php // Documentor 1.3.3- end ?>
						<tr valign="top">
							<th scope="row"><?php _e('Custom Styles','documentor'); ?></th>
							<td>
								<textarea name="<?php echo $documentor_global_options;?>[custom_styles]"  rows="5" cols="40" class="code"><?php echo $documentor_global_curr['custom_styles']; ?></textarea>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" name="Save" class="button-primary" value="Save Changes">
					</p>
				</form>	
			</div>
		<?php
		}
		function register_global_settings() {
			register_setting( 'documentor-global-group', 'documentor_global_options' );
		}
		//delete post from sections table if post is deleted from posts table
		function doc_delete_section( $pid ) {
			global $wpdb,$table_prefix;
			$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$table_prefix."posts WHERE ID = %d", $pid ) ); 
			if( $post != NULL ) {
				$wpdb->delete( $table_prefix.DOCUMENTOR_SECTIONS, array( 'post_id' => $pid ), array( '%d' ) );		
			}
		}
			
	}//end class
}//end if
new DocumentorAdmin();
?>
