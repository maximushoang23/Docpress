<?php // Hook for adding admin menus
if( !class_exists( 'DocumentorSection' ) ) {
	class DocumentorSection {
		public $secid,$docid;
		public $sectitle='';
		public $menutitle='';
		public $content='';
		public $type='';
		
		function __construct($id=0 , $secid=0){
			$this->doc_id = $id;
			$this->secid = $secid;
		}
		
		public static function create() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$ptype = ( isset($_POST['post_type']) ) ? sanitize_text_field($_POST['post_type']) : '';
			if ( $ptype != 'inline' && $ptype != 'link' ) {
				$type = 1;
				if( $ptype == 'post' ) {
					$type = 1;	// 1 for post
				} else if( $ptype == 'page' ) {
					$type = 2;	// 2 for page
				} else {
					$type = 4;	//4 for custom post 
				}
				$docptype = isset( $_POST['post_type'] ) ? $_POST['post_type']: "";
				global $wpdb, $table_prefix;
				$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
				$docid = isset( $_POST['docid'] ) ? intval($_POST['docid']) : '';
				if( !empty( $docid ) ) {
					if( !isset( $_POST['post_id'] ) ) {
						_e('Please select any '.$docptype,'documentor');
						die();
					}
					$count = count($_POST['post_id']);
					$values = '';
					for($i = 0; $i < $count; $i++ ) {
						$id = intval($_POST['post_id'][$i]);
						$post = get_post($id); 
						$title = $post->post_title;
						$pid = $id;	//save post/page id in content column
						$sec = new DocumentorSection();
						if(!$sec->is_sectionpresent($pid,$docid)) {	//check if post/page is already added 
							$slug = $post->post_name;				
							if($i == $count-1) {
								$values .= "('$docid', '$pid', '$type', '$slug')";
							} else {
								$values .= "('$docid', '$pid', '$type', '$slug'),";
							}
							//add meta fields for section title and menu title
							update_post_meta($pid, '_documentor_menutitle', $title);
							update_post_meta($pid, '_documentor_sectiontitle', $title);
						}
					}
					if( !empty( $values ) ) {
						$sql = "INSERT INTO $table_name (doc_id, post_id, type, slug) VALUES $values";
						$wpdb->query($sql);
						$sectionid = $wpdb->insert_id;
						$lastid = $wpdb->get_var("SELECT MAX(sec_id) FROM $table_name");
						//update order of sections in documentor table
						$secarr = array();
						for( $j = $sectionid; $j <= $lastid; $j++ ) {
							$secarr[] = (object) array( 'id' => $j );
						}
						$doctable = $table_prefix.DOCUMENTOR_TABLE;						
						//ver1.4 start
						$postid = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $docid ) );
						$getorder = get_post_meta($postid,'_doc_sections_order',true); //ver1.4end
						$secjarray = array();					
						if( !empty( $getorder ) ) {
							$secjarray = json_decode( $getorder, true );
							$secjarray = array_merge( $secjarray, $secarr );
						} else {
							$secjarray = array_merge( $secjarray, $secarr );
						}
						if( count( $secjarray ) > 0 ) {
							$jsonstr = json_encode($secjarray);						
							update_post_meta($postid,'_doc_sections_order',$jsonstr); //ver1.4
						}
					}
				}
				_e("Section added successfully!!!",'documentor');
				die();
			} else if ( $ptype == 'inline' ) {	
				$type = 0;	//0 for inline
				$menutitle = ( isset($_POST['menutitle']) ) ? $_POST['menutitle'] : '';
				$sectiontitle = ( isset($_POST['sectiontitle']) ) ? $_POST['sectiontitle'] : '';
				$icontent = ( isset($_POST['icontent']) ) ? $_POST['icontent'] : '';
				$docid = isset( $_POST['docid'] ) ? intval($_POST['docid']) : '';
				if( empty( $menutitle ) ) {
					$error = 'error: Please enter menu title';
					echo $error; 
					die();
				} else if( empty( $sectiontitle ) ) {
					$error = 'error: Please enter section title';
					echo $error; 
					die();
				} else {
					if( !empty( $menutitle ) && !empty( $docid ) ) {
						global $table_prefix, $wpdb;
						$post = array(
							'post_title'    => $sectiontitle,
							'post_content'  => $icontent,
							'post_type'	=> 'documentor-sections',
							'post_status'	=> 'publish'
						);
						//insert custom post
						$post_id = wp_insert_post( $post );

						//add meta fields for section title and menu title
						update_post_meta($post_id, '_documentor_menutitle', $menutitle);
						update_post_meta($post_id, '_documentor_sectiontitle', $sectiontitle);	
				
						//get slug of post
						$dpost = get_post($post_id); 
						$slug = $dpost->post_name;
						
						//insert section in sections table
						$wpdb->insert( 
							$table_prefix.DOCUMENTOR_SECTIONS, 
							array(
								'doc_id' => $docid,
								'post_id' => $post_id,
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
						//update order of sections in documentor table
						$sectionid = $wpdb->insert_id;
						$doctable = $table_prefix.DOCUMENTOR_TABLE;
						//ver1.4
						$postid= $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $docid ) );
						$getorder = get_post_meta($postid,'_doc_sections_order',true);
						$secjarray = array();					
						if( !empty( $getorder ) ) {
							$secjarray = json_decode( $getorder, true );
							$secjarray[] = (object) array('id' => $sectionid);
						} else {
							$secjarray[] = (object) array('id' => $sectionid);
						}
						if( count( $secjarray ) > 0 ) {
							$jsonstr = json_encode($secjarray);
							update_post_meta($postid,'_doc_sections_order',$jsonstr); //ver1.4
						}
						_e("Section added successfully!!!",'documentor');
						die();
					}
				}
				
			} else if ( $ptype == 'link' ) {
				$type = 3;	//3 for links
				$menutitle = ( isset( $_POST['menutitle'] ) ) ? $_POST['menutitle'] : '';
				$linkurl = ( isset( $_POST['linkurl'] ) ) ? $_POST['linkurl'] : '#';
				$newwindow = ( isset( $_POST['targetw'] ) ) ? intval($_POST['targetw']) : '0';
				if( empty( $menutitle ) ) {
					$error = 'error: Please enter menu title';
					echo $error; 
					die();
				} else if( empty( $linkurl ) ) {
					echo 'error: Please enter link url';
					die();
				} else {
					$arr = array(
						'link'=>$linkurl,
						'new_window'=>$newwindow
						);
					$content = serialize($arr); 
					$post = array(
							'post_title'    => $menutitle,
							'post_content'  => $content,
							'post_type'	=> 'nav_menu_item',
							'post_status'	=> 'publish'
						);
					//insert custom post
					$post_id = wp_insert_post( $post );

					//get slug of post
					$dpost = get_post($post_id); 
					$slug = $dpost->post_name;
					
					//insert section in sections table
					$docid = isset( $_POST['docid'] ) ? intval($_POST['docid']) : '';
					global $table_prefix, $wpdb;
					$wpdb->insert( 
						$table_prefix.DOCUMENTOR_SECTIONS, 
						array(
							'doc_id' => $docid,
							'post_id' => $post_id,
							'type'	=> $type,
							'slug'	=> $slug
						), 
						array( 
							'%d',
							'%d', 
							'%s',
							'%s'
						) 
					);
					//update order of sections in documentor table
					$sectionid = $wpdb->insert_id;
					$doctable = $table_prefix.DOCUMENTOR_TABLE;
					if( !empty( $docid ) ) {
						$postid= $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM ".$table_prefix.DOCUMENTOR_TABLE." WHERE doc_id = %d", $docid ) );
						$getorder = get_post_meta($postid,'_doc_sections_order',true); //ver1.4
						$secjarray = array();					
						if( !empty( $getorder ) ) {
							$secjarray = json_decode( $getorder, true );
							$secjarray[] = (object) array('id' => $sectionid);
						} else {
							$secjarray[] = (object) array('id' => $sectionid);
						}
						if( count( $secjarray ) > 0 ) {
							$jsonstr = json_encode($secjarray);
							update_post_meta($postid,'_doc_sections_order',$jsonstr); //ver1.4
						}
					} 
					_e("Section added successfully!!!",'documentor');
					die();
				}
			}
		}
		public static function update() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$type = ( isset( $_POST['type'] ) ) ? intval($_POST['type']) : 0;
			$secid = ( isset( $_POST['section_id'] ) ) ? intval($_POST['section_id']) : '';
			$mtitle = ( isset( $_POST['menutitle'] ) ) ? $_POST['menutitle'] : '';
			$stitle = ( isset( $_POST['sectiontitle'] ) ) ? $_POST['sectiontitle'] : '';
			$postid = ( isset( $_POST['post_id'] ) ) ? intval($_POST['post_id']) : '';
			$slug = ( isset( $_POST['slug'] ) ) ? sanitize_title($_POST['slug']) : '';
			global $wpdb, $table_prefix;
			$sections_table = $table_prefix.DOCUMENTOR_SECTIONS;
			$response = array();
			//menu title is compolsary field
			if( empty($mtitle) ) {
				$response['error'] = "Please add menu title";
				echo json_encode($response);
				die();
			} else if( empty( $stitle ) && $type == 0 ) {
				$response['error'] = "Please add section title";
				echo json_encode($response);
				die();
			}
			if( empty($slug) ) {
				$response['error'] = "Please add slug";
				echo json_encode($response);
				die();
			}
			if( !empty( $secid ) && ( !empty( $postid ) ) ) {
				//inline or post or page section
				if( $type != 3 )  {
					//update post if inline section
					if( $type == 0 || ( $type == 4 && get_post_type($postid) == 'documentor-sections' )) {
						$post = array(
						      'ID'           => $postid,
						      'post_title' => $stitle
						);
						wp_update_post( $post );
					}
					//update meta fields for menu title and section title
					$menu_title = get_post_meta($postid,'_documentor_menutitle',true);
					if( $menu_title != $mtitle ) {
						update_post_meta($postid, '_documentor_menutitle', $mtitle);
					}
					$section_title = get_post_meta($postid,'_documentor_sectiontitle',true);
					if( $section_title != $stitle ) {
						update_post_meta($postid, '_documentor_sectiontitle', $stitle);	
					}
				} else { //link section
					$linkurl = ( isset( $_POST['linkurl'] ) ) ? $_POST['linkurl'] : '#';
					$newwindow = ( isset( $_POST['new_window'] ) ) ? intval($_POST['new_window']) : '0';
					if( empty( $linkurl ) ) {
						$response['error'] = "error: Please add link url";
						echo json_encode($response);
						die();
					}
					$arr = array(
						'link'=>$linkurl,
						'new_window'=>$newwindow
						);
					$content = serialize($arr); 

					//update nav_menu item post
					$post = array(
						      'ID'           => $postid,
						      'post_title'   => $mtitle,
						      'post_content' => $content
						);
					wp_update_post( $post );
				}
				//update slug in sections table
				$secobj = new DocumentorSection();
				//check for duplicate slug if present make it unique 
				$check_sql = "SELECT slug FROM $sections_table WHERE slug = %s AND sec_id != %d";
				$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $secid ) );
				if ( $slug_check ) {
					$suffix = 2;
					do {
						$alt_slug = $secobj->truncate_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
						$slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_slug, $secid ) );
						$suffix++;
					} while ( $slug_check );
					$slug = $alt_slug;
				}
				//update slug
				$wpdb->update( 
					$sections_table, 
					array( 
						'slug' => $slug	
					), 
					array( 'sec_id' => $secid ), 
					array( 
						'%s'
					), 
					array( '%d' ) 
				);
				$response['slug'] = apply_filters( 'editable_slug', $slug );
				echo json_encode($response);
			}	
			die();
		}
		//get section data while showing suggestion form
		public static function suggest_editsecdata() {
			$secid = isset($_POST['secid']) ? $_POST['secid'] : '';
			$docid = isset($_POST['docid']) ? $_POST['docid'] : '';
			$sectitle = $captcha = '';
			if( !empty( $secid ) && !empty( $docid ) ) {	
				$sectitle = '';
				$sec = new DocumentorSection();
				$secdata = $sec->getsection( $secid );
				$postid = $secdata->post_id;
				$guide = new DocumentorGuide( $docid );
				if( $secdata->type == 0 ) $type = 'documentor-sections';
				else if( $secdata->type == 1 ) $type = 'post';
				else if( $secdata->type == 2 ) $type = 'page';
				else if( $secdata->type == 3 ) $type = 'link';
				else if( $secdata->type == 4 ) {
					$type = get_post_type( $postid );
				}
				//WPML
				if( function_exists('icl_plugin_action_links') ) {	
					$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
					$sectitle = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
					$postid = $lang_post_id;
				} else {
					$sectitle = get_post_meta( $postid, '_documentor_sectiontitle', true );
				}
				
				$nm = 'sedit-doc-captcha'.$docid;
				$trans_name = 'sedit_session_id'.$docid;
				$captcha = $guide->generate_captcha($name=$nm, $tr_name=$trans_name);
				$arr = array();
				$arr[] = $sectitle;
				$arr[] = $captcha;
				$arr[] = $postid;
				echo json_encode($arr);
			}
			die();
		}
		function getsection( $secid ) {
			global $wpdb, $table_prefix;
			$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
			$results = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE sec_id = %d", $secid ) );
			return $results;
		}
		//get all data of particular section
		function getdata() {
			global $wpdb, $table_prefix;
			$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
			$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE sec_id = %d", $this->secid ) );
			return $results;
		}
		//function to get already added posts in document
		function get_addedposts( $docid ) {
			global $wpdb, $table_prefix;
			$pids = array();
			$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
			if( !empty( $docid ) ) {
				$results = $wpdb->get_results($wpdb->prepare( "SELECT * FROM $table_name WHERE doc_id = %d AND (type != %d )", $docid, 3 ));
				foreach( $results as $result ) {
					$pids[] = $result->post_id;	
				}
			}
			return $pids;
		}
		//function show() {
		public static function show() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			// Edit Document
			if(isset($_POST['docid'])) {
				$id = intval($_POST['docid']);
			} else {
				$id = 1;
			}
			$guide=new DocumentorGuide($id);
			//print_r("In show".$id);die();
			$guide->get_sections_html();
			
		}
		// check whether section(post/page) already added
		function is_sectionpresent( $id, $docid ) {
			global $wpdb, $table_prefix;
			$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT sec_id FROM $table_name WHERE post_id = %d AND doc_id = %d", $id, $docid ) );
			if( $result == NULL ) { 
				return FALSE; 
			}
			else { 
				return TRUE; 
			}	
		}	
		// add links section form
		public static function section_add_linkform() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$html = '';
			$html.='<form method="post" id="addlink-section" class="addsecform">
					<div style="margin-left: 20px;">
						<div class="docfrm-div">
							<label class="titles"> '.__('Menu Title','documentor').' </label>
							<input type="text" name="menutitle" class="txts menutitle" placeholder="'.__('Enter Menu Title','documentor').'" value="" />
						</div>
						<div class="docfrm-div">
							<label class="titles"> '.__('Link URL','documentor').' </label>
							<input type="text" name="linkurl" class="txts linkurl" placeholder="http://" value="" />
						</div>
						<div class="docfrm-div">
							<input type="checkbox" name="new_window" class="new_window" />
							<input type="hidden" name="targetw" class="targetw">
							<label class="linklabel"> '.__('Open in new window','documentor').' </label>';
						$html.='</div><div class="clrleft"></div>
						<input type="submit" name="add_section" class="button-primary add-linksectionbtn" value="'.__('Insert','documentor').'" />
						<input type="hidden" name="post_type" value="link" />
					</div>
				</form>';
			echo $html;
			die();
		}
		//negatibe feedback form 
		public static function get_feedback_form() {	
			$docid = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			$secid = isset( $_POST['secid'] ) ? $_POST['secid'] : '';
			$nfeedbackform = '';
			$responsearr = array('msgflag'=> 0, 'text'=>'');
			$l_delimiter = "!!START!!";
    			$r_delimiter = "!!END!!";
			if( !empty( $docid ) && !empty( $secid ) ) {
				$guide = new DocumentorGuide( $docid );
				//get ip address of current user
				$ip = $guide->getRealIpAddr();
				global $wpdb, $table_prefix;
				//check whether same user had given vote for a section
				$feedbacktbl = $table_prefix.DOCUMENTOR_FEEDBACK;
				$res = $wpdb->get_var( $wpdb->prepare( "SELECT sec_id FROM $feedbacktbl WHERE sec_id = %d AND doc_id = %d AND date(date)=date(NOW()) AND ip = %s", $secid, $docid, $ip ) );
				if( $res != NULL ) {
					$responsearr['msgflag'] = 1;
					$responsearr['text'] = __("You have already given your feedback to this section. You can again give feedback for the same section after 24hrs","documentor"); 
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				} else {
					$settings = $guide->get_settings();
					$section = new DocumentorSection(); 
					$secdata = $section->getsection( $secid );
					$sectitle = '';
					$postid = $secdata->post_id;
					if( $secdata->type == 0 ) $type = 'documentor-sections';
					else if( $secdata->type == 1 ) $type = 'post';
					else if( $secdata->type == 2 ) $type = 'page';
					else if( $secdata->type == 3 ) $type = 'link';
					else if( $secdata->type == 4 ) {
						$type = get_post_type( $postid );
					}
					//WPML
					if( function_exists('icl_plugin_action_links') ) {	
						$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
						$sectitle = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
						$postid = $lang_post_id;
					} else {
						$sectitle = get_post_meta( $postid, '_documentor_sectiontitle', true );
					}
					$nfeedbackform .= '<form name="documentor-nfeedback" method="post" class="documentor-nfeedback">';
					if( $settings['feedback_frmname'] == 1 ) {
						$nfeedbackform.='<div>
							<input type="text" class="txtinput" name="name" placeholder="'.__('Name','documentor').'" />
						</div>';	
					}
					if( $settings['feedback_frmemail'] == 1 ) {
						$nfeedbackform.='<div>
							<input type="email" class="emailinput" placeholder="'.__('Email','documentor').'" name="email" /> 
						</div>';
					}
					if( $settings['feedback_frmtext'] == 1 ) {
						$nfeedbackform.='<div>
							<textarea name="content" class="textareainput" placeholder="'.__('Post your feedback...','documentor').'"></textarea>
						</div>';
					}
					if( !empty( $settings['feedback_frminputs'] ) ) {
						$inputs = explode(',',$settings['feedback_frminputs']);
						foreach( $inputs as $input ) {
							$nfeedbackform.='<div><input type="text" class="txtinput" name="'.trim($input).'" placeholder="'.trim($input).'"></div>';
						}
					}
					if( $settings['feedback_frmcapcha'] == 1 ) {
						$nfeedbackform.='<div><label>'.__('Captcha : ','documentor').' </label>'.$guide->generate_captcha($name='feedback-doc-captcha', $tr_name='feedback_session_id').'</div>';							
					}
					if( !empty( $settings['feedback_frminputs'] ) ) {
						$nfeedbackform .= '<input type="hidden" name="feedback_extrainputs" value="'.$settings['feedback_frminputs'].'">';
					}
					$nfeedbackform.='<input type="hidden" class="feedback-secid" name="secid" value="'.$secid.'" />
					<input type="hidden" class="feedback-sectitle" name="sec_title" value="'.$sectitle.'" />
					<input type="hidden" class="feedback-docid" name="docid" value="'.$docid.'" />
					<input type="hidden" class="feedback-postid" name="feedback_postid" value="'.$postid.'" />
					<button class="docsubmit-nfeedback"> Submit </button>
					</form>';
					$responsearr['text'] = $nfeedbackform;
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}
			}
		}
		//positive feedback to a section in document
		public static function positive_feedback() {
			$secid = isset($_POST['secid']) ? $_POST['secid'] : '';
			$docid = isset($_POST['docid']) ? $_POST['docid'] : '';
			$responsearr = array('success'=> 0, 'msg'=>'');
			$l_delimiter = "!!START!!";
    			$r_delimiter = "!!END!!";
			if( !empty( $docid) ) {
				$guide = new DocumentorGuide( $docid );
				//get ip address of current user
				$ip = $guide->getRealIpAddr();
			}
			if( !empty( $secid ) && !empty( $docid ) ) {
				global $wpdb, $table_prefix;
				//check whether same user had given vote for a section
				$feedbacktbl = $table_prefix.DOCUMENTOR_FEEDBACK;
				$res = $wpdb->get_var( $wpdb->prepare( "SELECT sec_id FROM $feedbacktbl WHERE sec_id = %d AND doc_id = %d AND date(date)=date(NOW()) AND ip = %s", $secid, $docid, $ip ) );
				if( $res != NULL ) {
					$responsearr['msg'] = __("You have already given your feedback to this section. You can again give feedback for the same section after 24hrs","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				} else {
					//insert entry in feedback table
					$feedbacktbl = $table_prefix.DOCUMENTOR_FEEDBACK;
					$qfeedback = "INSERT INTO $feedbacktbl(doc_id, sec_id, ip, vote, date) VALUES($docid, $secid, '$ip', 'yes', NOW())";
					$wpdb->query( $qfeedback );
					
					//update vote count
					$sectbl = $table_prefix.DOCUMENTOR_SECTIONS;
					$upvote = $wpdb->get_var( $wpdb->prepare( "SELECT upvote FROM $sectbl WHERE sec_id = %d", $secid ) );
					$upvote = $upvote + 1;
					$wpdb->update( 
							$sectbl, 
							array( 
								'upvote' => $upvote	
							), 
							array( 'sec_id' => $secid ), 
							array( 
								'%d'
							), 
							array( '%d' ) 
						);
						
					//get settings to get thank you message
					$thankyoumsg = '';
					$settings = $guide->get_settings();
					if( isset( $settings['feedback_thankyoumsg'] ) ) $thankyoumsg = $settings['feedback_thankyoumsg'];
					$responsearr['success'] = 1;
					$responsearr['msg'] = $thankyoumsg;
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}
			}
		}
		//negative feedback to a section in document
		public static function negative_feedback() {
			$nm = isset($_POST['name']) ? $_POST['name'] : '';
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$content = isset($_POST['content']) ? $_POST['content'] : '';
			$secid = isset($_POST['secid']) ? $_POST['secid'] : '';
			$sec_title = isset($_POST['sec_title']) ? $_POST['sec_title'] : '';
			$feedback_postid = isset($_POST['feedback_postid']) ? $_POST['feedback_postid'] : '';
			$docid = isset($_POST['docid']) ? $_POST['docid'] : '';
			$feedback_doc_captcha = isset($_POST['feedback-doc-captcha']) ? $_POST['feedback-doc-captcha'] : '';
			$extra_inputs = isset($_POST['feedback_extrainputs']) ? $_POST['feedback_extrainputs'] : '';
			$responsearr = array('success'=> 0, 'msg'=>'');
			$l_delimiter = "!!START!!";
    			$r_delimiter = "!!END!!";
			if( !empty( $secid ) && !empty( $docid ) ) {
				$guide = new DocumentorGuide( $docid );
				global $wpdb, $table_prefix;

				//get guide to get title of document
				$doc = $guide->get_guide( $docid );

				//get settings to get subject, thank you message
				$settings = $guide->get_settings();
				$subject = isset( $settings['feedback_frmsubject'] ) ? $settings['feedback_frmsubject'] : '';
				if( !empty( $subject ) && strpos( $subject, '{doc-title}' ) !== false ) {
	    				$subject = str_replace( "{doc-title}", $doc->doc_title, $subject );
				}
				if( !empty( $subject ) && strpos( $subject, '{section-title}' ) !== false ) {
	    				$subject = str_replace( "{section-title}", $sec_title,$subject );
				}
				$uidarr = isset( $settings['guide'] ) ? $settings['guide'] : '';
				if( empty( $uidarr ) ) {
					$responsearr['msg'] = __("Please select Guide manager at settings panel to whom suggestion will be send","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}

				//get guide managers for sending suggestion through mail to them 	
				$to = $guide->get_guideManager_emails( $uidarr ); 

				//get extra input fields added by user 
				$mcontent = "Hello there,\r\r";
				if( !empty( $extra_inputs ) ) {
					$inputs = explode( ',', $extra_inputs );
					$i = 0;
					foreach( $inputs as $input ) {
						$ipt = trim($input);
						$val = isset( $_POST["$ipt"] ) ? $_POST["$ipt"] : '';
						if( !empty( $val ) ) {
							if( $i == 0 ) {
								$mcontent .= "User added following extra information: \r";
							}
							$mcontent .= $ipt.": ".$val."\r";
							$i++;
						}
					}
				}
				$mcontent .= "\r";	
				$mcontent .= $content;
				$headers[] ='From: '.$nm.' <'.$email.'>' . "\r\n";

				//Form validations
				if( isset($_POST['email']) && empty( $email ) ) {
					$responsearr['msg'] = __("Please provide email address","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				} 
				if( !empty( $email ) ) {
					if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
						$responsearr['msg'] = __("Invaild email address","documentor");
						echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
						die();
					}
				}
				if( isset($_POST['content']) && empty( $content ) ) {
					$responsearr['msg'] = __("Please add your feedback","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}
				if( isset($_POST['feedback-doc-captcha']) && empty( $feedback_doc_captcha ) ) {
					$responsearr['msg'] = __("Please enter captcha","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}
				if( isset($_POST['feedback-doc-captcha']) && !empty( $feedback_doc_captcha ) )
				{
					if( get_transient('feedback_session_id' ) !== false)
					{
						//echo AUTH_KEY; die();
						if( strcmp( get_transient('feedback_session_id'), sha1(AUTH_KEY.$_POST['feedback-doc-captcha'].'feedback_session_id', false) ) !== 0) {
							$responsearr['msg'] = __("Invalid Captcha Entered","documentor");
							echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
							die();
						    }
					}
				}

				//update vote count
				$sectbl = $table_prefix.DOCUMENTOR_SECTIONS;
				$downvote = $wpdb->get_var( $wpdb->prepare( "SELECT downvote FROM $sectbl WHERE sec_id = %d", $secid ) );
				$downvote = $downvote + 1;
				$wpdb->update( 
						$sectbl, 
						array( 
							'downvote' => $downvote	
						), 
						array( 'sec_id' => $secid ), 
						array( 
							'%d'
						), 
						array( '%d' ) 
					);
				
				//insert entry in feedback table
				$feedbacktbl = $table_prefix.DOCUMENTOR_FEEDBACK;
				$ip = $guide->getRealIpAddr();
				$qfeedback = "INSERT INTO $feedbacktbl(doc_id, sec_id, ip, vote, date) VALUES($docid, $secid, '$ip', 'no', NOW())";
				$wpdb->query( $qfeedback );

				//send mail
				wp_mail($to, $subject, $mcontent, $headers);
				//add suggestion as WordPress comment for the section
				if( !empty( $feedback_postid ) ) {
					$time = current_time('mysql');
					$commentdata = array(
					    'comment_post_ID' => $feedback_postid,
					    'comment_author' => $nm,
					    'comment_author_email' => $email,
					    'comment_content' => $content,
					    'comment_parent' => 0,
					    'comment_author_IP' => $ip,
					    'user_id' => '',
					    'comment_date' => $time,
					    'comment_approved' => 0,
					    'comment_type' => '',
					    'comment_author_url' => '',
					);
					$comment_id = wp_new_comment( $commentdata );
				}
				//thank you message
				$thankyoumsg = isset( $settings['feedback_thankyoumsg'] ) ? $settings['feedback_thankyoumsg'] : '';
				$responsearr['success'] = 1;
				$responsearr['msg'] = $thankyoumsg;
				echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
				die();	
			}	
		}
		//suggest edit
		public static function suggest_edit() {
			$nm = isset($_POST['name']) ? $_POST['name'] : '';
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$content = isset($_POST['content']) ? $_POST['content'] : '';
			$sec_title = isset($_POST['sec_title']) ? $_POST['sec_title'] : '';
			$sedit_postid = isset($_POST['sedit_postid']) ? $_POST['sedit_postid'] : '';
			$docid = isset($_POST['docid']) ? $_POST['docid'] : '';
			$cpatchanm = 'sedit-doc-captcha'.$docid;
			$trans_name = 'sedit_session_id'.$docid;
			$sedit_doc_captcha = isset($_POST["$cpatchanm"]) ? $_POST["$cpatchanm"] : '';
			$extra_inputs = isset($_POST['sedit_extrainputs']) ? $_POST['sedit_extrainputs'] : '';
			$responsearr = array('success'=> 0, 'msg'=>'');
			$l_delimiter = "!!START!!";
    			$r_delimiter = "!!END!!";
			//get guide to get guide title
			$guide = new DocumentorGuide( $docid );
			//get settings to get subject, thank you message
			$settings = $guide->get_settings();
			$doc = $guide->get_guide( $docid );
			$subject = isset( $settings['sedit_frmsubject'] ) ? $settings['sedit_frmsubject']: '';
			if( !empty( $subject ) && strpos( $subject, '{doc-title}' ) !== false ) {
    				$subject = str_replace( "{doc-title}", $doc->doc_title, $subject );
			}
			if( !empty( $subject ) && strpos( $subject, '{section-title}' ) !== false ) {
    				$subject = str_replace( "{section-title}", $sec_title,$subject );
			}
			$uidarr = isset( $settings['guide'] ) ? $settings['guide'] : '';
			if( empty( $uidarr ) ) {
				$responsearr['msg'] = __("Please select Guide manager at settings panel to whom suggestion will be send","documentor");
				echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
				die();
			}
			//get guide managers for sending suggestion through mail to them 	
			$to = $guide->get_guideManager_emails( $uidarr ); 
			//get extra input fields added by user 
			$mcontent = "Hello,\r\r";
			if( !empty( $extra_inputs ) ) {
				$inputs = explode( ',', $extra_inputs );
				$i = 0;
				foreach( $inputs as $input ) {
					$ipt = trim($input);
					$val = isset( $_POST["$ipt"] ) ? $_POST["$ipt"] : '';
					if( !empty( $val ) ) {
						if( $i == 0 ) {
							$mcontent .= "User added following extra information: \r";
						}
						$mcontent .= $ipt.": ".$val."\r";
						$i++;
					}
				}
			}
			$mcontent .= "\r";	
			$mcontent .= $content;
			$headers[] ='From: '.$nm.' <'.$email.'>' . "\r\n";
			//validations
			if( isset($_POST['email']) && empty( $email ) ) {
				$responsearr['msg'] = __("Please provide email address","documentor");
				echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
				die();
			} 
			if( !empty( $email ) ) {	
				if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
					$responsearr['msg'] = __("Invaild email address","documentor");
					echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
					die();
				}
			}
			if( isset($_POST['content']) && empty( $content ) ) {
				$responsearr['msg'] = __("Please add your suggestion","documentor");
				echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
				die();
			}
			if( isset($_POST["$cpatchanm"]) && empty( $sedit_doc_captcha ) ) {
				$responsearr['msg'] = __("Please enter captcha","documentor");
				echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
				die();
			}
			if( isset($_POST["$cpatchanm"]) && !empty( $sedit_doc_captcha ) )
			{
				if( get_transient( $trans_name ) !== false)
				{
					if( strcmp( get_transient( $trans_name ), sha1(AUTH_KEY.$_POST["$cpatchanm"].$trans_name, false) ) !== 0) {
						$responsearr['msg'] = __("Invalid Captcha Entered","documentor");
						echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
						die();
					    }
				}
			}
			//send mail
			wp_mail($to, $subject, $mcontent, $headers);
			//add suggestion as WordPress comment for the section
			if( !empty( $sedit_postid ) ) {
				$ip = $guide->getRealIpAddr();
				$time = current_time('mysql');
				$commentdata = array(
				    'comment_post_ID' => $sedit_postid,
				    'comment_author' => $nm,
				    'comment_author_email' => $email,
				    'comment_content' => $content,
				    'comment_parent' => 0,
				    'comment_author_IP' => $ip,
				    'user_id' => '',
				    'comment_date' => $time,
				    'comment_approved' => 0,
				    'comment_type' => '',
				    'comment_author_url' => '',
				);
				$comment_id = wp_new_comment( $commentdata );
			}
			//thank you message
			$thankyoumsg = isset( $settings['sedit_thankyoumsg'] ) ? $settings['sedit_thankyoumsg'] : '';
			$responsearr['success'] = 1;
			$responsearr['msg'] = $thankyoumsg;
			echo $l_delimiter.json_encode( $responsearr ).$r_delimiter;
			die();	
		}
		public static function get_ajaxcontent() {
			$secid = isset( $_POST['secid'] ) ? $_POST['secid'] : '';
			$docid = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			$currenturl = isset( $_POST['currenturl'] ) ? $_POST['currenturl'] : '';
			$nextsecid = isset( $_POST['nextsecid'] ) ? $_POST['nextsecid'] : '';
			$prevsecid = isset( $_POST['prevsecid'] ) ? $_POST['prevsecid'] : '';
			$nextsecname = isset( $_POST['nextsecname'] ) ? $_POST['nextsecname'] : '';
			$prevsecname = isset( $_POST['prevsecname'] ) ? $_POST['prevsecname'] : '';
			$html = "";
			if( !empty( $secid ) && !empty( $docid ) ) {
				$guide = new DocumentorGuide( $docid );
				$settings = $guide->get_settings();
				$skin = $settings['skin'];
				require_once(dirname(dirname (__FILE__)) . '/skins/'.$skin.'/index.php');
				$displayclass = 'DocumentorDisplay'.$skin;
				$dispobj = new $displayclass( $docid );
				$html = $dispobj->get_section_ajaxcontent( $docid, $secid, $currenturl, $nextsecid, $prevsecid, $nextsecname, $prevsecname  );
			}
			$html=apply_filters('section_html', $html);
			echo $html;
			die();
		}
		//reset section feedback count 
		public static function reset_feedbackcnt() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$secid = isset( $_POST['secid'] ) ? $_POST['secid'] : '';
			$docid = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			$res = '';
			if( !empty( $secid ) && !empty( $docid ) ) {
				global $wpdb,$table_prefix;
				$sectbl = $table_prefix.DOCUMENTOR_SECTIONS;
				$res = $wpdb->update( 
					$sectbl, 
					array( 
						'upvote' => 0,
						'downvote' => 0
					), 
					array( 'sec_id' => $secid, 'doc_id' => $docid ), 
					array( '%d', '%d' ), 
					array( '%d', '%d' ) 
				);
			}
			if( false !== $res ) {
				$msg = 'Feedback counters reset successfully!!';
			} else {
				$msg = 'Some error occured. Please try again.';	
			}
			_e($msg,'documentor'); 
			die();
		}
		//save PDF
		public static function save_pdf() {
			check_ajax_referer( 'documentor-sections-nonce', 'sections_nonce' );
			$secid = isset( $_POST['secid'] ) ? $_POST['secid'] : '';
			$docid = isset( $_POST['docid'] ) ? $_POST['docid'] : '';
			if( !empty( $secid ) && !empty( $docid ) ) {
				require_once (dirname (__FILE__) . '/includes/tcpdf/tcpdf.php');	
				require_once (dirname (__FILE__) . '/includes/tcpdf/config/tcpdf_config.php');
				require_once (dirname (__FILE__) . '/includes/tcpdf/documentor_tcpdf.php');
				//get section data
				$sec = new DocumentorSection();
				$secdata = $sec->getsection( $secid );
				$content = $sectitle = $menutitle = "";
				if( $secdata->type != 3 ) { //Not link section
					$postid = $secdata->post_id;
					//WPML
					if( function_exists('icl_plugin_action_links') ) {	
						if( $secdata->type == 0 ) $type = 'documentor-sections';
						else if( $secdata->type == 1 ) $type = 'post';
						else if( $secdata->type == 2 ) $type = 'page';
						else if( $secdata->type == 4 ) {
							$type = get_post_type( $postid );
						}
						$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
						$post = get_post( $lang_post_id );
						$sectitle = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
						$menutitle = get_post_meta( $lang_post_id, '_documentor_menutitle', true );
					} else {
						$post = get_post( $postid );
						$sectitle = get_post_meta( $postid, '_documentor_sectiontitle', true );
						$menutitle = get_post_meta( $postid, '_documentor_menutitle', true );
					}
					if( $post != null ) {
						$content = $post->post_content;
						//apply the_content filter so that HTML tags and shortcodes are preserved
						$content = apply_filters( 'the_content' , $content );
						$content = DocumentorGuide::documentorReplaceAnchorsWithText($content);
					}
				}
				//get guide data and settings
				$doc = new DocumentorGuide( $secdata->doc_id );
				$guide = $doc->get_guide( $doc->docid );
				
				$settings = json_decode($guide->settings, true);
				$guide_subtitle = $settings['guide_subtitle'];
				$guide_subtitle = isset( $guide_subtitle ) ? $guide_subtitle : '';
				$guide_title = isset( $guide->doc_title ) ? $guide->doc_title : '';
				$pdf = new DOCTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				// set document information
				$pdf->SetCreator(PDF_CREATOR);
				$pdf->SetAuthor($guide_subtitle);
				$pdf->SetTitle($guide_title);
				// set default header data
				$pdfHeaderString = $guide_subtitle;
				//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
				$header_txtcolor = array(0,0,0);
				$header_brcolor = array(255,255,255);
				$headertitle = $guide_title;
				$hlogo = $hlogow = '';
				$pdf->headeronfirstpg = 1;
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
				$pdf->SetHeaderData($hlogo, $hlogow, $headertitle, $pdfHeaderString,$header_txtcolor, $header_brcolor);
				//set footer data
				$pdf->footeronfirstpg = 1;
				$pdf->footertxt = '';
				if( isset( $settings['pdf_footertxt'] ) && !empty( $settings['pdf_footertxt'] ) ) {
					$pdf->footertxt = $settings['pdf_footertxt'];
				}
				$pdf->setFooterData();

				// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

				// set margins
				$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				// set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

				// ---------------------------------------------------------

				// set default font subsetting mode
				$pdf->setFontSubsetting(true);

				// Set font
				// helvetica is a UTF-8 Unicode font, if you only need to
				// print standard ASCII chars, you can use core fonts like
				// helvetica or times to reduce file size.
				
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
					
				// Add a page
				// This method has several options, check the source code documentation for more information.
				$pdf->AddPage();

				// Print text using writeHTMLCell()
				$sectitle = (!empty( $sectitle )) ? $sectitle : $menutitle;
				$buffer = "<div>".$sectitle."</div>";
				$pdf->writeHTMLCell(0, 0, '', '', $buffer, 0, 1, 0, true, '', true);
								
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
				$buffer = "<p>".$content."</p>";
				$pdf->writeHTMLCell(0, 0, '', '', $buffer, 0, 1, 0, true, '', true);

				// ---------------------------------------------------------

				// Close and output PDF document
				$pdfname = $docid.'-'.$secid.'.pdf';
				$pdfname = sanitize_file_name( strtolower($pdfname) );
				
				//delete previous PDF if exists
				if( $secdata->pdf_id != 0 ) {
					$attachmentid = $secdata->pdf_id;
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
				$wpdb->update( 
					$table_prefix.DOCUMENTOR_SECTIONS, 
					array( 
						'pdf_id' => $attach_id
					), 
					array( 'sec_id' => $secid ), 
					array( 
						'%d'
					), 
					array( '%d' ) 
				);
			}
			_e('PDF generated successfully', 'documentor');
			die();
		}
		/**
		 * Truncate a post slug.
		 * @param string $slug   The slug to truncate.
		 * @param int    $length Optional. Max length of the slug. Default 200 (characters).
		 * @return string The truncated slug.
		 */
		function truncate_slug( $slug, $length = 200 ) {
			if ( strlen( $slug ) > $length ) {
				$decoded_slug = urldecode( $slug );
				if ( $decoded_slug === $slug )
					$slug = substr( $slug, 0, $length );
				else
					$slug = utf8_uri_encode( $decoded_slug, $length );
			}

			return rtrim( $slug, '-' );
		}
	} //End Class DocumentorSection
} // End If
//download section pdf
$pdf = isset($_POST['doc_pdf']) ? $_POST['doc_pdf'] : '';
if( $pdf == 'section_pdf' ) {
	$secid = isset($_POST['secid']) ? $_POST['secid'] : '';
	$docid = isset($_POST['docid']) ? $_POST['docid'] : '';
	//Single section PDF
	if( !empty( $secid ) && !empty( $docid ) ) {
		$secobj = new DocumentorSection( $docid, $secid );
		$secdata = $secobj->getsection( $secid );
		$pdfid = $secdata->pdf_id;
		if( $pdfid != 0 ) {
			$postdata = get_post($pdfid);
			$pdfurl = $postdata->guid;
		
			$fileinfo = pathinfo($pdfurl);
		
			// The user will receive a PDF to download
			header('Content-type: application/pdf');

			// File will be called 
			header('Content-Disposition: attachment; filename='.$fileinfo['filename'].'.pdf');

			// The actual PDF file on the server
			readfile($pdfurl);
		}
	}
}
if( $pdf == 'document_pdf' ) {
	$doc_id = isset($_POST['doc_id']) ? $_POST['doc_id'] : '';
	//Complete document PDF
	if( !empty( $doc_id ) ) {
		$doc = new DocumentorGuide( $doc_id );
		
		$pdfid = $doc->pdf_id;
		$postdata = get_post($pdfid);
		$pdfurl = $postdata->guid;
	
		$fileinfo = pathinfo($pdfurl);
	
		// The user will receive a PDF to download
		header('Content-type: application/pdf');

		// File will be called 
		header('Content-Disposition: attachment; filename='.$fileinfo['filename'].'.pdf');

		// The actual PDF file on the server
		readfile($pdfurl);
	}
}
?>
