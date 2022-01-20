<?php 
class DocumentorGuide{
		public $docid;
		public $title='';
		public $settings='';
		
		function __construct($id=0) {
			$this->docid=$id;
			if($this->docid>0) {
				global $table_prefix, $wpdb;
				
				//ver1.4 start
				$postid = $this->get_guide_post_id($this->docid);
				if( isset($postid) and intval($postid)>0 ) {
					$guiderow = $wpdb->get_row( $wpdb->prepare( "SELECT post_title,post_date FROM ".$table_prefix."posts WHERE ID = %d", $postid ) );
					if(count($guiderow) > 0){
						$settings=get_post_meta($postid,'_doc_settings',true);				
						$sections_order=get_post_meta($postid,'_doc_sections_order',true);
						$pdf_id=get_post_meta($postid,'_doc_pdf_id',true);				
						$row=(object)array(
							'doc_id'=>$this->docid,
							'doc_title'=>$guiderow->post_title,
							'created_on'=>$guiderow->post_date,
							'pdf_id'=>$pdf_id,
							'sections_order'=>$sections_order,
							'settings'=>$settings 
						);
						$this->title=$row->doc_title;
						$this->doc_title=$row->doc_title;
						$this->settings=$row->settings;
						$this->sections_order=$row->sections_order;
						$this->pdf_id = $row->pdf_id;
						$this->createdon =$row->created_on;
					}
				}
				else { //if guide post type did not get created properly for this particular guide
					$guide = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $this->docid ) );
					if( count($guide) > 0 ) {
						$doc = new Documentor();
						$default_documentor_settings = $doc->default_documentor_settings;
						$created_on=(isset($guide->created_on)?($guide->created_on):(date('Y-m-d H:i:s', strtotime("now"))));
						$pdf_id=(isset($guide->pdf_id)?($guide->pdf_id):0);
						$rel_id=(isset($guide->rel_id)?($guide->rel_id):0);
						$rel_title=(isset($guide->rel_title)?($guide->rel_title):'');
						$post= array(
							'post_title'=>$guide->doc_title,
							'post_type'=>'guide',
							'post_status'=>'publish',
							'post_content'=>'[documentor '.$guide->doc_id.']',
							'post_date'=> $created_on
							);
						$post_id=wp_insert_post( $post );
						$wpdb->update( 
							$table_prefix.DOCUMENTOR_TABLE, 
							array( 
								'post_id' => $post_id	
							), 
							array( 'doc_id' => $guide->doc_id ), 
							array( 
								'%d'
							), 
							array( '%d' ) 
						);		
						$curr_settings = json_decode( $guide->settings, true );
						//Merging default settings with skin specific settings
						$skin=$curr_settings['skin'];
						$skin_defaults_str='default_settings_'.$skin;
						require_once(dirname(dirname (__FILE__)) . '/skins/'.$skin.'/settings.php');
						$default_settings=array_merge($default_documentor_settings, ${$skin_defaults_str});
						$curr_settings=array_merge($default_settings, $curr_settings);
						/*foreach($default_settings as $key=>$value) {
							  if(!isset($curr_settings[$key])) {
								 $curr_settings[$key] = $value;
							  }
						}*/
						$curr_settings = json_encode($curr_settings);
						
						update_post_meta($post_id,'_doc_settings',$curr_settings);
						update_post_meta($post_id,'_doc_sections_order',$guide->sections_order);
						update_post_meta($post_id,'_doc_rel_id',$rel_id);
						update_post_meta($post_id,'_doc_rel_title',$rel_title);
						update_post_meta($post_id,'_doc_pdf_id',$pdf_id);
						
						$this->title=$guide->doc_title;
						$this->doc_title=$guide->doc_title;
						$this->settings=$curr_settings;
						$this->sections_order=$guide->sections_order;
						$this->pdf_id=$pdf_id;
						$this->createdon=$created_on;
					}
				}
			} //ver1.4End	
		}
		// use to get postid from documentor table
		function get_guide_post_id($docid){ //ver1.4
		   global $wpdb,$table_prefix;
		   $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $docid ) );
		   return $post_id;
		}	
		//get guide
		function get_guide( $docid ) { //ver1.4
			global $table_prefix, $wpdb;
			$postid = $this->get_guide_post_id($docid);			
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT post_title,post_date FROM $wpdb->posts WHERE ID = %d", $postid ) );	
			$settings=get_post_meta($postid,'_doc_settings',true);				
			$sections_order=get_post_meta($postid,'_doc_sections_order',true);
			$pdf_id=get_post_meta($postid,'_doc_pdf_id',true);
			$rel_id=get_post_meta($postid,'_doc_rel_id',true);
			$rel_title=get_post_meta($postid,'_doc_rel_title',true);
			if(isset($row->post_title) && isset($row->post_date)){						
				$guide= (object) array(
					'post_id'=>$postid,
					'doc_id'=>$this->docid,
					'doc_title'=>$row->post_title,
					'created_on'=>$row->post_date,
					'pdf_id'=>$pdf_id,
					'sections_order'=>$sections_order,
					'settings'=>$settings,
					'rel_id'=>$rel_id,
					'rel_title'=>$rel_title 
				);
				return $guide;
			} else {
			 echo "Guide is not available"; die();
			}		 	
		}
	    	//update settings       
		function update_settings( $setting, $newtitle ) { //ver1.4 start
			global $wpdb, $table_prefix;
			$postid = $this->get_guide_post_id($this->docid);	
			//$id = $postid;
			$guide_title = $wpdb->get_row( $wpdb->prepare( "SELECT post_title FROM ".$table_prefix."posts WHERE ID = %d", $postid ) );
			if($guide_title != $newtitle ) {
				$update_post= array( 
						'ID' => $postid,	
						'post_title' => $newtitle
						
						);
				wp_update_post( $update_post );
			}
			update_post_meta($postid,'_doc_settings',$setting);
		}
		//get settings ver1.4end
		function get_settings() {
			global $table_prefix, $wpdb;
			$postid = $this->get_guide_post_id($this->docid); //ver1.4	
			$result=get_post_meta($postid,'_doc_settings',true);	//ver1.4
			if( $result != NULL ) {
				$documentor_curr = json_decode($result, true);
				$documentor_curr = $this->populate_documentor_current($documentor_curr); 
				
				return $documentor_curr;
			}
			
		}
		//get sections of document
		function get_sections() {
			global $table_prefix, $wpdb;
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$table_prefix.DOCUMENTOR_SECTIONS." WHERE doc_id = %d",$this->docid ) ); 
			return $result;
			
		}
		//get guide managers email_id
		function get_guideManager_emails( $uidarr ) {
			global $table_prefix, $wpdb;
			$htmlnm = '';
			$i = 0; $cnt = count( $uidarr );
			foreach( $uidarr as $uid ) {
				$i++;
				$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$table_prefix."users WHERE ID= %d", $uid ) );
				if( !empty( $result->user_email ) ) {
					if( $i == $cnt ) {
						$htmlnm .= $result->user_email;
					} else {
						$htmlnm .= $result->user_email.',';
					}
				}
			} 
			return $htmlnm;
		}
		//get ip address of user
		function getRealIpAddr()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
			{
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
			{
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
		//create guide
		function create() {
			if( isset( $_POST['guidetitle'] ) ) {
				global $table_prefix, $wpdb;
				$guidetitle = sanitize_text_field( $_POST['guidetitle'] );
				$doc = new Documentor();
				if( isset($_POST['settings']) ) {
					if($_POST['settings'] == '0') {
						$settings = $doc->default_documentor_settings;
						$ser_settings = json_encode( $settings );
					}
					else {
						$settings = $this->get_guide( $_POST['settings'] )->settings;
						$ser_settings = $settings;
					}
				}
				else {
					$settings = $doc->default_documentor_settings;
					$ser_settings = json_encode( $settings );
				}
				$post = array(
					'post_title'    => $guidetitle,					
					'post_type'	=> 'guide',
					'post_status'	=> 'publish'					
				);				
				$postid = wp_insert_post( $post );
				add_post_meta($postid,'_doc_settings',$ser_settings,true);	//ver1.4	
				$wpdb->insert( 
					$table_prefix.DOCUMENTOR_TABLE, 
					array(
						'post_id'=> $postid
					), 
					array( 
						'%d'
					) 
				);//ver1.4
				
				$this->docid=$wpdb->insert_id;
							
				return $this->docid;
			}
			else {
				return 0;
			}
		}
		//delete guide
		function delete() {
			global $table_prefix, $wpdb;
			$postid= $this->get_guide_post_id($this->docid); //ver1.4			
			$wpdb->delete( $wpdb->posts, array( 'ID' => $postid ), array( '%d' ) ); //ver1.4
			$wpdb->delete( $table_prefix.DOCUMENTOR_TABLE, array( 'doc_id' => $this->docid ), array( '%d' ) );
			// delete from post_meta tabel
			delete_post_meta($postid,'_doc_settings');				
			delete_post_meta($postid,'_doc_sections_order');
			delete_post_meta($postid,'_doc_pdf_id');
			delete_post_meta($postid,'_doc_rel_id');
			delete_post_meta($postid,'_doc_rel_title');
			
			//delete from documentor section table
			$wpdb->delete( $table_prefix.DOCUMENTOR_SECTIONS, array( 'doc_id' => $this->docid ), array( '%d' ) );
		}
		//show document on front 
		function view() {
			$html = '';
			$settings_arr = $this->get_settings();
			if( count( $settings_arr ) > 0 ) {
				require_once(dirname(dirname (__FILE__)) . '/skins/'.$settings_arr["skin"].'/index.php');
				$classname = 'DocumentorDisplay'.$settings_arr["skin"];
				$displayobj = new $classname( $this->docid );
				$html = $displayobj->display();
			}
			$html=apply_filters('guide_html', $html);
			return $html;
		}
		//build sections html on admin
		function buildItem($obj) {
			if(isset($this->docid)) {
				if( class_exists( 'DocumentorSection' ) && is_admin() ) {
					$id = $this->docid;
					$ds = new DocumentorSection( $id, $obj->id);
				}
			}
			$settings = $this->get_settings();
			if( $settings['button'][3] == 1 && $settings['disable_ajax'] == 1 ) {
				$pdfbtnstyle = 'style="display:inline-block"';
			} else {
				$pdfbtnstyle = 'style="display:none"';
			}
			$html = "";
			if( $ds != null ) {
			$sectiondata = $ds->getdata();
			
			foreach( $sectiondata as $secdata ) {	
			if( $secdata->type == 0 ) {
				$type = 'Inline';
			} else if( $secdata->type == 1 ) {
				$type = 'Post';
			} else if( $secdata->type == 2 ) {
				$type = 'Page';
			} else if( $secdata->type == 3 ) {
				$type = 'Link';
			}
			$postid = $secdata->post_id;
			$menutitle = '';
			$slug = $secdata->slug;
			//WPML
			if( function_exists('icl_plugin_action_links') ) {	
				if( $secdata->type == 0 ) $ptype = 'documentor-sections';
				else if( $secdata->type == 1 ) $ptype = 'post';
				else if( $secdata->type == 2 ) $ptype = 'page';
				else if( $secdata->type == 3 ) $ptype = 'nav_menu_item';
				else if( $secdata->type == 4 ) {
					$ptype = get_post_type( $postid );
				}
				$lang_post_id = icl_object_id( $postid , $ptype, true, ICL_LANGUAGE_CODE );
				$postdata = get_post( $lang_post_id );
				$postid = $lang_post_id;
			} else {
				$postdata = get_post( $postid );
			}
			if( $secdata->type == 4 ) {
				$type = $postdata->post_type;
			}
			if( $secdata->type != 3 ) {
				$menutitle = get_post_meta( $postid, '_documentor_menutitle', true );
			} else if( $secdata->type == 3 ) {
				if( $postdata != NULL )
					$menutitle = $postdata->post_title;
			}
			$sectiontitle = get_post_meta( $postid, '_documentor_sectiontitle', true );
			$html .= '<li class="table-row oldrow close" data-id="'. $obj->id . '" id="' . $obj->id . '">';
			$html .= '<div class="doc-list"><button class="sectiont_img close dd-nodrag" type="button" ></button>';
			$html .= '<div class="table-col slide-title">
					<p class="this-title" >'.$menutitle;
					$html.= '<span class="item-controls">
							<span class="item-type">'.$type.'</span>
						</span>
					</p>
				  </div>';
				  $html .= '<div class="section-form dd-nodrag" style="display:none;">';
					//if not link section and user having capability to edit post
					$ptype = strtolower( $type );
					if( $type == 'Inline' ) $ptype = 'documentor-sections';
					if( post_type_exists($ptype) ) {
						if( ( $secdata->type != 3 ) && current_user_can('edit_post', $postid) ) {  
							$edtlink = get_edit_post_link($postid);
							$html .= '<a href="'.$edtlink.'" target="_blank" class="section-editlink">'. __('Edit','documentor').'</a>';
							$html .= '<a href="'.$edtlink.'#commentsdiv" target="_blank" class="section-commentslink">'. __('Comments/Suggestion','documentor').'</a>';
						}
					}
					$html .= '<div class="sections-div">
						<label class="titles">'. __('Menu Title','documentor').'</label>
						<input type="text" name="menutitle" class="txts menutitle" placeholder="'. __('Enter Menu Title','documentor').'" value="'.esc_attr($menutitle).'" />';
						if( $secdata->type != 3 ) { //if section is not link
						$html .='<label class="titles">'. __('Section Title','documentor').'</label>
						<input type="text" name="sectiontitle" class="txts sectiontitle" placeholder="'. __('Enter Menu Title','documentor').'" value="'.esc_attr($sectiontitle).'" />';
						}
						if( $secdata->type == 3 ) { //if section is link
							$content = unserialize( $postdata->post_content );
							$html.='<label class="titles">'. __('Link','documentor').'</label>
							<input type="text" name="linkurl" class="txts linkurl" placeholder="http://" value="'.esc_url($content['link']).'" />';
							$targetwval = ( $content['new_window'] != '0' ) ? "1":"0";
							$newwindow = ( $content['new_window'] != '0' ) ? 'checked="checked"':"";
							$html.='<label class="titles">'. __('Open in new window','documentor').'</label><input type="checkbox" name="new_window" class="new_window" '.$newwindow.' /><input type="hidden" name="targetw" class="targetw" value="'.esc_attr($targetwval).'">';
						}
						$html.='<div class="clrleft"></div>
						<div class="sections-div">
							<label class="titles">'. __('Slug','documentor').'</label>
							<input type="text" name="slug" class="txts sec-slug" placeholder="'. __('Enter slug','documentor').'" value="'.apply_filters( 'editable_slug', $slug ).'" />
						</div>
						<div class="sections-div">
							<label class="titles">'. __('Feedback count','documentor').'</label>
							<div class="feedback-cnt">
								<span class="dashicons dashicons-smiley" title="'. __('Upvotes','documentor').'"></span>
								<span class="vote-cnt upvote">'.$secdata->upvote.'</span>
								<span class="dashicons dashicons-arrow-down-alt2 down" title="'. __('Downvotes','documentor').'"></span>
								<span class="vote-cnt downvote">'.$secdata->downvote.'</span>
								<input type="submit" name="reset_feedbackcnt" class="reset-feedbackcnt link-button" value="'. __('Reset','documentor').'" /><span class="reset-success"></span>
							</div>
						</div>
						<div class="description-wide submitbox">
								<input type="hidden" name="section_id" class="section_id" value="'.esc_attr($secdata->sec_id).'">
								<input type="hidden" name="post_id" class="post-id" value="'.esc_attr($postid).'">
								<input type="hidden" name="type" class="ptype" value="'.esc_attr($secdata->type).'">
							   	<input type="hidden" name="docid" class="docid" value="'.esc_attr($secdata->doc_id).'">
								<input type="submit" name="update_section" class="update-section button-primary" value="'. __('Save','documentor').'" />
								<span class="meta-sep hide-if-no-js"> | </span>
								<a class="remove-section link-button" href="#confirmdelete-'.$secdata->sec_id.'" >'. __('Remove','documentor').'</a> 
								<span class="meta-sep hide-if-no-js"> | </span>
								<input type="submit" name="cancel_section" class="cancel-section link-button" value="'. __('Cancel','documentor').'" />
								<span class="docsec-pdf-msg"></span>
								<a class="doc-section-pdf link-button" '.$pdfbtnstyle.'>'. __('Generate PDF','documentor').'</a> 
								<span class="docloader"></span>
								<div id="confirmdelete-'.$secdata->sec_id.'" class="confirmdelete" >
									<div class="doc-popupcontent text">Do you want to delete all children sections ?</div> <div class="doc-popupcontent"><button class="delete_child btn-delete">Delete children</button><button class="keep_child btn-cancel">Keep children</button></div></div>	
								<div class="validation-msg"></div>
						</div>
					
					</div>
				</div></div>';
					
			if ( isset( $obj->children ) && $obj->children ) {
				$html .= '<ol class="dd-list">';
				foreach( $obj->children as $child ) {
				    $html .= $this->buildItem($child);
				}
				$html .= '</ol>';
			}

			$html .= '</li>';
			}
			
			}
			return $html;
		}
		//
		function get_inline_css() {
			$settings = $this->get_settings();
			$cssarr = array(
					'navmenu' => '',
					'sectitle' => '',
					'sectioncontent'=>'',
					'guidetitle' => '',
				);
			$style_start= 'style="';
			$style_end= '"';
			$objfonts = new DocumentorFonts();
			//section title
			//check for use theme default option
			if( $settings['sectitle_default'] == 0 ) {
				if ($settings['sectitle_fstyle'] == "bold" or $settings['sectitle_fstyle'] == "bold italic" ){
					$sectitle_fweight = "bold";
				} else {
					$sectitle_fweight = "normal";
				}
				if ($settings['sectitle_fstyle'] == "italic" or $settings['sectitle_fstyle'] == "bold italic"){
					$sectitle_fstyle = "italic";
				} else {
					$sectitle_fstyle = "normal";
				}
			
				if( $settings['sect_font'] == 'regular' ) {
					$sect_font = $settings['sectitle_font'].', helvetica, Helvetica, sans-serif';
					$pt_fontw = $sectitle_fweight;
					$pt_fontst = $sectitle_fstyle;
				} else if( $settings['sect_font'] == 'google' ) {
					$sectitle_fontg = isset($settings['sectitle_fontg']) ? trim($settings['sectitle_fontg']) : '';
					$pgfont = $objfonts->get_google_font($settings['sectitle_fontg']);
					( isset( $pgfont['category'] ) ) ? $ptfamily = $pgfont['category'] : '';
					( isset( $settings['sectitle_fontgw'] ) ) ? $ptfontw = $settings['sectitle_fontgw'] : ''; 
					if (strpos($ptfontw,'italic') !== false) {
						$pt_fontst = 'italic';
					} else {
						$pt_fontst = 'normal';
					}
					if( strpos($ptfontw,'italic') > 0 ) { 
						$len = strpos($ptfontw,'italic');
						$ptfontw = substr( $ptfontw, 0, $len );
					}
					if( strpos($ptfontw,'regular') !== false ) { 
						$ptfontw = 'normal';
					}
					if( isset($settings['sectitle_fontgw']) && !empty($settings['sectitle_fontgw']) ) {
						$currfontw=$settings['sectitle_fontgw'];
						$gfonturl = $pgfont['urls'][$currfontw];
			
					}  else {
						$gfonturl = 'http://fonts.googleapis.com/css?family='.$settings['sectitle_fontg'];
					}
					if( isset($settings['sectitle_fontgsubset']) && !empty($settings['sectitle_fontgsubset']) ) {
						$strsubset = implode(",",$settings['sectitle_fontgsubset']);
						$gfonturl = $gfonturl.'&subset='.$strsubset;
					} 
					if(!empty($sectitle_fontg)) {
						wp_enqueue_style( 'documentor_sectitle', $gfonturl,array(),DOCUMENTOR_VER);
						$sectitle_fontg=$pgfont['name'];
						$sect_font = $sectitle_fontg.','.$ptfamily;
						$pt_fontw = $ptfontw;	
					}
					else { //if not set google font fall back to default font
				
						$sect_font = 'helvetica, Helvetica, sans-serif';
						$pt_fontw = 'normal';
						$pt_fontst = 'normal';
					}
				} else if( $settings['sect_font'] == 'custom' ) {
					$sect_font = $settings['ptfont_custom'];
					$pt_fontw = $sectitle_fweight;
					$pt_fontst = $sectitle_fstyle;
				}
				$tcss = '';
				if( $settings['skin'] == 'mint' ) { $tcss = 'margin: 62px 0px 15px 0px;border-bottom: 1px dotted #e6e6e6;'; }
				if( $settings['skin'] == 'bar' ) {
					$tcss = 'margin: 40px 0px 20px 0px;padding-bottom: 9px;border-bottom: 1px dotted #e6e6e6;'; 
				}
				$lineheight = $settings['sectitle_fsize'] + 5;
				if( $settings['skin'] != 'default' || $settings['skin'] != 'cherry' ) $lineheight = $settings['sectitle_fsize'] + 8;
				$cssarr['sectitle']=$style_start.'clear:none;line-height:'. $lineheight .'px;font-family:'. $sect_font.';font-size:'.$settings['sectitle_fsize'].'px;font-weight:'.$pt_fontw.';font-style:'.$pt_fontst.';color:'.$settings['sectitle_color'].';'.$tcss.$style_end;
			}
			//navigation menu
			//check for use theme default option
			if( $settings['navmenu_default'] == 0 ) {
				if ($settings['navmenu_fstyle'] == "bold" or $settings['navmenu_fstyle'] == "bold italic" ){
					$navmenu_fweight = "bold";
				} else {
					$navmenu_fweight = "normal";
				}
				if ($settings['navmenu_fstyle'] == "italic" or $settings['navmenu_fstyle'] == "bold italic"){
					$navmenu_fstyle = "italic";
				} else {
					$navmenu_fstyle = "normal";
				}
			
				if( $settings['navt_font'] == 'regular' ) {
					$navt_font = $settings['navmenu_tfont'].', helvetica, Helvetica, sans-serif';
					$pt_fontw = $navmenu_fweight;
					$pt_fontst = $navmenu_fstyle;
				} else if( $settings['navt_font'] == 'google' ) {
					$navmenu_tfontg = isset($settings['navmenu_tfontg']) ? trim($settings['navmenu_tfontg']) : '';
					$pgfont = $objfonts->get_google_font($settings['navmenu_tfontg']);
					( isset( $pgfont['category'] ) ) ? $ptfamily = $pgfont['category'] : '';
					( isset( $settings['navmenu_tfontgw'] ) ) ? $ptfontw = $settings['navmenu_tfontgw'] : ''; 
					if (strpos($ptfontw,'italic') !== false) {
						$pt_fontst = 'italic';
					} else {
						$pt_fontst = 'normal';
					}
					if( strpos($ptfontw,'italic') > 0 ) { 
						$len = strpos($ptfontw,'italic');
						$ptfontw = substr( $ptfontw, 0, $len );
					}
					if( strpos($ptfontw,'regular') !== false ) { 
						$ptfontw = 'normal';
					}
					if( isset($settings['navmenu_tfontgw']) && !empty($settings['navmenu_tfontgw']) ) {
						$currfontw=$settings['navmenu_tfontgw'];
						$gfonturl = $pgfont['urls'][$currfontw];
			
					}  else {
						$gfonturl = 'http://fonts.googleapis.com/css?family='.$settings['navmenu_tfontg'];
					}
					if( isset($settings['navmenu_tfontgsubset']) && !empty($settings['navmenu_tfontgsubset']) ) {
						$strsubset = implode(",",$settings['navmenu_tfontgsubset']);
						$gfonturl = $gfonturl.'&subset='.$strsubset;
					} 
					if(!empty($navmenu_tfontg)) {
						wp_enqueue_style( 'documentor_navmenutitle', $gfonturl,array(),DOCUMENTOR_VER);
						$navmenu_tfontg=$pgfont['name'];
						$navt_font = $navmenu_tfontg.','.$ptfamily;
						$pt_fontw = $ptfontw;	
					}
					else { //if not set google font fall back to default font
				
						$navt_font = 'helvetica, Helvetica, sans-serif';
						$pt_fontw = 'normal';
						$pt_fontst = 'normal';
					}
				} else if( $settings['navt_font'] == 'custom' ) {
					$navt_font = $settings['ptfont_custom'];
					$pt_fontw = $navmenu_fweight;
					$pt_fontst = $navmenu_fstyle;
				}
				$cssarr['navmenu']=$style_start.'clear:none;line-height:'. ($settings['navmenu_fsize'] + 5) .'px;font-family:'. $navt_font.';font-size:'.$settings['navmenu_fsize'].'px;font-weight:'.$pt_fontw.';font-style:'.$pt_fontst.';color:'.$settings['navmenu_color'].';'.$style_end;
				//print_r($settings['navmenu_color']);
			}
			//section content
			//check for use theme default option
			if( $settings['seccont_default'] == 0 ) {
				if ($settings['seccont_fstyle'] == "bold" or $settings['seccont_fstyle'] == "bold italic" ){
					$sectitle_fweight = "bold";
				} else {
					$sectitle_fweight = "normal";
				}
				if ($settings['seccont_fstyle'] == "italic" or $settings['seccont_fstyle'] == "bold italic"){
					$seccont_fstyle = "italic";
				} else {
					$seccont_fstyle = "normal";
				}
			
				if( $settings['secc_font'] == 'regular' ) {
					$secc_font = $settings['seccont_font'].', helvetica, Helvetica, sans-serif';
					$pt_fontw = $sectitle_fweight;
					$pt_fontst = $seccont_fstyle;
				} else if( $settings['secc_font'] == 'google' ) {
					$seccont_fontg = isset($settings['seccont_fontg']) ? trim($settings['seccont_fontg']) : '';
					$pgfont = $objfonts->get_google_font($settings['seccont_fontg']);
					( isset( $pgfont['category'] ) ) ? $ptfamily = $pgfont['category'] : '';
					( isset( $settings['seccont_fontgw'] ) ) ? $ptfontw = $settings['seccont_fontgw'] : ''; 
					if (strpos($ptfontw,'italic') !== false) {
						$pt_fontst = 'italic';
					} else {
						$pt_fontst = 'normal';
					}
					if( strpos($ptfontw,'italic') > 0 ) { 
						$len = strpos($ptfontw,'italic');
						$ptfontw = substr( $ptfontw, 0, $len );
					}
					if( strpos($ptfontw,'regular') !== false ) { 
						$ptfontw = 'normal';
					}
					if( isset($settings['seccont_fontgw']) && !empty($settings['seccont_fontgw']) ) {
						$currfontw=$settings['seccont_fontgw'];
						$gfonturl = $pgfont['urls'][$currfontw];
			
					}  else {
						$gfonturl = 'http://fonts.googleapis.com/css?family='.$settings['seccont_fontg'];
					}
					if( isset($settings['seccont_fontgsubset']) && !empty($settings['seccont_fontgsubset']) ) {
						$strsubset = implode(",",$settings['seccont_fontgsubset']);
						$gfonturl = $gfonturl.'&subset='.$strsubset;
					} 
					if(!empty($seccont_fontg)) {
						wp_enqueue_style( 'documentor_seccontent', $gfonturl,array(),DOCUMENTOR_VER);
						$seccont_fontg=$pgfont['name'];
						$secc_font = $seccont_fontg.','.$ptfamily;
						$pt_fontw = $ptfontw;	
					}
					else { //if not set google font fall back to default font
				
						$secc_font = 'helvetica, Helvetica, sans-serif';
						$pt_fontw = 'normal';
						$pt_fontst = 'normal';
					}
				} else if( $settings['secc_font'] == 'custom' ) {
					$secc_font = $settings['ptfont_custom'];
					$pt_fontw = $sectitle_fweight;
					$pt_fontst = $seccont_fstyle;
				}
				$lineheight = $settings['seccont_fsize'] + 5;
				if( $settings['skin'] != 'default' || $settings['skin'] != 'cherry' ) $lineheight = $settings['seccont_fsize'] + 9;
				$cssarr['sectioncontent']=$style_start.'clear:none;line-height:'. $lineheight .'px;font-family:'. $secc_font.';font-size:'.$settings['seccont_fsize'].'px;font-weight:'.$pt_fontw.';font-style:'.$pt_fontst.';color:'.$settings['seccont_color'].';'.$style_end;
			}
			//guide title css
			if( $settings['guidet_default'] == 0 ) {
				if ($settings['guidet_fstyle'] == "bold" or $settings['guidet_fstyle'] == "bold italic" ){
					$guidet_fweight = "bold";
				} else {
					$guidet_fweight = "normal";
				}
				if ($settings['guidet_fstyle'] == "italic" or $settings['guidet_fstyle'] == "bold italic"){
					$guidet_fstyle = "italic";
				} else {
					$guidet_fstyle = "normal";
				}
			
				if( $settings['guidet_font'] == 'regular' ) {
					$guidetfont = $settings['guidetitle_font'].', helvetica, Helvetica, sans-serif';
					$gt_fontw = $guidet_fweight;
					$gt_fontst = $guidet_fstyle;
				} else if( $settings['guidet_font'] == 'google' ) {
					$guidet_fontg = isset($settings['guidet_fontg']) ? trim($settings['guidet_fontg']) : '';
					$pgfont = $objfonts->get_google_font($settings['guidet_fontg']);
					( isset( $pgfont['category'] ) ) ? $ptfamily = $pgfont['category'] : '';
					( isset( $settings['guidet_fontgw'] ) ) ? $ptfontw = $settings['guidet_fontgw'] : ''; 
					if (strpos($ptfontw,'italic') !== false) {
						$gt_fontst = 'italic';
					} else {
						$gt_fontst = 'normal';
					}
					if( strpos($ptfontw,'italic') > 0 ) { 
						$len = strpos($ptfontw,'italic');
						$ptfontw = substr( $ptfontw, 0, $len );
					}
					if( strpos($ptfontw,'regular') !== false ) { 
						$ptfontw = 'normal';
					}
					if( isset($settings['guidet_fontgw']) && !empty($settings['guidet_fontgw']) ) {
						$currfontw=$settings['guidet_fontgw'];
						$gfonturl = $pgfont['urls'][$currfontw];
			
					}  else {
						$gfonturl = 'http://fonts.googleapis.com/css?family='.$settings['guidet_fontg'];
					}
					if( isset($settings['guidet_fontgsubset']) && !empty($settings['guidet_fontgsubset']) ) {
						$strsubset = implode(",",$settings['guidet_fontgsubset']);
						$gfonturl = $gfonturl.'&subset='.$strsubset;
					} 
					if(!empty($guidet_fontg)) {
						wp_enqueue_style( 'documentor_guidetitle', $gfonturl,array(),DOCUMENTOR_VER);
						$guidet_fontg=$pgfont['name'];
						$guidetfont = $guidet_fontg.','.$ptfamily;
						$gt_fontw = $ptfontw;	
					}
					else { //if not set google font fall back to default font
				
						$guidetfont = 'helvetica, Helvetica, sans-serif';
						$gt_fontw = 'normal';
						$gt_fontst = 'normal';
					}
				} else if( $settings['guidet_font'] == 'custom' ) {
					$guidetfont = $settings['ptfont_custom'];
					$gt_fontw = $guidet_fweight;
					$gt_fontst = $guidet_fstyle;
				}
				$lineheight = $settings['guidet_fsize'] + 5;
				$cssarr['guidetitle']=$style_start.'clear:none;line-height:'. $lineheight .'px;font-family:'. $guidetfont.';font-size:'.$settings['guidet_fsize'].'px;font-weight:'.$gt_fontw.';font-style:'.$gt_fontst.';color:'.$settings['guidet_color'].';'.$style_end;
			}
			return $cssarr;
		}
		
		public static function documentorRemoveAnchors($data) {
			$regex  = '/(<a\s*'; // Start of anchor tag
			$regex .= '(.*?)\s*'; // Any attributes or spaces that may or may not exist
			$regex .= 'href=[\'"]+?\s*(?P<link>\S+)\s*[\'"]+?'; // Grab the link
			$regex .= '\s*(.*?)\s*>\s*'; // Any attributes or spaces that may or may not exist before closing tag 
			$regex .= '(?P<name>\s*(.*?)\s*)'; // Grab the name
			$regex .= '\s*<\/a>)/i'; // Any number of spaces between the closing anchor tag (case insensitive)
			if (is_array($data)) {
				// This is what will replace the link (modify to you liking)
				$data = "{$data['name']}";			
			}
			return preg_replace_callback($regex, 'DocumentorGuide::documentorRemoveAnchors', $data);
		}
		
		public static function documentorReplaceAnchorsWithText($data){
			$regex  = '/(<a\s*'; // Start of anchor tag
			$regex .= '(.*?)\s*'; // Any attributes or spaces that may or may not exist
			$regex .= 'href=[\'"]+?\s*(?P<link>\S+)\s*[\'"]+?'; // Grab the link
			$regex .= '\s*(.*?)\s*>\s*'; // Any attributes or spaces that may or may not exist before closing tag 
			$regex .= '(?P<name>\s*(.*?)\s*)'; // Grab the name
			$regex .= '\s*<\/a>)/i'; // Any number of spaces between the closing anchor tag (case insensitive)
			if (is_array($data)) {
				// This is what will replace the link (modify to you liking)
				$data = "{$data['name']}({$data['link']})"."<br />";				
			}
			return preg_replace_callback($regex, 'DocumentorGuide::documentorReplaceAnchorsWithText', $data);
		}
			
		//get all sections html
		function get_sections_html() {
			global $table_prefix, $wpdb;
			$html='<input type="hidden" value="'.esc_attr($this->docid).'" name="docsid" />';
			$doc = new Documentor();
			$settings = $doc->default_documentor_settings;
			$sections = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM ".$table_prefix.DOCUMENTOR_SECTIONS." WHERE doc_id = %d",$this->docid ) );
			
			if( $sections ) {
			 $i = 1;
			 $postid= $this->get_guide_post_id($this->docid); //ver1.4
			 $obj = get_post_meta($postid,'_doc_sections_order',true); //ver1.4				 
				if( !empty($obj) ) {
					$jsonObj = json_decode($obj);
					$html.='<ol class="dd-list">';
					foreach( $jsonObj as $jobj ) {
						$html.= $this->buildItem($jobj);
					}					
					$html.='</ol><textarea name="reorders-output" id="reorders-output">'.$this->sections_order.'</textarea>';
				} 
			}
			echo $html;
			die();
		}
		function get_childrens( $element, $html, $docid ) {
			//$arrid = array();
			$guide = new DocumentorGuide( $docid );
			foreach( $element as $valueKey => $value ) {
				foreach ( $value as $k => $v ) {
					if( $k == 'id' ) {
						 $html .= $v.",";
					} else if( $k == 'children' ) {
						$html = $guide->get_childrens( $v, $html, $docid );
					}
				}
			}
			return $html;
		}
		//save sections of guide
		public static function save_sections() {
			check_ajax_referer( 'documentor-sections-nonce', 'documentor-sections-nonce' );
			global $table_prefix, $wpdb;
			$sorders = ( isset( $_POST['reorders-output'] ) ) ? sanitize_text_field($_POST['reorders-output']) : '';	
			$docid = ( isset( $_POST['docid'] ) ) ? intval($_POST['docid']) : '';
			$docpostid= ( isset( $_POST['doc_postid'] ) ) ? intval($_POST['doc_postid']) : '0';
			//$sectionsarr = ( isset( $_POST['sectionObj'] ) ) ? $_POST['sectionObj'] : '';		
			$doc_title = ( isset( $_POST['guidename'] ) ) ? sanitize_text_field($_POST['guidename']) : '';
			if( empty( $doc_title ) ) {
				_e("Warning: Guide name could not be blank","documentor");
			} else if( !empty( $docid ) ) { 
				//update sections order in documentor table
				$jarr = json_decode( stripslashes($sorders), true );
				if( count($jarr) > 0 ) {
					$sections_order = stripslashes_deep( $sorders );
				} else {
					$sections_order = '';
				}
				//ver1.4			
				$postid= $docpostid;
				update_post_meta($postid,'_doc_sections_order',$sections_order);
				//delete sections from sections table which are not in section order of documentor table
				
				//$sorders=get_post_meta($postid,'_doc_sections_order',true);
				$sorders=$sections_order;
				$jarr = json_decode( $sorders, true );	
				if( count($jarr) > 0 ) {
					$idstr = '';
					$guide = new DocumentorGuide( $docid );
					foreach($jarr as $elementKey => $element) {
					    foreach($element as $valueKey => $value) {
						if( $valueKey == 'id' ){
							$idstr .= $value.",";
						} else if( $valueKey == 'children' ) {
							$idstr = $guide->get_childrens( $value, $idstr, $docid );
						}
					    }
					}
					$idstr = rtrim( $idstr , ',' );
					
					$delsql = "DELETE FROM ".$table_prefix.DOCUMENTOR_SECTIONS." WHERE sec_id NOT IN(".$idstr.") AND doc_id = ".$docid;
					$wpdb->query($delsql);
					
				} else {
					$wpdb->delete( $table_prefix.DOCUMENTOR_SECTIONS, array( 'doc_id' => $docid ), array( '%d' ) );
				}				
				//save all sections of guide
				//No need to save each section on document/guide save //Fixed in 1.4.5
				/*foreach( $sectionsarr as $sectionarr ) {
					$postid = intval($sectionarr['postid' ]);
					$sectype = intval($sectionarr['type']);
					if( empty( $sectionarr['slug'] ) ) {
						_e("Warning: slug could not be blank.");die();
					}
					if( $sectype != 3 ) { //if not link section
						if( empty( $sectionarr['menutitle'] ) ) {
							_e("Warning: menu title could not be blank.");die();
							
						} else if( empty( $sectionarr['sectiontitle'] ) && $sectionarr['type'] == 0 ) {
							_e("Warning: section title could not be blank.");die(); 
						} else {
							//update post meta
							update_post_meta( $postid, '_documentor_sectiontitle', $sectionarr['sectiontitle'] );
							update_post_meta( $postid, '_documentor_menutitle', $sectionarr['menutitle'] );
						}
					} else if( $sectype == 3 ) { //link section
						if( empty( $sectionarr['linkurl'] ) ) {
							_e("Warning: link url could not be blank.");
						} else {
							$arr = array(
								'link' => $sectionarr['linkurl'],
								'new_window' => intval($sectionarr['new_window'])
							);
							$content = serialize( $arr ); 						
							//update nav_menu item post
							$guide_title = $wpdb->get_row( $wpdb->prepare( "SELECT post_title FROM ".$table_prefix."posts WHERE ID = %d", $postid ) );
							if($guide_title != $sectionarr['menutitle']) {
								$post = array(
									      'ID'           => $postid,
									      'post_title'   => $sectionarr['menutitle'],
									      'post_content' => $content
									);
								wp_update_post( $post );
							}
						}
					}
					//update slug in sections table
					$wpdb->update( 
						$table_prefix.DOCUMENTOR_SECTIONS, 
						array( 
							'slug' => sanitize_title( $sectionarr['slug'] )
						), 
						array( 'sec_id' => $sectionarr['section_id'] ), 
						array( 
							'%s'
						), 
						array( '%d' ) 
					);
				}*/
				//$postid = $docpostid;		
				$guide_title = $wpdb->get_row( $wpdb->prepare( "SELECT post_title FROM ".$table_prefix."posts WHERE ID = %d", $docpostid ) );
				if($guide_title != $doc_title ) {
					$update_post= array( 
						'ID' => $docpostid,
						'post_title' => $doc_title		
						);
					wp_update_post( $update_post );
				}
			}
			die();
		}
		//save related documents
		function save_related($post) {
			global $table_prefix, $wpdb;
			
			//ver1.4
			$postid= $this->get_guide_post_id($post['doc_id']);
			$guide_title = $wpdb->get_row( $wpdb->prepare( "SELECT post_title FROM ".$table_prefix."posts WHERE ID = %d", $postid ) );
			$doc_title=$post['guidename'];
			if($guide_title != $doc_title ) {
				$update_post= array( 
						'ID' => $postid,	
						'post_title' => $post['guidename']	
						);
				wp_update_post( $update_post );
			}
			
			update_post_meta($postid,'_doc_rel_id',$post['related_menu']);
			update_post_meta($postid,'_doc_rel_title',$post['related_title']);	
		}
		//
		function encode_operation($string)
		{
			$chars = str_split($string);
			$seed = mt_rand(0, (int)abs(crc32($string) / strlen($string)));

			foreach($chars as $key => $char)
			{
				$ord = ord($char);

				// ignore non-ascii chars
				if($ord < 128)
				{
					// pseudo "random function"
					$r = ($seed * (1 + $key)) % 100;

					if($r > 60 && $char !== '@') {} // plain character (not encoded), if not @-sign
					elseif($r < 45) $chars[$key] = '&#x'.dechex($ord).';'; // hexadecimal
					else $chars[$key] = '&#'.$ord.';'; // decimal (ascii)
				}
			}

			return implode('', $chars);
		}
		//Get captcha
		function generate_captcha( $name, $tr_name ) {
			$ops = array(
				'addition' => '+',
				'subtraction' => '&#8722;',
				'multiplication' => '&#215;',
				'division' => '&#247;',
			);

			$operations = array();
			$input = '<input type="number" size="2" length="2" id="'.$name.'" class="doc-captcha numberinput" name="'.$name.'" value="" required="true"/>';

			// available operations
			$operations = array('addition',
					'subtraction' );
	
			// operation
			$rnd_op = $operations[mt_rand(0, count($operations) - 1)];
			$number[3] = $ops[$rnd_op];

			// place where to put empty input
			$rnd_input = mt_rand(0, 2);

			// which random operation
			switch($rnd_op)
			{
				case 'addition':
					if($rnd_input === 0)
					{
						$number[0] = mt_rand(1, 10);
						$number[1] = mt_rand(1, 89);
					}
					elseif($rnd_input === 1)
					{
						$number[0] = mt_rand(1, 89);
						$number[1] = mt_rand(1, 10);
					}
					elseif($rnd_input === 2)
					{
						$number[0] = mt_rand(1, 9);
						$number[1] = mt_rand(1, 10 - $number[0]);
					}

					$number[2] = $number[0] + $number[1];
					break;

				case 'subtraction':
					if($rnd_input === 0)
					{
						$number[0] = mt_rand(2, 10);
						$number[1] = mt_rand(1, $number[0] - 1);
					}
					elseif($rnd_input === 1)
					{
						$number[0] = mt_rand(11, 99);
						$number[1] = mt_rand(1, 10);
					}
					elseif($rnd_input === 2)
					{
						$number[0] = mt_rand(11, 99);
						$number[1] = mt_rand($number[0] - 10, $number[0] - 1);
					}

					$number[2] = $number[0] - $number[1];
					break;
			}
	
			// position of empty input
			if($rnd_input === 0)
				$return = $input.' '.$number[3].' '.$this->encode_operation($number[1]).' = '.$this->encode_operation($number[2]);
			elseif($rnd_input === 1)
				$return = $this->encode_operation($number[0]).' '.$number[3].' '.$input.' = '.$this->encode_operation($number[2]);
			elseif($rnd_input === 2)
				$return = $this->encode_operation($number[0]).' '.$number[3].' '.$this->encode_operation($number[1]).' = '.$input;
		
			set_transient($tr_name, sha1(AUTH_KEY.$number[$rnd_input].$tr_name, false), apply_filters('doc_math_captcha_time', 300));
			return $return;

		} 
		//get PDF fonts
		function get_pdf_fonts( $currfont ) {
			$pdflang=array(
				'courier' => 'Courier',
				'courierb' => 'Courier Bold',
				'courierbi' => 'Courier Bold Italic',
				'courieri' => 'Courier Italic',
				'helvetica' => 'Helvetica',
				'helveticab' => 'Helvetica Bold',
				'helveticabi' => 'Helvetica Bold Italic',
				'helveticai' => 'Helvetica Italic',
				'symbol' => 'Symbol',
				'times' => 'Times New Roman',
				'timesb' => 'Times New Roman Bold',
				'timesbi' => 'Times New Roman Bold Italic',
				'timesi' => 'Times New Roman Italic',
				'zapfdingbats' => 'Zapf Dingbats',	 
				'aealarabiya' => 'Al Arabiya',
				'aefurat' => 'Furat',
				'cid0cs' => 'Chinese Simplified',
				'cid0ct' => 'Chinese Traditional',
				'cid0jp' => 'Japanese',
				'cid0kr' => 'Korean',
				'dejavusans' => 'Dejavu Sans',
				'dejavusansb' => 'Dejavu Sans Bold',
				'dejavusansbi' => 'Dejavu Sans Bold Italic',
				'dejavusansi' => 'Dejavu Sans Italic',
				'dejavusanscondensed' => 'Dejavu Sans Condensed',
				'dejavusanscondensedb' => 'Dejavu Sans Condensed Bold',
				'dejavusanscondensedbi' => 'Dejavu Sans Condensed Bold Italic',
				'dejavusanscondensedi' => 'Dejavu Sans Condensed Italic',
				'dejavusansextralight' => 'Dejavu Sans Extra Light',
				'dejavusansmono' => 'Dejavu Sans Mono',
				'dejavusansmonob' => 'Dejavu Sans Mono Bold',
				'dejavusansmonobi' => 'Dejavu Sans Mono Bold Italic',
				'dejavusansmonoi' => 'Dejavu Sans Mono Italic',
				'dejavuserif' => 'Dejavu Serif',
				'dejavuserifb' => 'Dejavu Serif Bold',
				'dejavuserifbi' => 'Dejavu Serif Bold Italic',
				'dejavuserifi' => 'Dejavu Serif Italic',
				'dejavuserifcondensed' => 'Dejavu Serif Condensed',
				'dejavuserifcondensedb' => 'Dejavu Serif Condensed Bold',
				'dejavuserifcondensedbi' => 'Dejavu Serif Condensed Bold Italic',
				'dejavuserifcondensedi' => 'Dejavu Serif Condensed Italic',
				'freemono' => 'Free Mono',
				'freemonob' => 'Free Mono Bold',
				'freemonobi' => 'Free Mono Bold Italic',
				'freemonoi' => 'Free Mono Italic',
				'freesans' => 'Free Sans',
				'freesansb' => 'Free Sans Bold',
				'freesansbi' => 'Free Sans Bold Italic',
				'freesansi' => 'Free Sans Italic',
				'freeserif' => 'Free Serif',
				'freeserifb' => 'Free Serif Bold',
				'freeserifbi' => 'Free Serif Bold Italic',
				'freeserifi' => 'Free Serif Italic',
				'hysmyeongjostdmedium' => 'HYSMyeongJoStd Medium',
				'kozgopromedium' => 'KozGoPro Medium',
				'kozminproregular' => 'KozMinPro Regular',
				'msungstdlight' => 'MSungstd Light',
				'pdfacourier' => 'PDF Courier',
				'pdfacourierb' => 'PDF Courier Bold',
				'pdfacourierbi' => 'PDF Courier Bold Italic',
				'pdfacourieri' => 'PDF Courier Italic',
				'pdfahelvetica' => 'PDF Helvetica',
				'pdfahelveticab' => 'PDF Helvetica Bold',
				'pdfahelveticabi' => 'PDF Helvetica Bold Italic',
				'pdfahelveticai' => 'PDF Helvetica Italic',
				'pdfasymbol' => 'PDF Symbol',
				'pdfatimes' => 'PDF Times',
				'pdfatimesb' => 'PDF Times Bold',
				'pdfatimesbi' => 'PDF Times Bold Italic',
				'pdfatimesi' => 'PDF Times Italic',
				'pdfazapfdingbats' => 'PDF Zapf Dingbats',
				'stsongstdlight' => 'STSongStd Light',
				'uni2cid_ac15' => 'Unicode to CID ac15',
				'uni2cid_ag15' => 'Unicode to CID ag15',
				'uni2cid_aj16' => 'Unicode to CID aj16',
				'uni2cid_ak12' => 'Unicode to CID ak12'
			 );
			$optionhtml = '';$currfonts = array();
			$directory = DOCPRO_PATH.'core/includes/tcpdf/fonts/';
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) { 
					if($file != '.' and $file != '..') {  	
						$farr = explode(".",$file);
						if( $farr[1] == 'php') {
							$currfonts[] = $farr[0];
						}						
					} 
				}
				closedir($handle);
			}
			if( count( $currfonts ) > 0 ) {
				foreach( $pdflang as $fontfile => $fontname ) {
					if( in_array( $fontfile, $currfonts ) ) {
						$optionhtml .= '<option value="'.$fontfile.'" '. selected( $currfont, $fontfile, false ) .'>'.$fontname.'</option>'; 
					}	
				}
			}
			return $optionhtml;
		}
		//Admin View of Guide
		function admin_view() {
				$documentor_curr = $this->get_settings();
				$guide = $this->get_guide( $this->docid );
				$class0 = $class1 = $class2 = $class3 = "";
				$tabindex = (isset( $_GET['tab'] )) ? $_GET['tab'] : '';
				if( !empty( $tabindex ) ) {
					if( $tabindex == 'sections' ) {
						$class0 = 'nav_tab_active';
					} else if( $tabindex == 'settings' ) {
						$class1 = 'nav_tab_active';
					} else if( $tabindex == 'embedcode' ) {
						$class2 = 'nav_tab_active';
					} else if( $tabindex == 'related' ) {
						$class3 = 'nav_tab_active';
					}
				} else {
					$class0 = 'nav_tab_active';
				}		
				if( $tabindex != 'add-sections' ) { ?>
					<div id="documentor_tabs" class="documentor_editguide"> 
							<div class="edit-guidetitle"><span class="dashicons dashicons-welcome-write-blog editguide-icon"></span> Edit Guide </div>
						<input type="text" id="documentor-name" class="docname" value="<?php echo esc_attr($guide->doc_title);?>" />
					</div>
					<div class="doc-successmsg"></div>
					<h2 class="nav-tab-wrapper"> 
						<a id="options-group-1-tab" class="nav-tab sections-tab <?php if( isset( $class0 ) ) echo $class0; ?>" title="<?php _e('Sections','documentor'); ?>" href="<?php echo esc_url( admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=sections') ); ?>"><?php _e('Sections','documentor'); ?></a> 
						<a id="options-group-2-tab" class="nav-tab settings-tab <?php if( isset( $class1 ) ) echo $class1; ?>" title="<?php _e('Settings','documentor'); ?>" href="<?php echo esc_url( admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=settings') ); ?>"><?php _e('Settings','documentor'); ?></a>
						  <?php
						  if( $documentor_curr['related_doc'] == 1 ) { ?>
						 	 <a id="options-group-4-tab" class="nav-tab relateddoc-tab <?php if( isset( $class3 ) ) echo $class3; ?>" title="<?php _e('Related documnets','documentor'); ?>" href="<?php echo esc_url( admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=related') ); ?>"><?php _e('Related','documentor'); ?></a> 
						 <?php } ?>
						<a id="options-group-3-tab" class="nav-tab embedcode-tab <?php if( isset( $class2 ) ) echo $class2; ?>" title="<?php _e('Embed code','documentor'); ?>" href="<?php echo esc_url( admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=embedcode') ); ?>"><?php _e('Embed Code','documentor'); ?></a> 
					</h2>
				<?php }
				if( ( isset( $tabindex ) && $tabindex == 'sections' ) || empty( $tabindex )) { ?>
					<div id="options-group-1" class="group sections">
						<div id="addsections" class="documentor-newdoc">
							<a href="<?php echo esc_url(admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=add-sections')); ?>" title="<?php _e('Add Section','documentor'); ?>" class="create-btn add-secbtn"><?php _e('Add Section','documentor'); ?></a>
							<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="docsid" />
							
							<input type="hidden" name="documentor-loader" value="<?php echo esc_url( admin_url('images/loading.gif') );?>" />
							<form name="guide_secform" class="guide-secform" method="post">
								<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="docid" />
								<input type="hidden" value="<?php echo esc_attr($this->get_guide_post_id($this->docid)); ?>" name="doc_postid" id="doc_postid" />
								<div id="reorders" class="reorders" >
											
								</div>
								<p>
								<?php $guide = $this->get_guide( $this->docid ); ?>
								<input type="hidden" name="guidename" class="guidename" value="<?php echo esc_attr($guide->doc_title);?>">
								<input type="submit" name="save_sections" class="save-sections button-primary" value="Save" style="display: none;" />
								<input type="hidden" name="documentor-sections-nonce" value="<?php echo wp_create_nonce( 'documentor-sections-nonce' ); ?>">
								<?php 
								
								$documentor_curr = $this->get_settings();
								$secarr = $this->get_sections();
								if( $documentor_curr['button'][3] == 1 && count($secarr) > 0 && $documentor_curr['disable_ajax'] == 0 ) {
									$style = 'style="display:inline-block"';
								} else {
									$style = 'style="display:none"';
								}?>
								<input type="submit" name="create_doc_pdf" class="create-doc-pdf button-primary" value="<?php _e('Generate PDF','documentor');?>" <?php echo $style;?> >
								<input type="submit" name="doc_feedbackcnt_reset" class="doc-feedbackcnt-reset button-primary" value="<?php _e('Reset Feedback Counts','documentor');?>" >
								<span class="docloader"></span>
								<span class="doc-pdf-msg"></span>
								</p>
							</form>
						</div>
					</div> <!--tab group-1 ends -->
				<?php } else if( $tabindex == 'add-sections' ) { ?>
					<div id="doc-add-sections" class="doc-add-sections">
						<div class="edit-guidetitle"><span class="dashicons dashicons-plus-alt addsec-icon"></span>Add New Section<a class="create-btn edit-guidebtn" href="<?php echo esc_url(admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=sections')); ?>"><?php _e('Edit','documentor'); ?></a></div>
						<div class="doc-successmsg"></div>
						<form method="post" id="addsecform" name="addsecform" class="addsecform">
							<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="docsid" />
							<div class="eb-cs-left">
								<?php 
								//if custom post is enabled then only add inline sections
								$global_settings_curr = get_option('documentor_global_options');
								if( isset( $global_settings_curr['custom_post'] ) && $global_settings_curr['custom_post'] == '1' ) { ?>
								<div class="eb-cs-tab eb-cs-blank doc-active"> <span class="dashicons dashicons-editor-alignleft"></span> <?php _e('Inline','documentor'); ?></div>
								<?php } ?>
								<?php
								if( isset( $global_settings_curr['custom_posts'] ) ) {
									foreach( $global_settings_curr['custom_posts'] as $post_type ) {
										$obj = get_post_type_object( $post_type ); 
										if( $obj !== null ) {
											if( $post_type == 'page' ) $dashicon_class = 'dashicons-admin-page'; else $dashicon_class = 'dashicons-admin-post';
									?>
										
										<div class="eb-cs-tab eb-cs-post" id="<?php echo $post_type;?>" ><span class="dashicons <?php echo $dashicon_class;?>"></span> <?php _e($obj->labels->name,'documentor'); ?></div>	
									<?php }
									}
								}
								?>
								<div class="eb-cs-tab eb-cs-links" id="attachment"><span class="dashicons dashicons-admin-links"></span> <?php _e('Links','documentor'); ?></div>
								
							</div>
							<div class="eb-cs-right-wrap">
								
								<?php 
								//if custom post is enabled then only add inline sections
								if( isset( $global_settings_curr['custom_post'] ) && $global_settings_curr['custom_post'] == '1' ) { ?>
									<div style="margin-left: 20px;" class="addinlinesecform">
											<div class="docfrm-div">
												<label class="titles"> <?php _e('Menu Title','documentor'); ?> </label>
												<input type="text" name="menutitle" class="txts menutitle" placeholder="<?php _e('Enter Menu Title','documentor'); ?>" value="" />
											</div>
											<div class="docfrm-div">
												<label class="titles"> <?php _e('Section Title','documentor'); ?> </label>
												<input type="text" name="sectiontitle" class="txts sectiontitle" placeholder="<?php _e('Enter Section Title','documentor'); ?>" value="" />
											</div>
											<div class="docfrm-div">
												<label class="titles"> <?php _e('Content','documentor'); ?> </label>
												<?php 
												$content = '';
												$editor_id = 'content';
												$settings =   array(
												    'wpautop' => true, // use wpautop?
												    'media_buttons' => true, // show insert/upload button(s)
												    'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
												    'textarea_rows' => 15, // rows="..."
												    'tabindex' => '',
												    'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
												    'editor_class' => '', // add extra class(es) to the editor textarea
												    'teeny' => false, // output the minimal editor config used in Press This
												    'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
												    'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
												    'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
												);
												echo '<div style="width:99%;height:auto;">';
													wp_editor( $content, $editor_id, $settings );
												echo '</div>';
												?>

											</div>
											<div class="clrleft"></div>
											<p><input type="submit" name="add_section" class="button-primary add-inlinesectionbtn" value="<?php _e('Insert','documentor'); ?>" /></p>
											<input type="hidden" name="post_type" value="inline" />
									</div>
								<?php }?>
							
								<div class="eb-cs-right"> 								
								</div>
							</div>
							<input type="hidden" name="documentor-sections-nonce" value="<?php echo wp_create_nonce( 'documentor-sections-nonce' ); ?>">
							<a class="create-btn edit-guidebtn" style="float: right;margin-right: 98px;" href="<?php echo esc_url(admin_url('admin.php?page=documentor-admin&action=edit&id='.$this->docid.'&tab=sections')); ?>"><span class="dashicons dashicons-undo doc-back"></span><?php _e('Back to Edit','documentor'); ?></a>
						</form>
						
					</div>
				<?php }//if tab ends
				else if( isset( $tabindex ) && $tabindex == 'settings' ) { ?>
				<div id="options-group-2" class="group settings">
				<form method="post" name="documentor-settings" class="documentor-settings">
				<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="docsid" />
				<input type="hidden" name="documentor-loader" value="<?php echo esc_url( admin_url('images/loading.gif') );?>" />
				<div id="basic" class="doc-settingsdiv">
				<div class="sub_settings toggle_settings">
				<h2 class="sub-heading"><?php _e('Basic Settings','documentor'); ?><span class="toggle_img"></span></h2> 
				
				<?php
				$documentor_options = 'documentor_options'; ?>
				<table class="form-table">

				<tr valign="top">
				<th scope="row"><?php _e('Skin','documentor'); ?></th>
				<td><select name="<?php echo $documentor_options;?>[skin]" id="doc-skin" class="doc-skin">
				<?php 
				$directory = DOCUMENTOR_CSS_DIR;
				if ($handle = opendir($directory)) {
					while (false !== ($file = readdir($handle))) { 
					 if($file != '.' and $file != '..') {  ?>	
						<option value="<?php echo esc_attr($file);?>" <?php if ($documentor_curr['skin'] == $file){ echo "selected";}?> ><?php echo $file;?></option>
				<?php		
				} }
					closedir($handle);
				}
				?>
				</select>
				</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e('Section Animation','documentor'); ?></th>
					<td>
						<?php $animation = $documentor_curr['animation']; ?>
						<select name="<?php echo $documentor_options;?>[animation]">
							<option value="">Select animation</option>
							<optgroup label="<?php _e('Attention Seekers','documentor'); ?>">
							  <option value="bounce" <?php selected( $animation, "bounce" ); ?> ><?php _e('bounce','documentor'); ?></option>
							  <option value="flash" <?php selected( $animation, "flash" ); ?> ><?php _e('flash','documentor'); ?></option>
							  <option value="pulse" <?php selected( $animation, "pulse" ); ?> ><?php _e('pulse','documentor'); ?></option>
							  <option value="rubberBand" <?php selected( $animation, "rubberBand" ); ?> ><?php _e('rubberBand','documentor'); ?></option>
							  <option value="shake" <?php selected( $animation, "shake" ); ?> ><?php _e('shake','documentor'); ?></option>
							  <option value="swing" <?php selected( $animation, "swing" ); ?> ><?php _e('swing','documentor'); ?></option>
							  <option value="tada" <?php selected( $animation, "tada" ); ?> ><?php _e('tada','documentor'); ?></option>
							  <option value="wobble" <?php selected( $animation, "wobble" ); ?> ><?php _e('wobble','documentor'); ?></option>
							</optgroup>
							<optgroup label="<?php _e('Bouncing Entrances','documentor'); ?>">
							  <option value="bounceIn" <?php selected( $animation, "bounceIn" ); ?> ><?php _e('bounceIn','documentor'); ?></option>
							  <option value="bounceInDown" <?php selected( $animation, "bounceInDown" ); ?> ><?php _e('bounceInDown','documentor'); ?></option>
							  <option value="bounceInLeft" <?php selected( $animation, "bounceInLeft" ); ?> ><?php _e('bounceInLeft','documentor'); ?></option>
							  <option value="bounceInRight" <?php selected( $animation, "bounceInRight" ); ?> ><?php _e('bounceInRight','documentor'); ?></option>
							  <option value="bounceInUp" <?php selected( $animation, "bounceInUp" ); ?> ><?php _e('bounceInUp','documentor'); ?></option>
							</optgroup>

						       <optgroup label="<?php _e('Fading Entrances','documentor'); ?>">
							  <option value="fadeIn" <?php selected( $animation, "fadeIn" ); ?> ><?php _e('fadeIn','documentor'); ?></option>
							  <option value="fadeInDown" <?php selected( $animation, "fadeInDown" ); ?> ><?php _e('fadeInDown','documentor'); ?></option>
							  <option value="fadeInDownBig"<?php selected( $animation, "fadeInDownBig" ); ?> ><?php _e('fadeInDownBig','documentor'); ?></option>
							  <option value="fadeInLeft" <?php selected( $animation, "fadeInLeft" ); ?> ><?php _e('fadeInLeft','documentor'); ?></option>
							  <option value="fadeInLeftBig" <?php selected( $animation, "fadeInLeftBig" ); ?> ><?php _e('fadeInLeftBig','documentor'); ?></option>
							  <option value="fadeInRight" <?php selected( $animation, "fadeInRight" ); ?> ><?php _e('fadeInRight','documentor'); ?></option>
							  <option value="fadeInRightBig" <?php selected( $animation, "fadeInRightBig" ); ?> ><?php _e('fadeInRightBig','documentor'); ?></option>
							  <option value="fadeInUp" <?php selected( $animation, "fadeInUp" ); ?> ><?php _e('fadeInUp','documentor'); ?></option>
							  <option value="fadeInUpBig" <?php selected( $animation, "fadeInUpBig" ); ?> ><?php _e('fadeInUpBig','documentor'); ?></option>
							</optgroup>

						       <optgroup label="<?php _e('Flippers','documentor'); ?>">
							  <option value="flip" <?php selected( $animation, "flip" ); ?> ><?php _e('flip','documentor'); ?></option>
							  <option value="flipInX" <?php selected( $animation, "flipInX" ); ?> ><?php _e('flipInX','documentor'); ?></option>
							  <option value="flipInY" <?php selected( $animation, "flipInY" ); ?> ><?php _e('flipInY','documentor'); ?></option>
						       </optgroup>

							<optgroup label="<?php _e('Lightspeed','documentor'); ?>">
							  <option value="lightSpeedIn" <?php selected( $animation, "lightSpeedIn" ); ?> ><?php _e('lightSpeedIn','documentor'); ?></option>
							</optgroup>

							<optgroup label="<?php _e('Rotating Entrances','documentor'); ?>">
							  <option value="rotateIn" <?php selected( $animation, "rotateIn" ); ?> ><?php _e('rotateIn','documentor'); ?></option>
							  <option value="rotateInDownLeft" <?php selected( $animation, "rotateInDownLeft" ); ?> ><?php _e('rotateInDownLeft','documentor'); ?></option>
							  <option value="rotateInDownRight" <?php selected( $animation, "rotateInDownRight" ); ?> ><?php _e('rotateInDownRight','documentor'); ?></option>
							  <option value="rotateInUpLeft" <?php selected( $animation, "rotateInUpLeft" ); ?> ><?php _e('rotateInUpLeft','documentor'); ?></option>
							  <option value="rotateInUpRight" <?php selected( $animation, "rotateInUpRight" ); ?> ><?php _e('rotateInUpRight','documentor'); ?></option>
							</optgroup>

							<optgroup label="<?php _e('Specials','documentor'); ?>">
							  <option value="hinge" <?php selected( $animation, "hinge" ); ?> ><?php _e('hinge','documentor'); ?></option>
							  <option value="rollIn" <?php selected( $animation, "rollIn" ); ?> ><?php _e('rollIn','documentor'); ?></option>
							</optgroup>

							<optgroup label="<?php _e('Zoom Entrances','documentor'); ?>">
							  <option value="zoomIn" <?php selected( $animation, "zoomIn" ); ?> ><?php _e('zoomIn','documentor'); ?></option>
							  <option value="zoomInDown" <?php selected( $animation, "zoomInDown" ); ?> ><?php _e('zoomInDown','documentor'); ?></option>
							  <option value="zoomInLeft" <?php selected( $animation, "zoomInLeft" ); ?> ><?php _e('zoomInLeft','documentor'); ?></option>
							  <option value="zoomInRight" <?php selected( $animation, "zoomInRight" ); ?> ><?php _e('zoomInRight','documentor'); ?></option>
							  <option value="zoomInUp" <?php selected( $animation, "zoomInUp" ); ?> ><?php _e('zoomInUp','documentor'); ?></option>
							</optgroup>

							 <optgroup label="<?php _e('Slide Entrances','documentor'); ?>">
							  <option value="slideInDown" <?php selected( $animation, "slideInDown" ); ?> ><?php _e('slideInDown','documentor'); ?></option>
							  <option value="slideInLeft" <?php selected( $animation, "slideInLeft" ); ?> ><?php _e('slideInLef','documentor'); ?></option>
							  <option value="slideInRight" <?php selected( $animation, "slideInRight" ); ?> ><?php _e('slideInRight','documentor'); ?></option>
							  <option value="slideInUp" <?php selected( $animation, "slideInUp" ); ?> ><?php _e('slideInUp','documentor'); ?></option>
							 </optgroup>
      

						</select>
					</td>
				</tr>
				
				<?php 
					$indexstyle = ( $documentor_curr['skin'] == 'cherry' ) ? 'style="display: none;"' : 'style="display: table-row;"';
					$mtogglestyle = ( $documentor_curr['skin'] == 'bar' ) ? 'style="display: none;"' : 'style="display: table-row;"';
				?>
				<tr valign="top" class="doc-indexformat-row" <?php echo $indexstyle; ?>>
				<th scope="row"><?php _e('Indexing Format','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo indexswitch" >
					<input type="hidden" name="<?php echo $documentor_options;?>[indexformat]" id="documentor_indexformat" class="hidden_check" value="<?php echo esc_attr($documentor_curr['indexformat']);?>">
					<input id="indexformat" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['indexformat']); ?>>
					<label for="indexformat"></label>
				</div>
					<?php
					 
					 $ind_display= ($documentor_curr['indexformat']==1 and $documentor_curr['disable_ajax'] != 1)?"display:inline":"display:none";
					
					 ?>
					<a href="#format-index" id="index_format" rel="leanModal" style="<?php echo $ind_display; ?>" title="Guide Title Formatting " ><?php _e('Format','documentor');?></a>
				<script type="text/javascript">
  	 			jQuery( document ).ready( function() {
	  	 			jQuery('.indexswitch').on("change",function(){ 
			      		var val_checkbox = jQuery("#indexformat").attr("checked");			      		
			      		if(val_checkbox=='checked'){
			      			console.log(val_checkbox);
			      			jQuery('#index_format').show();
			      		}else {
			      		  console.log("no checked");
			      		  jQuery('#index_format').hide();
			      		}
  	 			  });
  	 			 });
  	 			</script>
  	 			
				
				</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e('Guide Title','documentor'); ?></th>
					<td>
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="<?php echo $documentor_options;?>[guidetitle]" id="documentor_guidetitle" class="hidden_check" value="<?php echo esc_attr($documentor_curr['guidetitle']);?>">
						<input id="guidetitle" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['guidetitle']); ?>>
						<label for="guidetitle"></label>
					</div>
					<a href="#options-guidetitle" rel="leanModal" title="Guide Title Formatting" ><?php _e('Options','documentor');?></a>
					</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Scrolling','documentor'); ?></th>
				<td>
				<?php $documentor_curr['scrolling'] = ( !isset( $documentor_curr['scrolling'] )  ) ? 1 : $documentor_curr['scrolling']; ?>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[scrolling]" id="doc-enable-scroll" class="hidden_check" value="<?php echo esc_attr($documentor_curr['scrolling']);?>">
					<input id="enable-scroll" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['scrolling']); ?>>
					<label for="enable-scroll"></label>
				</div>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Fixed Menu','documentor'); ?></th>
				<td>
				<?php $documentor_curr['fixmenu'] = ( !isset( $documentor_curr['fixmenu'] )  ) ? 1 : $documentor_curr['fixmenu']; ?>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[fixmenu]" id="doc-enable-fixmenu" class="hidden_check" value="<?php echo esc_attr($documentor_curr['fixmenu']);?>">
					<input id="enable-fixmenu" class="cmn-toggle eb-toggle-round" type="checkbox"  <?php checked('1', $documentor_curr['fixmenu']); ?>>
					<label for="enable-fixmenu"></label>
				</div>
				</td>
				</tr>
				
				<tr valign="top" class="menuTop" style="<?php echo ( !isset( $documentor_curr['fixmenu'] )  or $documentor_curr['fixmenu']=='0' ) ? 'display:none;' : ''; ?>">
				<th scope="row"><?php _e('Top Margin for Menu','documentor'); ?></th>
				<td>
					<input type="number" name="<?php echo $documentor_options;?>[menuTop]" id="menuTop" class="small-text" value="<?php echo esc_attr($documentor_curr['menuTop']); ?>" min="0" />&nbsp;<?php _e('px','documentor'); ?>
				</td>
				</tr>
				
				<tr valign="top">
					<?php
						//new field added in v1.1
						$documentor_curr['menu_position'] = isset($documentor_curr['menu_position']) ? $documentor_curr['menu_position'] : 'left'; 
					?>
					<th scope="row"><?php _e('Menu Position','documentor'); ?></th>
					<td>
						<select name="<?php echo $documentor_options;?>[menu_position]" >
							<option value="left" <?php if ($documentor_curr['menu_position'] == "left"){ echo "selected";}?> >Left</option>
							<option value="right" <?php if ($documentor_curr['menu_position'] == "right"){ echo "selected";}?> >Right</option>
						</select>
					</td>
				</tr>
				
				<tr valign="top" class="mtoggle-row" <?php echo $mtogglestyle; ?>>
					<th scope="row"><?php _e('Toggle child menu','documentor'); ?></th>
					<td>
					<div class="eb-switch eb-switchnone havemoreinfo">
						<input type="hidden" name="<?php echo $documentor_options;?>[togglemenu]" id="doc-enable-togglemenu" class="hidden_check" value="<?php echo esc_attr($documentor_curr['togglemenu']);?>">
						<input id="enable-togglemenu" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['togglemenu']); ?>>
						<label for="enable-togglemenu"></label>
					</div>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Extras on Section hover','documentor'); ?></th>
					<td>
						<div class="eb-switch eb-switchnone havemoreinfo">
							<input type="hidden" name="<?php echo $documentor_options;?>[iconscroll]" id="doc-enable-iconscroll" class="hidden_check" value="<?php echo esc_attr($documentor_curr['iconscroll']);?>">
							<input id="enable-iconscroll" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['iconscroll']); ?>>
							<label for="enable-iconscroll"></label>
						</div>
					</td>
				</tr>
				
				</table>
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				</div>

				</div> <!--Basic ends-->
				<div id="formating" class="doc-settingsdiv" >
				<div class="sub_settings toggle_settings">
				<h2 class="sub-heading"><?php _e('Formatting','documentor'); ?><span class="toggle_img"></span></h2> 
				
				<span scope="row" class="doc-settingtitle"><?php _e('Nav Menu Title','documentor'); ?></span>
				<table class="form-table settings-tbl"  >
				<tr valign="top" >
					<th scope="row" ><?php _e('Use theme default','documentor'); ?></th>
					<td>
					<div class="eb-switch eb-switchnone havemoreinfo">
						<input type="hidden" name="<?php echo $documentor_options;?>[navmenu_default]" id="navmenu-default" class="hidden_check" value="<?php echo esc_attr($documentor_curr['navmenu_default']);?>">
						<input id="navmenu-def" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['navmenu_default']); ?>>
						<label for="navmenu-def"></label>
					</div>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Color','documentor'); ?></th>
					<td><input type="text" name="<?php echo $documentor_options;?>[navmenu_color]" id="navmenu_color" value="<?php echo esc_attr($documentor_curr['navmenu_color']); ?>" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
			    </tr>
			    
				<tr valign="top">
				<th scope="row"><?php _e('Font','documentor'); ?></th>
				<td>
				<input type="hidden" value="navmenu_tfont" class="ftype_rname">
				<input type="hidden" value="navmenu_tfontg" class="ftype_gname">
				<input type="hidden" value="navmenu_custom" class="ftype_cname">
				<select name="<?php echo $documentor_options;?>[navt_font]" id="navt_font" class="main-font">
	
					<option value="regular" <?php selected( $documentor_curr['navt_font'], "regular" ); ?> > Regular Fonts </option>
					<option value="google" <?php selected( $documentor_curr['navt_font'], "google" ); ?> > Google Fonts </option>
					<option value="custom" <?php selected( $documentor_curr['navt_font'], "custom" ); ?> > Custom Fonts </option>
				</select>
				</td>
				</tr>

				<tr><td class="load-fontdiv" colspan="2"></td></tr>

				<tr valign="top">
				<th scope="row"><?php _e('Font Size','documentor'); ?></th>
				<td><input type="number" name="<?php echo $documentor_options;?>[navmenu_fsize]" id="navmenu_fsize" class="small-text" value="<?php echo esc_attr($documentor_curr['navmenu_fsize']); ?>" min="1" />&nbsp;<?php _e('px','documentor'); ?></td>
				</tr>

				<tr valign="top" class="font-style">
				<th scope="row"><?php _e('Font Style','documentor'); ?></th>
				<td><select name="<?php echo $documentor_options;?>[navmenu_fstyle]" id="navmenu_fstyle" class="font-style" >
				<option value="bold" <?php if ($documentor_curr['navmenu_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','documentor'); ?></option>
				<option value="bold italic" <?php if ($documentor_curr['navmenu_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','documentor'); ?></option>
				<option value="italic" <?php if ($documentor_curr['navmenu_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','documentor'); ?></option>
				<option value="normal" <?php if ($documentor_curr['navmenu_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','documentor'); ?></option>
				</select>
				</td>
				</tr>

				</table>
				
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>

				<span scope="row" class="doc-settingtitle" ><?php _e('Active Nav Menu Background','documentor'); ?></span>

				<table class="form-table settings-tbl"  >

				<tr valign="top">
				<th scope="row"><?php _e('Use theme default','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[actnavbg_default]" id="actnav-background" class="hidden_check" value="<?php echo esc_attr($documentor_curr['actnavbg_default']);?>">
					<input id="actnav-bg" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['actnavbg_default']); ?>>
					<label for="actnav-bg"></label>
				</div>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Color','documentor'); ?></th>
				<td><input type="text" name="<?php echo $documentor_options;?>[actnavbg_color]" id="actnavbg-color" value="<?php echo esc_attr($documentor_curr['actnavbg_color']); ?>" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
				</tr>

				</table>
				
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>

				<span scope="row" class="doc-settingtitle"><?php _e('Section Title','documentor'); ?></span>

				<table class="form-table settings-tbl"  >

				<tr valign="top">
				<th scope="row"><?php _e('Element','documentor'); ?>
				</th>
				<td><select name="<?php echo $documentor_options;?>[section_element]" >
				<option value="1" <?php if ($documentor_curr['section_element'] == "1"){ echo "selected";}?> >h1</option>
				<option value="2" <?php if ($documentor_curr['section_element'] == "2"){ echo "selected";}?> >h2</option>
				<option value="3" <?php if ($documentor_curr['section_element'] == "3"){ echo "selected";}?> >h3</option>
				<option value="4" <?php if ($documentor_curr['section_element'] == "4"){ echo "selected";}?> >h4</option>
				<option value="5" <?php if ($documentor_curr['section_element'] == "5"){ echo "selected";}?> >h5</option>
				<option value="6" <?php if ($documentor_curr['section_element'] == "6"){ echo "selected";}?> >h6</option>
				</select>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Use theme default','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[sectitle_default]" id="sectitle-default" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sectitle_default']);?>">
					<input id="sectitle-def" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sectitle_default']); ?>>
					<label for="sectitle-def"></label>
				</div>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Color','documentor'); ?></th>
				<td><input type="text" name="<?php echo $documentor_options;?>[sectitle_color]" id="sectitle-color" value="<?php echo esc_attr($documentor_curr['sectitle_color']); ?>" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Font','documentor'); ?></th>
				<td>
				<input type="hidden" value="sectitle_font" class="ftype_rname">
				<input type="hidden" value="sectitle_fontg" class="ftype_gname">
				<input type="hidden" value="sectitle_custom" class="ftype_cname">
				<select name="<?php echo $documentor_options;?>[sect_font]" id="sect_font" class="main-font">
					<option value="regular" <?php selected( $documentor_curr['sect_font'], "regular" ); ?> > Regular Fonts </option>
					<option value="google" <?php selected( $documentor_curr['sect_font'], "google" ); ?> > Google Fonts </option>
					<option value="custom" <?php selected( $documentor_curr['sect_font'], "custom" ); ?> > Custom Fonts </option>
				</select>
				</td>
				</tr>

				<tr><td class="load-fontdiv" colspan="2"></td></tr>

				<tr valign="top">
				<th scope="row"><?php _e('Font Size','documentor'); ?></th>
				<td><input type="number" name="<?php echo $documentor_options;?>[sectitle_fsize]" id="sectitle_fsize" class="small-text" value="<?php echo esc_attr($documentor_curr['sectitle_fsize']); ?>" min="1" />&nbsp;<?php _e('px','documentor'); ?></td>
				</tr>

				<tr valign="top" class="font-style">
				<th scope="row"><?php _e('Font Style','documentor'); ?></th>
				<td><select name="<?php echo $documentor_options;?>[sectitle_fstyle]" id="sectitle_fstyle" class="font-style" >
				<option value="bold" <?php if ($documentor_curr['sectitle_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','documentor'); ?></option>
				<option value="bold italic" <?php if ($documentor_curr['sectitle_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','documentor'); ?></option>
				<option value="italic" <?php if ($documentor_curr['sectitle_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','documentor'); ?></option>
				<option value="normal" <?php if ($documentor_curr['sectitle_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','documentor'); ?></option>
				</select>
				</td>
				</tr>

				</table>
				
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				
				<span scope="row" class="doc-settingtitle"><?php _e('Section Content','documentor'); ?></span>

				<table class="form-table settings-tbl"  >


				<tr valign="top">
				<th scope="row"><?php _e('Use theme default','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[seccont_default]" id="seccont-default" class="hidden_check" value="<?php echo esc_attr($documentor_curr['seccont_default']);?>">
					<input id="seccont-def" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['seccont_default']); ?>>
					<label for="seccont-def"></label>
				</div>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Color','documentor'); ?></th>
				<td><input type="text" name="<?php echo $documentor_options;?>[seccont_color]" id="seccont_color" value="<?php echo esc_attr($documentor_curr['seccont_color']); ?>" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Font','documentor'); ?></th>
				<td>
				<input type="hidden" value="seccont_font" class="ftype_rname">
				<input type="hidden" value="seccont_fontg" class="ftype_gname">
				<input type="hidden" value="seccont_custom" class="ftype_cname">
				<select name="<?php echo $documentor_options;?>[secc_font]" id="secc_font" class="main-font">
					<option value="regular" <?php selected( $documentor_curr['secc_font'], "regular" ); ?> > Regular Fonts </option>
					<option value="google" <?php selected( $documentor_curr['secc_font'], "google" ); ?> > Google Fonts </option>
					<option value="custom" <?php selected( $documentor_curr['secc_font'], "custom" ); ?> > Custom Fonts </option>
				</select>
				</td>
				</tr>

				<tr><td class="load-fontdiv" colspan="2"></td></tr>

				<tr valign="top">
				<th scope="row"><?php _e('Font Size','documentor'); ?></th>
				<td><input type="number" name="<?php echo $documentor_options;?>[seccont_fsize]" id="seccont-fsize" class="small-text" value="<?php echo esc_attr($documentor_curr['seccont_fsize']); ?>" min="1" />&nbsp;<?php _e('px','documentor'); ?></td>
				</tr>

				<tr valign="top" class="font-style">
				<th scope="row"><?php _e('Font Style','documentor'); ?></th>
				<td><select name="<?php echo $documentor_options;?>[seccont_fstyle]" id="seccont-fstyle" class="font-style" >
				<option value="bold" <?php if ($documentor_curr['seccont_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','documentor'); ?></option>
				<option value="bold italic" <?php if ($documentor_curr['seccont_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','documentor'); ?></option>
				<option value="italic" <?php if ($documentor_curr['seccont_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','documentor'); ?></option>
				<option value="normal" <?php if ($documentor_curr['seccont_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','documentor'); ?></option>
				</select>
				</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e('Last Updated Date','documentor'); ?></th>
					<td>
						<?php 
						//new field added in v1.1
						$documentor_curr['updated_date'] = isset( $documentor_curr['updated_date'] ) ? $documentor_curr['updated_date'] : 0;
						?>
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[updated_date]" id="sec_updated_date" class="hidden_check" value="<?php echo esc_attr($documentor_curr['updated_date']);?>">
							<input id="updated_date" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['updated_date']); ?>>
							<label for="updated_date"></label>
						</div>
					</td>
				</tr>

				</table>
				
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				
				<span scope="row" class="doc-settingtitle"><?php _e('Scrollbar','documentor'); ?></span>

				<table class="form-table settings-tbl"  >
					<?php 
						//new settings for scrollbar v1.1
						$scrollsize = isset( $documentor_curr['scroll_size'] ) ? $documentor_curr['scroll_size'] : 3;
						$scrollcolor = isset( $documentor_curr['scroll_color'] ) ? $documentor_curr['scroll_color'] : '#F45349';
						$scrollopacity = isset( $documentor_curr['scroll_opacity'] ) ? $documentor_curr['scroll_opacity'] : 0.4;
					?>
					<tr valign="top">
						<th scope="row"><?php _e('size','documentor'); ?></th>
						<td>
							<input type="number" min="0" class="small-text" name="<?php echo $documentor_options;?>[scroll_size]" id="scroll_size" value="<?php echo esc_attr($scrollsize);?>">
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Color','documentor'); ?></th>
						<td>
							<input type="text" name="<?php echo $documentor_options;?>[scroll_color]" id="scroll_color" value="<?php echo esc_attr($scrollcolor); ?>" class="wp-color-picker-field" data-default-color="#2c3e50" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Opacity','documentor'); ?></th>
						<td>
							<input type="number" class="small-text" name="<?php echo $documentor_options;?>[scroll_opacity]" id="scroll_opacity" value="<?php echo esc_attr($scrollopacity); ?>" min="0" max="1" step="any" />
						</td>
					</tr>
				</table>
				
				<p class="submit">
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				</div>

				</div> <!--Formatting -->
				<div id="advance-settings" class="doc-settingsdiv">
				<div class="sub_settings toggle_settings">
				<h2 class="sub-heading"><?php _e('Advanced Settings','documentor'); ?><span class="toggle_img"></span></h2> 

				<table class="form-table">

				<tr valign="top">
				<th scope="row"><?php _e('Load sections using AJAX','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[disable_ajax]" id="disable-ajax" class="hidden_check" value="<?php echo esc_attr($documentor_curr['disable_ajax']);?>">
					<input id="dsable-ajax" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['disable_ajax']); ?>>
					<label for="dsable-ajax"></label>
				</div>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Search Box','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="<?php echo $documentor_options;?>[search_box]" id="search-box" class="hidden_check" value="<?php echo esc_attr($documentor_curr['search_box']);?>">
					<input id="search_box" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['search_box']); ?>>
					<label for="search_box"></label>
				</div>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Buttons','documentor'); ?></th>
				<td>
				<fieldset>
					<div class="mdivsett">
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[button][1]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['button'][1]);?>">
							<input id="button-select1" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['button'][1]); ?>>
							<label for="button-select1"></label>
						</div>
						<label><?php _e('Link','documentor');?></label>	
					</div>
					<div class="mdivsett">
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[button][2]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['button'][2]);?>">
							<input id="button-select2" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['button'][2]); ?>>
							<label for="button-select2"></label>
						</div>
						<label><?php _e('Email','documentor');?></label>	
					</div>
					<div class="mdivsett">
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[button][3]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['button'][3]);?>">
							<input id="button-select3" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['button'][3]); ?>>
							<label for="button-select3"></label>
						</div>
						<label><?php _e('Save PDF','documentor');?></label>
						<a href="#doc-pdf-options" rel="leanModal" title="" style="margin-left: 15px;"><?php _e('Options','documentor');?></a>
					</div>
					<div class="mdivsett">
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[button][4]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['button'][4]);?>">
							<input id="button-select4" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['button'][4]); ?>>
							<label for="button-select4"></label>
						</div>
						<label><?php _e('Print','documentor');?></label>
						<a href="#doc-print-options" rel="leanModal" title="" style="margin-left: 15px;"><?php _e('Options','documentor');?></a>	
					</div>
				</fieldset>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Users can suggest Edits','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[suggest_edit]" id="suggest-edits" class="hidden_check" value="<?php echo esc_attr($documentor_curr['suggest_edit']);?>">
					<input id="suggest-edit" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['suggest_edit']); ?>>
					<label for="suggest-edit"></label>
				</div>
				<span class="doc-format"><a href="#format-suggestedit" rel="leanModal" title="Suggest Edit Format" ><?php _e('Format','documentor');?></a>
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><?php _e('Guide Manager','documentor'); ?></th>
				<td>
				<?php 
				$gmanager_arr = ( isset( $documentor_curr['guide'] ) && is_array( $documentor_curr['guide'] ) ) ?$documentor_curr['guide'] : array(); ?>
				<select name="<?php echo $documentor_options;?>[guide][]" id="documentor_guide_manager" multiple>
				<?php $users = array_merge( get_users('role=administrator'), get_users('role=editor'), get_users('role=author') );
				$i = 0;
				foreach( $users as $user ) { ?>
					<option value="<?php echo esc_attr($user->ID);?>" <?php if(in_array($user->ID,$gmanager_arr)){echo 'selected';} ?> ><?php echo $user->display_name; ?></option>
				<?php	
					$i++;
				 }
				?>
				</select>
				</td>
				</tr>

				<tr valign="top">
				<th scope="row"><?php _e('Visitor\'s Feedback','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[feedback]" id="visitor-feedback" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedback']);?>">
					<input id="visitors-feedback" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedback']); ?>>
					<label for="visitors-feedback"></label>
				</div>
				<span class="doc-format">
					<a href="#format-feedback" rel="leanModal" title="User Feedback Format" ><?php _e('Format','documentor');?></a>
				</span>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Feedback Count','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="<?php echo $documentor_options;?>[feedbackcnt]" id="visitor_feedbackcnt" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedbackcnt']);?>">
					<input id="visitors-feedbackcnt" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedbackcnt']); ?>>
					<label for="visitors-feedbackcnt"></label>
				</div>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row"><?php _e('Related','documentor'); ?></th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="<?php echo $documentor_options;?>[related_doc]" id="related-document" class="hidden_check" value="<?php echo esc_attr($documentor_curr['related_doc']);?>">
					<input id="related-doc" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['related_doc']); ?>>
					<label for="related-doc"></label>
				</div>
				</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('RTL Support','documentor'); ?></th>
					<td>
						<?php $documentor_curr['rtl_support'] = isset($documentor_curr['rtl_support']) ? $documentor_curr['rtl_support'] : '0'; ?>
						<div class="eb-switch eb-switchnone havemoreinfo">
							<input type="hidden" name="<?php echo $documentor_options;?>[rtl_support]" id="related-document" class="hidden_check" value="<?php echo esc_attr($documentor_curr['rtl_support']);?>">
							<input id="rtl_support" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['rtl_support']); ?>>
							<label for="rtl_support"></label>
						</div>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e('Back to Top button','documentor'); ?></th>
					<td>
						<?php $documentor_curr['scrolltop'] = isset($documentor_curr['scrolltop']) ? $documentor_curr['scrolltop'] : '1'; ?>
						<div class="eb-switch eb-switchnone havemoreinfo">
							<input type="hidden" name="<?php echo $documentor_options;?>[scrolltop]" id="related-document" class="hidden_check" value="<?php echo esc_attr($documentor_curr['scrolltop']);?>">
							<input id="scrolltop" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['scrolltop']); ?>>
							<label for="scrolltop"></label>
						</div>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php _e('Social Sharing','documentor'); ?></th>
					<td>
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[socialshare]" id="related-document" class="hidden_check" value="<?php echo esc_attr($documentor_curr['socialshare']);?>">
							<input id="socialshare" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['socialshare']); ?>>
							<label for="socialshare"></label>
						</div>
						<span class="doc-format">
							<a href="#format-social" rel="leanModal" title="Social Share Format" ><?php _e('Format','documentor');?></a>
						</span>
					</td>
				</tr>
				
				<tr valign="top" >
					<th scope="row" ><?php _e('Attach Product','documentor'); ?></th>
					<td>
						<div class="eb-switch eb-switchnone">
							<input type="hidden" name="<?php echo $documentor_options;?>[productdetails]" id="product_details" class="hidden_check" value="<?php echo esc_attr($documentor_curr['productdetails']);?>">
							<input id="productdetails" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['productdetails']); ?>>
							<label for="productdetails"></label>
						</div>
						<span class="doc-format">
							<a href="#attachproduct-settings" rel="leanModal" title="Attach Product Settings" ><?php _e('Options','documentor');?></a>
						</span>
					</td>
				</tr>
					    				
				</table>
				<div id="format-suggestedit" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Suggest Edit Format','documentor');?></div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Form','documentor');?></p>
							<a class="modal_close" href="#"></a>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Name','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[sedit_frmname]" id="documentor_sedit_frmname" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sedit_frmname']);?>">
								<input id="sedit_frmname" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sedit_frmname']); ?>>
								<label for="sedit_frmname"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Email','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[sedit_frmemail]" id="documentor_sedit_frmemail" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sedit_frmemail']);?>">
								<input id="sedit_frmemail" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sedit_frmemail']); ?>>
								<label for="sedit_frmemail"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Extra Input Fields','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[sedit_frminputs]" id="documentor_sedit_frminputs" placeholder="Enter Comma Seperated Values" class="sfrminput" value="<?php echo esc_attr($documentor_curr['sedit_frminputs']);?>">
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Message Box','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[sedit_frmmsgbox]" id="documentor_sedit_frmmsgbox" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sedit_frmmsgbox']);?>">
								<input id="sedit_frmmsgbox" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sedit_frmmsgbox']); ?>>
								<label for="sedit_frmmsgbox"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Captcha','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[sedit_frmcapcha]" id="documentor_sedit_frmcapcha" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sedit_frmcapcha']);?>">
								<input id="sedit_frmcapcha" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sedit_frmcapcha']); ?>>
								<label for="sedit_frmcapcha"></label>
							</div>
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Email','documentor');?></p>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Subject','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[sedit_frmsubject]" id="documentor_sedit_frmsubject" class="sfrminput" value="<?php echo esc_attr($documentor_curr['sedit_frmsubject']);?>">
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Thank You Message','documentor'); ?></label>
							<div class="msg">	
								<textarea rows="3" name="<?php echo $documentor_options;?>[sedit_thankyoumsg]" id="documentor_sedit_thankyoumsg"><?php echo $documentor_curr['sedit_thankyoumsg'];?></textarea>
							</div>
						</div>
						<div class="btn-fld">
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</div>
					</div>
				</div>
					
				<div id="format-feedback" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('User Feedback Format','documentor');?></div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Form','documentor');?></p>
							<a class="modal_close" href="#"></a>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Name','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[feedback_frmname]" id="documentor_feedback_frmname" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedback_frmname']);?>">
								<input id="feedback_frmname" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedback_frmname']); ?>>
								<label for="feedback_frmname"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Email','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[feedback_frmemail]" id="documentor_feedback_frmemail" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedback_frmemail']);?>">
								<input id="feedback_frmemail" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedback_frmemail']); ?>>
								<label for="feedback_frmemail"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Extra Input Fields','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[feedback_frminputs]" id="documentor_feedback_frminputs" placeholder="Enter Comma Seperated Values" class="sfrminput" value="<?php echo esc_attr($documentor_curr['feedback_frminputs']);?>">
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Feedback Text','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[feedback_frmtext]" id="documentor_feedback_frmtext" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedback_frmtext']);?>">
								<input id="feedback_frmtext" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedback_frmtext']); ?>>
								<label for="feedback_frmtext"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Captcha','documentor'); ?></label>
							<div class="eb-switch eb-switchl">
								<input type="hidden" name="<?php echo $documentor_options;?>[feedback_frmcapcha]" id="documentor_feedback_frmcapcha" class="hidden_check" value="<?php echo esc_attr($documentor_curr['feedback_frmcapcha']);?>">
								<input id="feedback_frmcapcha" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['feedback_frmcapcha']); ?>>
								<label for="feedback_frmcapcha"></label>
							</div>
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Email','documentor');?></p>
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Subject','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[feedback_frmsubject]" id="documentor_feedback_frmsubject" class="sfrminput" value="<?php echo esc_attr($documentor_curr['feedback_frmsubject']);?>">
						</div>
						<div class="txt-fld">
							<label for="" class="lbl"><?php _e('Thank You Message','documentor'); ?></label>
							<div class="msg">	
								<textarea rows="3" name="<?php echo $documentor_options;?>[feedback_thankyoumsg]" id="documentor_feedback_thankyoumsg"><?php echo $documentor_curr['feedback_thankyoumsg'];?></textarea>
							</div>
						</div>
						<div class="btn-fld">
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</div>
					</div>
				</div>	
				<div id="doc-pdf-options" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('PDF Options','documentor');?></div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Author','documentor');?></p>
							<a class="modal_close" href="#"></a>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Guide Subtitle','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[guide_subtitle]" value="<?php echo  esc_attr($documentor_curr['guide_subtitle']); ?>" />
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('PDF Header','documentor');?></p>
						</div>
						<?php
						//display this setting only if load section through ajax is disabled
						if( $documentor_curr['disable_ajax'] == 0 ) {
						?>
							<div class="txt-fld">
								<label for="name" class="lbl"><?php _e('Header on First Page','documentor'); ?></label>
								<div class="eb-switch eb-switchnone">
									<input type="hidden" name="<?php echo $documentor_options;?>[pdf_headerfirst]" id="doc_pdf_headerfirst" class="hidden_check" value="<?php echo esc_attr($documentor_curr['pdf_headerfirst']);?>">
									<input id="pdf_headerfirst" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['pdf_headerfirst']); ?>>
									<label for="pdf_headerfirst"></label>
								</div>
							</div>
						<?php }?>
						<div class="txt-fld">
							<label class="lbl"><?php _e('Logo','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[pdf_headerlogo]" class="pdf-headerlogo uploadimg_url" placeholder="<?php _e('Enter URL or upload image','documentor');?>" value="<?php echo  esc_attr($documentor_curr['pdf_headerlogo']); ?>" />  							
							<input  class="doc-image-uploadbtn" type="button" value="<?php _e('Upload','documentor');?>" /><span>
						</div>
						<div class="txt-fld">
							<label class="lbl"><?php _e('Logo Width','documentor'); ?></label>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_headerlogow]" value="<?php echo  esc_attr($documentor_curr['pdf_headerlogow']); ?>" placeholder="<?php _e('pixels','documentor');?>" /> 							
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Header text','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[pdf_headertitle]" value="<?php echo  esc_attr($documentor_curr['pdf_headertitle']); ?>" />
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Text Color','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[pdf_headercolor]" id="pdf_headercolor" value="<?php echo esc_attr($documentor_curr['pdf_headercolor']); ?>" class="wp-color-picker-field" data-default-color="#646464" />
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Header border','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[pdf_headerborder]" id="doc_pdf_headerborder" class="hidden_check" value="<?php echo esc_attr($documentor_curr['pdf_headerborder']);?>">
								<input id="pdf_headerborder" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['pdf_headerborder']); ?>>
								<label for="pdf_headerborder"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Border Color','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[pdf_headerbrcolor]" id="pdf_headerbrcolor" value="<?php echo esc_attr($documentor_curr['pdf_headerbrcolor']); ?>" class="wp-color-picker-field" data-default-color="#FFFFFF" />
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('PDF Footer','documentor');?></p>
						</div>
						<?php //display this setting only if load section through ajax is disabled
						if( $documentor_curr['disable_ajax'] == 0 ) {
						?>
							<div class="txt-fld">
								<label for="name" class="lbl"><?php _e('Footer on First Page','documentor'); ?></label>
								<div class="eb-switch eb-switchnone">
									<input type="hidden" name="<?php echo $documentor_options;?>[pdf_footerfirst]" id="doc_pdf_footerfirst" class="hidden_check" value="<?php echo esc_attr($documentor_curr['pdf_footerfirst']);?>">
									<input id="pdf_footerfirst" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['pdf_footerfirst']); ?>>
									<label for="pdf_footerfirst"></label>
								</div>
							</div>
						<?php }?>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Footer text','documentor'); ?></label>
							<input type="text" name="<?php echo $documentor_options;?>[pdf_footertxt]" value="<?php echo  esc_attr($documentor_curr['pdf_footertxt']); ?>" />
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('PDF Title','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdf_title_font]">
								<?php $currfont = (isset( $documentor_curr['pdf_title_font'] ) ) ? $documentor_curr['pdf_title_font']: 'helveticab';
								echo $this->get_pdf_fonts( $currfont ); ?>
							</select>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font Size','documentor'); ?></label>
							<?php $documentor_curr['pdf_title_fsize'] = ( isset( $documentor_curr['pdf_title_fsize'] ) ) ? $documentor_curr['pdf_title_fsize'] : '22'; ?>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_title_fsize]" value="<?php echo  esc_attr($documentor_curr['pdf_title_fsize']); ?>" />
						</div>
						
						<div id="format-header">
							<p class="format-heading"><?php _e('PDF Subtitle','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdf_subt_font]">
								<?php $currfont = (isset( $documentor_curr['pdf_subt_font'] ) ) ? $documentor_curr['pdf_subt_font']: 'helvetica';
								echo $this->get_pdf_fonts( $currfont ); ?>
							</select>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font Size','documentor'); ?></label>
							<?php $documentor_curr['pdf_subt_fsize'] = ( isset( $documentor_curr['pdf_subt_fsize'] ) ) ? $documentor_curr['pdf_subt_fsize'] : '14'; ?>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_subt_fsize]" value="<?php echo  esc_attr($documentor_curr['pdf_subt_fsize']); ?>" />
						</div>

						<div id="format-header">
							<p class="format-heading"><?php _e('TOC Menu Title','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdf_menut_font]">
								<?php 
								$currfont = (isset( $documentor_curr['pdf_menut_font'] ) ) ? $documentor_curr['pdf_menut_font']: 'helvetica';
								echo $this->get_pdf_fonts( $currfont ); ?>
							</select>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font Size','documentor'); ?></label>
							<?php $documentor_curr['pdf_menut_fsize'] = ( isset( $documentor_curr['pdf_menut_fsize'] ) ) ? $documentor_curr['pdf_menut_fsize'] : '12'; ?>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_menut_fsize]" value="<?php echo  esc_attr($documentor_curr['pdf_menut_fsize']); ?>" />
						</div>

						<div id="format-header">
							<p class="format-heading"><?php _e('Section Title','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdf_sect_font]">
								<?php 
								$currfont = (isset( $documentor_curr['pdf_sect_font'] ) ) ? $documentor_curr['pdf_sect_font']: 'helveticab';
								echo $this->get_pdf_fonts($currfont); ?>
							</select>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font Size','documentor'); ?></label>
							<?php $documentor_curr['pdf_sect_fsize'] = ( isset( $documentor_curr['pdf_sect_fsize'] ) ) ? $documentor_curr['pdf_sect_fsize'] : '14'; ?>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_sect_fsize]" value="<?php echo  esc_attr($documentor_curr['pdf_sect_fsize']); ?>" />
						</div>

						<div id="format-header">
							<p class="format-heading"><?php _e('Section Content','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdf_secc_font]">
								<?php 
								$currfont = (isset( $documentor_curr['pdf_secc_font'] ) ) ? $documentor_curr['pdf_secc_font']: 'helvetica';
								echo $this->get_pdf_fonts($currfont); ?>
							</select>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Font Size','documentor'); ?></label>
							<?php $documentor_curr['pdf_secc_fsize'] = ( isset( $documentor_curr['pdf_secc_fsize'] ) ) ? $documentor_curr['pdf_secc_fsize'] : '10'; ?>
							<input type="number" name="<?php echo $documentor_options;?>[pdf_secc_fsize]" value="<?php echo  esc_attr($documentor_curr['pdf_secc_fsize']); ?>" />
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Links','documentor'); ?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Links inside PDF','documentor'); ?></label>
							<select name="<?php echo $documentor_options;?>[pdflinks]" >
										<option value="0" <?php if ($documentor_curr['pdflinks'] == "0"){ echo "selected";}?> > Remove all links </option>
										<option value="1" <?php if ($documentor_curr['pdflinks'] == "1"){ echo "selected";}?> > Print the link as text </option>
										<option value="2" <?php if ($documentor_curr['pdflinks'] == "2"){ echo "selected";}?> > Keep the links clickable </option>
										
							</select>
						</div>
						<div class="btn-fld">
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</div>
					</div>
				</div>	
				
				
				<div id="doc-print-options" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Print Options','documentor');?></div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Use window print','documentor'); ?></label>
							<?php 
							//new field added in v1.1
							$documentor_curr['window_print'] = isset( $documentor_curr['window_print'] ) ? $documentor_curr['window_print'] : 0;
							?>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[window_print]" id="doc_window_print" class="hidden_check" value="<?php echo esc_attr($documentor_curr['window_print']);?>">
								<input id="window_print" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['window_print']); ?>>
								<label for="window_print"></label>
							</div>
						</div>
						<div class="btn-fld">
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</div>
					</div>
				</div>	
				<!-- options of social share buttons -->
				<div id="format-social" class="format-form"> 
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Social Share Options','documentor');?></div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Select Social buttons','documentor');?></p>
							<a class="modal_close" href="#"></a>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Facebook','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[socialbuttons][0]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['socialbuttons'][0]);?>">
								<input id="socialbuttons-select1" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['socialbuttons'][0]); ?>>
								<label for="socialbuttons-select1"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Twitter','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[socialbuttons][1]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['socialbuttons'][1]);?>">
								<input id="socialbuttons-select2" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['socialbuttons'][1]); ?>>
								<label for="socialbuttons-select2"></label>
							</div>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Google Plus','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[socialbuttons][2]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['socialbuttons'][2]);?>">
								<input id="socialbuttons-select3" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['socialbuttons'][2]); ?>>
								<label for="socialbuttons-select3"></label>
							</div>
							<?php if( !function_exists('curl_version') ) { ?>
								<label><?php _e("To get the count of Google Plus shares, please enable the curl extension of PHP","");?></label>
							<?php }?>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Pinterest','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[socialbuttons][3]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['socialbuttons'][3]);?>">
								<input id="socialbuttons-select4" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['socialbuttons'][3]); ?>>
								<label for="socialbuttons-select4"></label>
							</div>
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Select Format','documentor');?></p>
						</div>
						<div class="txt-fld">
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_style]" <?php checked("square",$documentor_curr['sbutton_style'] );?> value="square" >
								<img src="<?php echo DOCPRO_URLPATH.'core/images/square.png'; ?>">
							</label>
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_style]" <?php checked("round",$documentor_curr['sbutton_style'] );?> value="round" >
								<img src="<?php echo DOCPRO_URLPATH.'core/images/round.png'; ?>">
							</label>
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_style]" <?php checked("squarecount",$documentor_curr['sbutton_style'] );?> value="squarecount" >
								<img src="<?php echo DOCPRO_URLPATH.'core/images/squarecount.png'; ?>">
							</label>
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_style]" <?php checked("squareround",$documentor_curr['sbutton_style'] );?> value="squareround" >
								<img src="<?php echo DOCPRO_URLPATH.'core/images/squareround.png'; ?>">
							</label>
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Display Share Count','documentor');?></p>
						</div>
						<div class="txt-fld">
							<label for="name" class="lbl"><?php _e('Share Count','documentor'); ?></label>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="<?php echo $documentor_options;?>[sharecount]" class="hidden_check" value="<?php echo esc_attr($documentor_curr['sharecount']);?>">
								<input id="sharecount" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['sharecount']); ?>>
								<label for="sharecount"></label>
							</div>
						</div>
						<div id="format-header">
							<p class="format-heading"><?php _e('Position','documentor');?></p>
						</div>
						<div class="txt-fld">
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_position]" <?php checked("top",$documentor_curr['sbutton_position'] );?> value="top" ><?php _e('Top','documentor');?>
							</label>
							<label>
								<input type="radio" name="<?php echo $documentor_options;?>[sbutton_position]" <?php checked("bottom",$documentor_curr['sbutton_position'] );?> value="bottom" style="margin-left: 20px;"><?php _e('Bottom','documentor');?>
							</label>
						</div>
						<div class="btn-fld">
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</div>
					</div>
				</div>
				<!--Indexing Formats -->
				
				<div id="format-index" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Index Formatting','documentor');?> &nbsp; <small><?php _e('(Only applied when AJAX loading is disabled)','documentor');?></small></div>
						
						<table class="form-table settings-tbl">	
							<tr valign="top">
								<th scope="row"><?php _e('Parent Index Format','documentor'); ?></th>
								<td>
									<select name="<?php echo $documentor_options;?>[pif]" >
										<option value="decimal" <?php if ($documentor_curr['pif'] == "decimal"){ echo "selected";}?> >Decimal</option>
										<option value="decimal-leading-zero" <?php if ($documentor_curr['pif'] == "decimal-leading-zero"){ echo "selected";}?> >Decimal leading zero</option>
										<option value="lower-roman" <?php if ($documentor_curr['pif'] == "lower-roman"){ echo "selected";}?> >Lower Roman</option>
										<option value="upper-roman" <?php if ($documentor_curr['pif'] == "upper-roman"){ echo "selected";}?> >Upper Roman</option>
										<option value="lower-alpha" <?php if ($documentor_curr['pif'] == "lower-alpha"){ echo "selected";}?> >Lower Alphabets</option>
										<option value="upper-alpha" <?php if ($documentor_curr['pif'] == "upper-alpha"){ echo "selected";}?> >Upper Alphabets</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e('Child Index Format','documentor'); ?></th>
								<td>
									<select name="<?php echo $documentor_options;?>[cif]" >
										<option value="decimal" <?php if ($documentor_curr['cif'] == "decimal"){ echo "selected";}?> >Decimal</option>
										<option value="decimal-leading-zero" <?php if ($documentor_curr['cif'] == "decimal-leading-zero"){ echo "selected";}?> >Decimal leading zero</option>
										<option value="lower-roman" <?php if ($documentor_curr['cif'] == "lower-roman"){ echo "selected";}?> >Lower Roman</option>
										<option value="upper-roman" <?php if ($documentor_curr['cif'] == "upper-roman"){ echo "selected";}?> >Upper Roman</option>
										<option value="lower-alpha" <?php if ($documentor_curr['cif'] == "lower-alpha"){ echo "selected";}?> >Lower Alphabets</option>
										<option value="upper-alpha" <?php if ($documentor_curr['cif'] == "upper-alpha"){ echo "selected";}?> >Upper Alphabets</option>
									</select>
								</td>
							</tr>
						</table>
						<p>
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</p>
					</div>
				</div>
							
				
				<!-- Guide title options -->
				<div id="options-guidetitle" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Guide Title Formatting','documentor');?></div>
						<table class="form-table settings-tbl">	
							<tr valign="top">
								<th scope="row"><?php _e('Element','documentor'); ?></th>
								<td>
									<select name="<?php echo $documentor_options;?>[guidet_element]" >
										<option value="1" <?php if ($documentor_curr['guidet_element'] == "1"){ echo "selected";}?> >h1</option>
										<option value="2" <?php if ($documentor_curr['guidet_element'] == "2"){ echo "selected";}?> >h2</option>
										<option value="3" <?php if ($documentor_curr['guidet_element'] == "3"){ echo "selected";}?> >h3</option>
										<option value="4" <?php if ($documentor_curr['guidet_element'] == "4"){ echo "selected";}?> >h4</option>
										<option value="5" <?php if ($documentor_curr['guidet_element'] == "5"){ echo "selected";}?> >h5</option>
										<option value="6" <?php if ($documentor_curr['guidet_element'] == "6"){ echo "selected";}?> >h6</option>
									</select>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row"><?php _e('Use theme default','documentor'); ?></th>
								<td>
									<div class="eb-switch eb-switchnone havemoreinfo">
										<input type="hidden" name="<?php echo $documentor_options;?>[guidet_default]" id="guidet-default" class="hidden_check" value="<?php echo esc_attr($documentor_curr['guidet_default']);?>">
										<input id="guidet-def" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked('1', $documentor_curr['guidet_default']); ?>>
										<label for="guidet-def"></label>
									</div>
								</td>
							</tr>				
							<tr valign="top">
								<th scope="row"><?php _e('Color','documentor'); ?></th>
								<td>
									<input type="text" name="<?php echo $documentor_options;?>[guidet_color]" id="guidet_color" value="<?php echo esc_attr($documentor_curr['guidet_color']); ?>" class="wp-color-picker-field" data-default-color="#D8E7EE" />
								</td>
						   	</tr>
						    
							<tr valign="top">
								<th scope="row"><?php _e('Font','documentor'); ?></th>
								<td>
									<input type="hidden" value="guidetitle_font" class="ftype_rname">
									<input type="hidden" value="guidet_fontg" class="ftype_gname">
									<input type="hidden" value="guidet_custom" class="ftype_cname">
									<select name="<?php echo $documentor_options;?>[guidet_font]" id="guidet_font" class="main-font">
	
										<option value="regular" <?php selected( $documentor_curr['guidet_font'], "regular" ); ?> > Regular Fonts </option>
										<option value="google" <?php selected( $documentor_curr['guidet_font'], "google" ); ?> > Google Fonts </option>
										<option value="custom" <?php selected( $documentor_curr['guidet_font'], "custom" ); ?> > Custom Fonts </option>
									</select>
								</td>
							</tr>

							<tr><td class="load-fontdiv" colspan="2"></td></tr>

							<tr valign="top">
							<th scope="row"><?php _e('Font Size','documentor'); ?></th>
							<td><input type="number" name="<?php echo $documentor_options;?>[guidet_fsize]" id="guidet_fsize" class="small-text" value="<?php echo esc_attr($documentor_curr['guidet_fsize']); ?>" min="1" />&nbsp;<?php _e('px','documentor'); ?></td>
							</tr>

							<tr valign="top" class="font-style">
								<th scope="row"><?php _e('Font Style','documentor'); ?></th>
								<td>
									<select name="<?php echo $documentor_options;?>[guidet_fstyle]" id="guidet_fstyle" class="font-style" >
									<option value="bold" <?php if ($documentor_curr['guidet_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','documentor'); ?></option>
									<option value="bold italic" <?php if ($documentor_curr['guidet_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','documentor'); ?></option>
									<option value="italic" <?php if ($documentor_curr['guidet_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','documentor'); ?></option>
									<option value="normal" <?php if ($documentor_curr['guidet_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','documentor'); ?></option>
									</select>
								</td>
							</tr>
						</table>
						<p>
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</p>
					</div>
				</div>
				<!-- Attach Product options -->
				<div id="attachproduct-settings" class="format-form">
					<div id="format-ct">
						<div class="frm-heading"><?php _e('Guide Title Formatting','documentor');?></div>
						<table class="form-table">	
							<tr valign="top">
								<th scope="row"><?php _e('Product Name','documentor'); ?></th>
								<td>
									<input type="text" name="<?php echo $documentor_options;?>[prodname]" value="<?php echo esc_attr($documentor_curr['prodname']); ?>" />
								</td>
						    	</tr>
						    	
						    	<tr valign="top">
								<th scope="row"><?php _e('Product Version','documentor'); ?></th>
								<td>
									<input type="number" name="<?php echo $documentor_options;?>[prodversion]" value="<?php echo esc_attr($documentor_curr['prodversion']); ?>" min="0" step="any" />
								</td>
						    	</tr>

							<tr valign="top">
								<th scope="row"><?php _e('Product Link','documentor'); ?></th>
								<td>
									<input type="text" name="<?php echo $documentor_options;?>[prodlink]" value="<?php echo esc_attr($documentor_curr['prodlink']); ?>" />
								</td>
						    	</tr>
						    	
						    	<tr valign="top">
								<th scope="row"><?php _e('Product Image','documentor'); ?></th>
								<td>
									<input type="text" name="<?php echo $documentor_options;?>[prodimg]" class="product-img  uploadimg_url" placeholder="<?php _e('Enter URL or upload image','documentor');?>" value="<?php echo  esc_attr($documentor_curr['prodimg']); ?>" />  							
									<input  class="doc-image-uploadbtn" type="button" value="<?php _e('Upload','documentor');?>" />
								</td>
							</tr>
						</table>
						<p>
							<input type="submit" name="save-settings" class="button-primary" value="Save">
						</p>
					</div>
				</div>
				
				<input type="hidden" name="guidename" class="guidename" value="<?php echo esc_attr($this->title);?>">
				<p class="submit">
				<input type="hidden" name="hidden_urlpage" class="documentor_urlpage" value="<?php echo esc_attr($_GET['page']);?>" />
				<input type="hidden" name="documentor-settings-nonce" value="<?php echo wp_create_nonce( 'documentor-settings-nonce' ); ?>" />
				<input type="submit" name="save-settings" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				</div>
				
				</form>
					
				</div> <!--advance settings -->
				<?php
				//added
				?>
				</div> <!--tab group-2 ends -->
				<?php } // if tab is 1
				else if( isset( $tabindex ) && $tabindex == 'embedcode' ) { ?>
		
	
				<div id="options-group-3" class="group embedcode">
				<table class="form-table" id="embedcode">
				<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="docsid" />
				<?php

				if ( isset($this->docid ) ) {
					$doc_id = $this->docid;
				}
				?>
					<tr valign="top">
						<th scope="row"><?php _e('Shortcode','documentor'); ?></th>
						<td>
						<div><code><?php echo '[documentor '.$doc_id.']' ?></code></div>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e('Template Tag','documentor'); ?></th>
						<td>
						<div> <?php echo "<code>&lt;?php if(function_exists('get_documentor')){ get_documentor('".$doc_id."'); }?&gt;</code>"; ?></div>
						</td>
					</tr>
				</table>

				</div> <!--tab group-3 ends -->
				<?php } //if tab is 2 
					else if( isset( $tabindex ) && $tabindex == 'related' ) {				
				?>
	
				<div id="options-group-4" class="group relateddocs documentor-settings">
				<table class="form-table" id="relateddocs">
					<form name="doc-related" method="post">
					<input type="hidden" value="<?php echo esc_attr($this->docid); ?>" name="doc_id" />
					<?php
						$guide = $this->get_guide( $this->docid );
						$value = "Relevant Links";
						if( !empty( $guide->rel_title ) ) {
							$value = $guide->rel_title;							
						}
						
						
					?>
					<tr valign="top">
						<td scope="row"><strong><?php _e('Title','documentor'); ?></strong></td>
					</tr>
					<tr valign="top">
						<td><input type="text" name="related_title" value="<?php echo esc_attr($value);?>" /></td>
					</tr>

					<tr valign="top">
					<?php
					$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
					?>
					<td><select name="related_menu" style="width: 25%;">
					<option value="0">Select related menu</option>
					<?php foreach ( $menus as $menu ):
				         
					?>
				  		<option value="<?php echo esc_attr($menu->term_id);?>" <?php if( $guide->rel_id != 0 ) { selected( $guide->rel_id, $menu->term_id); } ?> ><?php echo $menu->name; ?></option> 
					<?php endforeach; ?>	
					</select></td>	
					</tr>
					<tr>
						<td>
							<input type="hidden" name="guidename" class="guidename" value="<?php echo esc_attr($guide->doc_title);?>" />
							
							<input type="hidden" name="docid" value="<?php echo esc_attr($guide->doc_id);?>" />
							<input type="submit" class="button-primary" name="save_related" value="Save" />
						</td>
					</tr>
					</form>
				</table>

				</div> <!--tab group-4 ends -->
					
				<?php
				}//if tab is 3
		} //function admin_view ends		
		
		
		//Prashant
		public static function doc_show_posts() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			global $paged,$wpdb,$post; 
			$pages = '';
			$paged = isset($_POST['paged'])?intval($_POST['paged']):'';
			$post_type = isset($_POST['post_type'])?sanitize_text_field($_POST['post_type']):'';
			$docid = isset($_POST['docid'])?intval($_POST['docid']):'';
			$stext = isset($_POST['search_text'])?sanitize_text_field($_POST['search_text']):'';
			$range = 10;
			$html = '';
			$showitems = ($range * 2)+1; 
			if(empty($paged)) $paged = 1;
			$sec = new DocumentorSection();
			$pidarr = $sec->get_addedposts( $docid );
			if( count( $pidarr ) > 0 ) {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page'=>10,	
					'post_status'   => 'publish',
					'paged'=>$paged,
					's'=>$stext,
					'post__not_in' => $pidarr
				);
			} else {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page'=>10,	
					'post_status'   => 'publish',
					'paged'=>$paged,
					's'=>$stext,
				);
			}
			$the_query = new WP_Query( $args );
			$i=0;
			// The Loop
			if ( $the_query->have_posts() ) {
				$html .= '<div style="margin-left: 20px;" >';
				$html .= '<h3 class="nav-tab-wrapper p-tabs">'; 
						  $tabnm = ( $post_type == 'post' || $post_type == 'page' ) ? $post_type.'s' : $post_type;
						  $html .= '<a id="recent-tabcontent-tab" class="nav-tab recent-tabcontent-tab" title="Recent '.$tabnm.'" href="#recent-tabcontent">Recent '.$tabnm.'</a> 
						  <a id="search-tabcontent-tab" class="nav-tab search-tabcontent-tab" title="Search" href="#search-tabcontent">Search</a>
					</h3>';
				$html .= '<!--<form name="eb-wp-posts" id="eb-wp-posts" method="post" >-->
					<div id="recent-tabcontent" class="pgroup recent-tabcontent">
					';
				$html .= '<table class="wp-list-table widefat sliders" >';
				$html .= '<col width="10%">
					<col width="70%">
					<col width="20%">
						<thead>
						<tr>
							<th class="docpost-id">'. __('ID','documentor').'</th>
							<th class="docpost-title">'. __('Name','documentor').'</th>	
							<th class="docpost-editlnk">'. __('Edit Link','documentor').'</th>
						</tr>
						</thead>';
				
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$i++;
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="post_id[]" value="'.esc_attr(get_the_ID()).'"></td>';
					$html .= '<td>' . get_the_title() . '</td>';
					if($post_type == 'attachment' ) {
						$html .= '<td> <img src="'. wp_get_attachment_url(  ).'" width="50" height="30" /> </td>';
					}
					$editlink = '';
					if( post_type_exists($post_type) ) { 
						if( current_user_can('edit_post', get_the_ID()) ) {
							$edtlink = get_edit_post_link(get_the_ID());
							$editlink = '<a href="'.$edtlink.'" target="_blank" class="section-editlink">'. __('Edit','documentor').'</a>';
						}
					}
					$html .= '<td>'.$editlink.'</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				if($pages == '') {
					$pages = $the_query->max_num_pages;
					if(!$pages) {
						$pages = 1;
					}
				}  

				if(1 != $pages)
				{
					if($paged > 1 ) $prev = ($paged - 1); else $prev = 1;
					$html .= "<div class=\"eb-cs-pagination\"><span>". __('Page','documentor')." ".$paged.__('of','documentor')." ".$pages."</span>";
					$html .= "<a id='1' class='pageclk' >&laquo; ".__('First','documentor')."</a>";
					$html .= "<a id='".$prev."' class='pageclk' >&lsaquo; ".__('Previous','documentor')."</a>";

					for ($i=1; $i <= $pages; $i++) {
						if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
							$html .= ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a id=\"$i\" class=\"inactive pageclk\">".$i."</a>";
						}
					}
					if( $paged + 1 > $pages ) $nextpg = 1;
					else $nextpg = $paged + 1;
					$html .= "<a id=\"".$nextpg ."\" class='pageclk' >".__('Next','documentor')." &rsaquo;</a>"; 
					$html .= "<a id='".$pages."' class='pageclk' >".__('Last','documentor')." &raquo;</a>";
					$html .= "</div>\n";
				}
				$html .= "<p><input type='submit' name='add_posts' value='".__('Insert','documentor')."' class='btn_save add_posts' /></p>\n";
				$html .= '<input type="hidden" name="docid" value="'.esc_attr($docid).'" />';
				$html .= '<input type="hidden" name="post_type" class="post_type" value="'.esc_attr($post_type).'" />';
				$html .= '</div>';
				$html .= '<div id="search-tabcontent" class="pgroup search-tabcontent">';
				$html .= '<input type="text" name="search-input" class="search-input" placeholder="'.__('Enter search text','documentor').'" />';
				$html .= '<div class="load-searchresults"></div><!--</form>--></div>';
				echo $html;
				/* Restore original Post Data */
				wp_reset_postdata();
			} else {
				_e('No entries found','documentor');
			}
			die();
		}
		//show search results of page/posts
		public static function show_search_results() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			global $paged,$wpdb,$post; 
			$pages = '';
			$paged = isset($_POST['paged'])?intval($_POST['paged']):'';
			$post_type = isset($_POST['post_type'])?sanitize_text_field($_POST['post_type']):'';
			$docid = isset($_POST['docid'])?intval($_POST['docid']):'';
			$stext = isset($_POST['search_text'])?sanitize_text_field($_POST['search_text']):'';
			$range = 10;
			$html = '';
			$showitems = ($range * 2)+1; 
			if(empty($paged)) $paged = 1;
			$args = array(
				'post_type' => $post_type,
				'posts_per_page'=>10,	
				'post_status'   => 'publish',
				'paged'=>$paged,
				's'=>$stext
			);
			$the_query = new WP_Query( $args );
			$i=0;
			// The Loop
			if ( $the_query->have_posts() ) {
				$html .= '<table class="wp-list-table widefat sliders" >';
				$html .= '<col width="10%">
					<col width="70%">
					<col width="20%">
						<thead>
						<tr>
							<th class="docpost-id">'. __('ID','documentor').'</th>
							<th class="docpost-title">'. __('Name','documentor').'</th>	
							<th class="docpost-editlnk">'. __('Edit Link','documentor').'</th>
						</tr>
						</thead>';
				
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$i++;
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="post_id[]" value="'.esc_attr(get_the_ID()).'"></td>';
					$html .= '<td>' . get_the_title() . '</td>';
					if($post_type == 'attachment' ) {
						$html .= '<td> <img src="'. wp_get_attachment_url(  ).'" width="50" height="30" /> </td>';
					}
					$editlink = '';
					if( post_type_exists($post_type) ) { 
						if( current_user_can('edit_post', get_the_ID()) ) {
							$edtlink = get_edit_post_link(get_the_ID());
							$editlink = '<a href="'.$edtlink.'" target="_blank" class="section-editlink">'. __('Edit','documentor').'</a>';
						}
					}
					$html .= '<td>'.$editlink.'</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
				if($pages == '') {
					$pages = $the_query->max_num_pages;
					if(!$pages) {
						$pages = 1;
					}
				}  

				if(1 != $pages) {
					if($paged > 1 ) $prev = ($paged - 1); else $prev = 1;
					$html .= "<div class=\"eb-cs-pagination\"><span>".__('Page','documentor')." ".$paged." ".__('of','documentor')." ".$pages."</span>";
					$html .= "<a id='1' class='pageclk-search' >&laquo; ".__('First','documentor')."</a>";
					$html .= "<a id='".$prev."' class='pageclk-search' >&lsaquo; ".__('Previous','documentor')."</a>";


					for ($i=1; $i <= $pages; $i++) {
						if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
							$html .= ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a id=\"$i\" class=\"inactive pageclk-search\">".$i."</a>";
						}
					}
					if( $paged + 1 > $pages ) $nextpg = 1;
					else $nextpg = $paged + 1;
					$html .= "<a id=\"".$nextpg ."\" class='pageclk-search' >".__('Next','documentor')." &rsaquo;</a>"; 
					$html .= "<a id='".$pages."' class='pageclk-search' >".__('Last','documentor')." &raquo;</a>";
					$html .= "</div>\n";
				}
				$html .= "<p><input type='submit' name='add_posts' value='".__('Insert','documentor')."' class='btn_save add_posts' /></p>\n";
				echo $html;
			} else {
				_e('No entries found','documentor');
			}	
			die();
		}
		function populate_documentor_current( $documentor_curr ) {
			$doc = new Documentor();
			$default_documentor_settings = $doc->default_documentor_settings;
			foreach( $default_documentor_settings as $key => $value ){
				if( !isset( $documentor_curr[$key] ) ) $documentor_curr[$key] = $value;
			}
			return $documentor_curr;
		}
		//load preview at admin panel
		public static function load_preview() {
			$html = '';
			$docid = isset( $_POST['docid'] ) ? intval($_POST['docid']) : '';
			$guide = new DocumentorGuide( $docid );
			$settings = $guide->get_settings();
			$stylepath = 'skins/'.$settings["skin"].'/style.css';
			$printstyle = 'skins/'.$settings["skin"].'/print_style.css';
			$jspath = 'core/js/documentor.js';
			$printjs = 'core/js/jQuery.print.js';
			if( !empty( $docid ) ) {
				$html.="<link rel='stylesheet' id='doc_".$settings["skin"]."_css-css'  href='".Documentor::documentor_plugin_url( $stylepath )."' type='text/css' media='all' /><link rel='stylesheet' id='doc_".$settings["skin"]."_print-css'  href='".Documentor::documentor_plugin_url( $printstyle )."' type='text/css' media='print' />";
				$html.="<script type='text/javascript' src='".Documentor::documentor_plugin_url( $jspath )."'></script>";
				$html.="<script type='text/javascript' src='".Documentor::documentor_plugin_url( $printjs )."'></script>";
				$html.="<div class='doc-preview-msg'>".__('This is a preview of your document. Features like print, scroll to particular section, animations, search are not available in Preview. Once you embed the document on front-end, those effects will be functional!','documentor')."</div>";
				$html.=do_shortcode('[documentor '.$docid.']');
			}
			echo $html;
			die();
		}
		//export document
		function export() {
			// Send the headers
			@ob_end_clean();
			$doctitle = $this->title; 
			$sections_order = $this->sections_order;
			$id = $this->docid;
			$filename = sanitize_file_name( strtolower($doctitle.'-'.$id.'.xml'));
			$settings = $this->get_settings();
			$sections = $this->get_sections();
			if( count( $settings ) > 0 ) {
				header("Content-type: text/xml");
				header('Content-Disposition: attachment; filename='.$filename);
				
				$document = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><documentor></documentor>");
				
				$document->addChild('title', htmlspecialchars(html_entity_decode($doctitle, ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8'));
				$document->addChild('sections_order', $sections_order);

				$settingstag = $document->addChild('settings');
								
				foreach( $settings as $key=>$value ) {
					if(!is_array($value)){
						if( isset( $value ) && !empty( $value ) ) {
							$settingstag->addChild($key, $value);
						}
					} else {
						if($value) {
							$valhtml = '';$j = 0;
							foreach($value as $v){
								if($j>0) $valhtml.="|";
								$valhtml.=$v;
								$j++;
							}
						}
						$settingstag->addChild($key, $valhtml);
					}
				}
				$allsections = $document->addChild('sections');
					foreach( $sections as $section ) {
						$postid = $section->post_id;
						$postdata = get_post( $postid );
						if( $postdata != NULL ) {
							$ptitle = $postdata->post_title;
							$mtitle = get_post_meta( $postid, '_documentor_menutitle', true );
							$sectitle = get_post_meta( $postid, '_documentor_sectiontitle', true );
							$content = $postdata->post_content;
							$ptype = ( get_post_type($postid) != NULL ) ? get_post_type($postid): '';
							$items = $allsections->addChild('item');
							$items->addChild('post_title', htmlspecialchars(html_entity_decode($ptitle, ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8'));
							//$content = htmlspecialchars( $content, ENT_QUOTES );
							$items->addChild('post_content', htmlspecialchars(html_entity_decode($content, ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8'));
							$items->addChild('post_type', $ptype);
							$items->addChild('order', $section->sec_id);
							$items->addChild('menu_title', htmlspecialchars(html_entity_decode($mtitle, ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8'));
							$items->addChild('section_title', htmlspecialchars(html_entity_decode($sectitle, ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8'));
							$items->addChild('slug', $section->slug);
						}
					}
				print($document->asXML());
				exit();
			}
		}
		public function hex2rgb($hex) {
			$hex = str_replace("#", "", $hex);
			if(strlen($hex) == 3) {
				$r = hexdec(substr($hex,0,1).substr($hex,0,1));
				$g = hexdec(substr($hex,1,1).substr($hex,1,1));
				$b = hexdec(substr($hex,2,1).substr($hex,2,1));
			} else {
				$r = hexdec(substr($hex,0,2));
				$g = hexdec(substr($hex,2,2));
				$b = hexdec(substr($hex,4,2));
			}
			$rgb = array($r, $g, $b);
			return $rgb; // returns an array with the rgb values
		}
		//pdf
		public static function save_pdf() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			//check_ajax_referer( 'documentor-pdf-nonce', 'pdf_nonce' );
			$doc_id = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			if( !empty( $doc_id ) ) {
				require_once (dirname (__FILE__) . '/includes/tcpdf/tcpdf.php');	
				require_once (dirname (__FILE__) . '/includes/tcpdf/config/tcpdf_config.php');
				require_once (dirname (__FILE__) . '/includes/tcpdf/documentor_tcpdf.php');
				$doc = new DocumentorGuide( $doc_id );
				$guide = $doc->get_guide( $doc_id );
				$settings = json_decode($guide->settings, true);
				$sections = $doc->get_sections();
				//$guide_sutitle = $settings['guide_subtitle'];
				$guide_sutitle	= html_entity_decode($settings['guide_subtitle'], ENT_QUOTES, "UTF-8");
				$pdf = new DOCTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				// set default header data
				$headertitle = $hlogo = $hlogow = '';
				$header_txtcolor = array(100,100,100);
				$header_brcolor = array(255,255,255);
				$pdfHeaderString = $doc->title;	
				$pdf->headeronfirstpg = 0;
				//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
				if( isset( $settings['pdf_headertitle'] ) && !empty( $settings['pdf_headertitle'] ) ) {
					$headertitle = $settings['pdf_headertitle'];
				}
				if( isset( $settings['pdf_headercolor'] ) && !empty( $settings['pdf_headercolor'] ) ) {
					$header_txtcolor = $doc->hex2rgb( $settings['pdf_headercolor'] );
				}
				if( isset( $settings['pdf_headerborder'] ) && $settings['pdf_headerborder'] == '1' ) {
					if( isset( $settings['pdf_headerbrcolor'] ) )
						$header_brcolor = $doc->hex2rgb( $settings['pdf_headerbrcolor'] );
				}
				if( isset( $settings['pdf_headerlogo'] ) && !empty($settings['pdf_headerlogo']) ) {
					$hlogo = $settings['pdf_headerlogo'];
				}
				if( isset( $settings['pdf_headerlogow'] ) && !empty($settings['pdf_headerlogow']) ) {
					$hlogow = $settings['pdf_headerlogow']*0.26; //logo width is in mm. covert pixels to mm ( 1 px = 0.264583 mm )
				}
				if( isset( $settings['pdf_headerfirst'] ) && $settings['pdf_headerfirst'] == '1' ) {
					$pdf->headeronfirstpg = 1;
				}
				$pdf->SetHeaderData($hlogo, $hlogow, $headertitle, $pdfHeaderString,$header_txtcolor, $header_brcolor);
				//set footer data
				$pdf->footertxt = '';
				$pdf->footeronfirstpg = 0;
				if( isset( $settings['pdf_footertxt'] ) && !empty( $settings['pdf_footertxt'] ) ) {
					$pdf->footertxt = $settings['pdf_footertxt'];
				}
				if( isset( $settings['pdf_footerfirst'] ) && $settings['pdf_footerfirst'] == '1' ) {
					$pdf->footeronfirstpg = 1;
				}
				$pdf->setFooterData();

				// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
				// set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				// set image scale factor //1.4
				$pdf->setImageScale(1.53);
				// set default font subsetting mode
				$pdf->setFontSubsetting(true);

				//Add front page
				$pdf->AddPage();
				//Document title
				$tstyle = '';
				$tfont = $settings['pdf_title_font'];
				$tfontsize = $settings['pdf_title_fsize'];
				if( substr($tfont, -1) == 'b' ) {
					$tstyle = 'B'; 
					$tfont = substr($tfont,0,-1);
				}
				else if( substr($tfont, -1) == 'i' ) {
					$tstyle = 'I';
					$tfont = substr($tfont,0,-1);
				}
				else if( substr($tfont, -2) == 'bi' ) {
					$tstyle = 'BI';
					$tfont = substr($tfont,0,-2);
				}
				$pdf->SetFont( $tfont, $tstyle, $tfontsize );
				$pdf->Ln( 100 );
				$pdf->Cell( 0, 15, $guide->doc_title, 0, 0, 'R' );
				//Document sub-title
				$subtstyle = '';
				$subtfont = $settings['pdf_subt_font'];
				$subtfontsize = $settings['pdf_subt_fsize'];
				if( substr($subtfont, -1) == 'b' ) {
					$subtstyle = 'B'; 
					$subtfont = substr($subtfont,0,-1);
				}
				else if( substr($subtfont, -1) == 'i' ) {
					$subtstyle = 'I';
					$subtfont = substr($subtfont,0,-1);
				}
				else if( substr($subtfont, -2) == 'bi' ) {
					$subtstyle = 'BI';
					$subtfont = substr($subtfont,0,-2);	
				}
				$pdf->SetFont( $subtfont, $subtstyle, $subtfontsize );
				$pdf->Ln( 12 );
				$pdf->Cell( 0, 15, $guide_sutitle, 0, 0, 'R' );
				// set text shadow effect
				//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
				$obj = $guide->sections_order;
				$html = '';
				//Add content to PDF
				$pdf->AddPage();
				if( !empty( $obj ) ) {
					$jsonObj = json_decode( $obj );
					$i = 0;
					$cnt = count($jsonObj);
					foreach( $jsonObj as $jobj ) {
						$i++; $k = 'parent'; $j = '';
						$html = $doc->buildPDFContent( $jobj, $pdf, $i, $doc, $j, $k );
					}
				}
				// add a new page for TOC
				$pdf->addTOCPage();

				// write the TOC title
				$sectstyle = '';
				$sectfont = $settings['pdf_sect_font'];
				$sectfontsize = $settings['pdf_sect_fsize'];
				if( substr($sectfont, -1) == 'b' ) {
					$sectstyle = 'B'; 
					$sectfont = substr($sectfont,0,-1);
				}
				else if( substr($sectfont, -1) == 'i' ) {
					$sectstyle = 'I';
					$sectfont = substr($sectfont,0,-1);
				}
				else if( substr($sectfont, -2) == 'bi' ) {
					$sectstyle = 'BI';
					$sectfont = substr($sectfont,0,-2);
				}
				$pdf->SetFont( $sectfont, $sectstyle, $sectfontsize );
						
				// add a simple Table Of Content at first page
				$pdf->addTOC(2, 'helvetica', '.', 'INDEX', '', array(0,0,0));

				// end of TOC page
				$pdf->endTOCPage();
				// Close and output PDF document
				// This method has several options, check the source code documentation for more information.
				$pdfname = ( !empty( $guide->doc_title ) ) ? $guide->doc_title.'-'.$guide->doc_id.'.pdf' : 'document.pdf';
				$pdfname = sanitize_file_name( strtolower($pdfname) );
				
				//delete previous PDF if exists
				if( $guide->pdf_id != 0 ) {
					$attachmentid = $guide->pdf_id;
					wp_delete_attachment( $attachmentid, 1 );
				}
				//upload PDF in uploads folder
				$wp_upload_dir = wp_upload_dir();
				$filename = $wp_upload_dir['url'] . '/'.$pdfname;
				$filepath= $wp_upload_dir['path']. '/'.$pdfname;
				
				$pdf->Output($filepath, 'F');
				
				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $filename ), null );
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'publish'
				);
				
				$attach_id = wp_insert_attachment( $attachment, $filename );
				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				
				//update pdf id in document table
				global $wpdb, $table_prefix;
				
				//ver1.4				
				update_post_meta($guide->post_id,'_doc_pdf_id',$attach_id);
				
			}
			_e('PDF generated successfully','documentor');
			die();
		}
		//Build PDF content
		function buildPDFContent( $obj, $pdf, $i, $doc, $j, $k ) {
			if(isset($doc->docid)) {
				if( class_exists( 'DocumentorSection' ) ) {
					$id = $doc->docid;
					$ds = new DocumentorSection( $id, $obj->id);
				}
			}
			$html = "";
			if( $ds != null ) {
				$settings = $doc->get_settings();
				$sectiondata = $ds->getdata();
				foreach( $sectiondata as $secdata ) {
					$postid = $secdata->post_id;
					$mtitle = $section_title = $content = '';
					if( $k == 'parent' ) {
						$scnt = $i;
					}
					else {
						$scnt = $i.'.'.$j;
					}
					$i = $scnt;
					if( $secdata->type == 3 ) { //if link section
						$postdata = get_post( $postid );
						if( $postdata != NULL ) {
							$jarr = unserialize( $postdata->post_content );
							$target = '';
							if( $jarr['new_window'] == '1' ) {
								$target = 'target="_blank"';
							}
							$bookmarkTitle = $postdata->post_title;
							$mtitle = '<a href="'.$jarr['link'].'">'.$postdata->post_title.'</a>'; 
							$mtitle = DocumentorGuide::documentorReplaceAnchorsWithText($mtitle);
						}
					} else { //if section is post or page or inline
						//WPML
						if( function_exists('icl_plugin_action_links') ) {	
							if( $secdata->type == 0 ) $type = 'documentor-sections';
							else if( $secdata->type == 1 ) $type = 'post';
							else if( $secdata->type == 2 ) $type = 'page';
							else if( $secdata->type == 3 ) $type = 'nav_menu_item';
							else if( $secdata->type == 4 ) {
								$type = get_post_type( $postid );
							}
							$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
							$menu_title = get_post_meta( $lang_post_id, '_documentor_menutitle', true );
							$section_title = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
							$postid = $lang_post_id;
						} else {
							$menu_title = get_post_meta( $postid, '_documentor_menutitle', true );
							$section_title = get_post_meta( $postid, '_documentor_sectiontitle', true );
						}
						$bookmarkTitle = $menu_title;
						$mtitle = $menu_title;
					}
					$section_title = ( !empty( $section_title) ) ? $section_title : $mtitle;
					//section title
					$sectstyle = '';
					$sectfont = $settings['pdf_sect_font'];
					$sectfontsize = $settings['pdf_sect_fsize'];
					if( substr($sectfont, -1) == 'b' ) {
						$sectstyle = 'B'; 
						$sectfont = substr($sectfont,0,-1);
					}
					else if( substr($sectfont, -1) == 'i' ) {
						$sectstyle = 'I';
						$sectfont = substr($sectfont,0,-1);
					}
					else if( substr($sectfont, -2) == 'bi' ){
					 	$sectstyle = 'BI';
					 	$sectfont = substr($sectfont,0,-2);
					}
					$pdf->SetFont( $sectfont, $sectstyle, $sectfontsize );
				
					$pdf->SetTextColor(0,0,0);
					$pdf->writeHTMLCell(0, 0, '', '', $scnt.'. '.$section_title, 0, 1, 0, true, '', true);
					
					//menu font in TOC
					$menutstyle = '';
					$menutfont = $settings['pdf_menut_font'];
					$menutfontsize = $settings['pdf_menut_fsize'];
					if( substr($menutfont, -1) == 'b' ){
						$menutstyle = 'B'; 
						$menutfont = substr($menutfont,0,-1);
					}
					else if( substr($menutfont, -1) == 'i' ) { 
						$menutstyle = 'I';
						$menutfont = substr($menutfont,0,-1);
					}
					else if( substr($menutfont, -2) == 'bi' ) {
						$menutstyle = 'BI';
						$menutfont = substr($menutfont,0,-2);
					}
					$pdf->SetFont( $menutfont, $menutstyle, $menutfontsize );
					
					//bookmark section for adding page numbers in index
					$dotcnt = substr_count($scnt, '.');
					$pdf->Bookmark($scnt.'. '.$bookmarkTitle, $dotcnt, 0, '', '', array(0,0,0));
					
					if( $secdata->type != 3 ) { //not link section 
						$pdf->Ln();
						//section content
						$seccstyle = '';
						$seccfont = $settings['pdf_secc_font'];
						$seccfontsize = $settings['pdf_secc_fsize'];
						if( substr($seccfont, -1) == 'b' ) {
							$seccstyle = 'B'; 
							$seccfont = substr($seccfont,0,-1);
						}
						else if( substr($seccfont, -1) == 'i' ) {
							$seccstyle = 'I';
							$seccfont = substr($seccfont,0,-1);
						}
						else if( substr($seccfont, -2) == 'bi' ) {
							$seccstyle = 'BI';
							$seccfont = substr($seccfont,0,-2);
						}
						$pdf->SetFont( $seccfont, $seccstyle, $seccfontsize );
						$pdf->SetTextColor(0,0,0);
						$content = '';
						//section is post/page
						$post = get_post( $postid );
						if( $post != null ) {
							$content = $post->post_content;
						}
					} 
					//apply the_content filter so that HTML tags and shortcodes are preserved
					
					$content = apply_filters( 'the_content' , $content );
					$content = do_shortcode( $content );
					if($settings['pdflinks'] == '0') {
						$content = DocumentorGuide::documentorRemoveAnchors($content);
					}
					elseif($settings['pdflinks'] == '2'){
						$content =  $content;
					}
					else{
						$content = DocumentorGuide::documentorReplaceAnchorsWithText($content);
					}
					
					$pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '', true);
					$pdf->Ln(6);
					
					if ( isset( $obj->children ) && $obj->children ) {
						$j = 0;
						foreach( $obj->children as $child ) {
							$j++;$k = 'child';
							$html = $doc->buildPDFContent( $child, $pdf, $i, $doc, $j, $k );
						}
					}
				}
			}
			return $html;
		}
		/* Search in document */
		public static function get_search_results() {
			$term = strtolower( $_REQUEST['term'] );
			$docid = isset( $_REQUEST['docid'] ) ? $_REQUEST['docid'] : '';
			$suggestions = array();
			if( !empty( $docid ) ) {
				global $wpdb,$table_prefix;
				$postids = $wpdb->get_col('SELECT post_id FROM '.$table_prefix.DOCUMENTOR_SECTIONS.' WHERE doc_id = '.$docid);
				$includearr =  array();
				if( $postids ) $includearr = $postids;
				$args = array(
					'post_type' => array( 'post', 'page', 'documentor-sections'),
					'posts_per_page' => -1,	
					'post_status'   => 'publish',
					's'=> $term,
			 	);
			 	$the_query = new WP_Query( $args );
			 	while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$suggestion = array();
					if( in_array( get_the_ID(), $includearr ) ) {
						$lbl = get_post_meta(get_the_ID(),'_documentor_sectiontitle', true);
						$suggestion['label'] = $lbl;
						$slug = $wpdb->get_var('SELECT slug FROM '.$table_prefix.DOCUMENTOR_SECTIONS.' WHERE post_id = '.get_the_ID());
						$suggestion['slug'] = $slug;
						$suggestions[] = $suggestion;
					}
				}
				wp_reset_postdata();
			}
			if( empty($suggestions) ) {
				$suggestions = array(
					'label' => 'Nothing Found'
				);
			}

			// JSON encode and echo
			$response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
			echo $response;
			die();
		}
		/* Reset feedback counts of whole document */
		public static function reset_feedback_count() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$docid = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			$res = '';
			if( !empty( $docid ) ) {
				global $wpdb,$table_prefix;
				$sectbl = $table_prefix.DOCUMENTOR_SECTIONS;
				$res = $wpdb->update( 
					$sectbl, 
					array( 
						'upvote' => 0,
						'downvote' => 0
					), 
					array( 'doc_id' => $docid ), 
					array( '%d', '%d' ), 
					array( '%d' ) 
				);
			}
			if( false !== $res ) {
				$msg = 'Feedback counters reset successfully!!';
			} else {
				$msg = 'Some error occured. Please try again.';	
			}
			_e( $msg, 'documentor' ); 
			die();	
		}
		/* function to get social share buttons */
		public function get_social_buttons( $settings, $sharetitle, $sharelink ) {
			$html = '';
			$btnposition = $settings['sbutton_position'];
			$html .='<div class="doc-socialshare doc-noprint '.$btnposition.'">
				<div class="doc-sharelink" data-sharelink="'.urlencode($sharelink).'"></div>';
			$btnclass = $settings['sbutton_style'];
			$i = 1;
			//facebook button
			if( $settings['socialbuttons'][0] == 1 ) {
				$fbtnclass = '';
				if( $i == 1 ) $fbtnclass = ' doc-fsbtn';
				$i++;
				$html .='<div class="sbutton doc-fb-share '.$btnclass.$fbtnclass.'" id="doc_fb_share"><a rel="nofollow" href="http://www.facebook.com/sharer.php?u='. urlencode($sharelink) .'&amp;t='. htmlspecialchars(urlencode(html_entity_decode($sharetitle.' - '.$sharelink, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'" title="Share to Facebook" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-doc-facebook"></i></a>';
				if( $settings['sharecount'] == 1 ) {
					$html .='<span class="doc-socialcount" id="doc-fb-count"><i class="cs c-icon-doc-spinner animate-spin"></i></span>';
				}
				$html .='</div>';
			}
			//twitter button
			if( $settings['socialbuttons'][1] == 1 ) {
				$fbtnclass = '';
				if( $i == 1 ) $fbtnclass = ' doc-fsbtn';
				$i++;
				$html .='<div class="sbutton doc-twitter-share '.$btnclass.$fbtnclass.'" id="doc_twitter_share"><a rel="nofollow" href="http://twitter.com/share?text='. htmlspecialchars(urlencode(html_entity_decode($sharetitle.' - ', ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'&amp;url='. urlencode($sharelink) .'" title="Share to Twitter" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-doc-twitter"></i></a>';
				if( $settings['sharecount'] == 1 ) {
					$html .= '<span class="doc-socialcount" id="doc-twitter-count"><i class="cs c-icon-doc-spinner animate-spin"></i></span>';
				}
				$html .= '</div>';
			}
			//google plus button
			if( $settings['socialbuttons'][2] == 1 ) {
				$fbtnclass = '';
				if( $i == 1 ) $fbtnclass = ' doc-fsbtn';
				$i++;
				$html .='<div class="sbutton doc-gplus-share '.$btnclass.$fbtnclass.'" id="doc_gplus_share"><a rel="nofollow" href="https://plus.google.com/share?url='.urlencode($sharelink).'" title="Share to Google Plus" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-doc-gplus"></i></a>';
				if( $settings['sharecount'] == 1 ) {
					$gpluscount = $this->get_plusones( $sharelink );
					$html .= '<span class="doc-socialcount" id="doc-gplus-count" data-gpluscnt="'.$gpluscount.'"><i class="cs c-icon-doc-spinner animate-spin"></i></span>';
				}
				$html .= '</div>';
			}
			//pinterest button
			if( $settings['socialbuttons'][3] == 1 ) {
				$fbtnclass = '';
				if( $i == 1 ) $fbtnclass = ' doc-fsbtn';
				$i++;
				$html .='<div class="sbutton doc-pin-share '.$btnclass.$fbtnclass.'" id="doc_pin_share"><a rel="nofollow" href="http://pinterest.com/pin/create/bookmarklet/?url='.urlencode($sharelink) .'&amp;description='. htmlspecialchars(urlencode(html_entity_decode($sharetitle.' - '.$sharelink, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') .'" title="Share to Pinterest" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=450\');return false;"><i class="cs c-icon-doc-pinterest"></i></a>';
				if( $settings['sharecount'] == 1 ) {
					$html .= '<span class="doc-socialcount" id="doc-pin-count"><i class="cs c-icon-doc-spinner animate-spin"></i></span>';
				}
				$html .= '</div>';
			}
			$html .='</div>';
			return $html;
		}
		/* Get google plus share count */
		public function get_plusones( $url )  {
			if( function_exists('curl_version') ) {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ");
				$curl_results = curl_exec ($curl);
				curl_close ($curl);
				$json = json_decode($curl_results, true);
				return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
			} else {
				return 0;
			}
		}
		/* Suggest guides to attach to the product of Woocommerce at meta box of post type product */
		public static function doc_suggest_guides_toattach() {
			$term = strtolower( $_REQUEST['term'] );
			$suggestions = array();
			global $wpdb,$table_prefix;
			$doctable = $table_prefix.DOCUMENTOR_TABLE;
			$guides = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$doctable." WHERE doc_title like '%%%s%%'", $term ) );
			if( count( $guides ) > 0 ) {
				foreach( $guides as $guide ) {
					$suggestion['id'] = $guide->doc_id;
					$suggestion['label'] = $guide->doc_title;
					$suggestions[] = $suggestion;
				}
			}			
			//JSON encode and echo
			$response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
			echo $response;
			die();
		}
}// class DocumentorGuide ends
?>
