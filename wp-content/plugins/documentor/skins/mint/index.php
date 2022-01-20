<?php 
class DocumentorDisplaymint{
	function __construct($id=0) {
		$this->docid=$id;
	}
	//build menues to display at front
	function buildFrontMenus($obj, $i, $k, $j) {
		if(isset($this->docid)) {
			if( class_exists( 'DocumentorSection' ) ) {
				$id = $this->docid;
				$ds = new DocumentorSection( $id, $obj->id);
			}
		}
		$html = "";
		if( $ds != null ) {
		$guide = new DocumentorGuide( $this->docid );
		$settings = $guide->get_settings();
		$cssarr = $guide->get_inline_css();
		$sectiondata = $ds->getdata();
		$root = 'skins/'.$settings['skin'];
		foreach( $sectiondata as $secdata ) {
			$postid = $secdata->post_id;
			$mtitle = '';
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
					$mtitle ='<a href="'.esc_url($jarr['link']).'" '.$target.' '.$cssarr['navmenu'].' data-href="'.esc_url($jarr['link']).'">'.$postdata->post_title.'</a>'; 
				}
			} else { //if section is post or page or inline
				//WPML
				if( function_exists('icl_plugin_action_links') ) {	
					if( $secdata->type == 0 ) $type = 'documentor-sections';
					else if( $secdata->type == 1 ) $type = 'post';
					else if( $secdata->type == 2 ) $type = 'page';
					else if( $secdata->type == 4 ) {
						$type = get_post_type( $postid );
					}
					$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
					$menu_title = get_post_meta( $lang_post_id, '_documentor_menutitle', true );
				} else {
					$menu_title = get_post_meta( $postid, '_documentor_menutitle', true );
				}
				$mtitle = $menu_title;
			}
			$liactiveclass = '';
			if( $secdata->type == 3 ) {
				$html .= '<li class="doc-actli">'.$mtitle;
			} else {
				//pretty links
				$linkhref = '#section-'.$secdata->sec_id;
				if( !empty( $secdata->slug ) ) {
					$linkhref = apply_filters( 'editable_slug', $secdata->slug );
					$linkhref = '#'.$linkhref;
				} 
				$href = '';
				if( $settings['disable_ajax'] == 0 ) { $href = 'href="'.$linkhref.'"'; }
				$html .= '<li class="doc-actli">'.'<a class="documentor-menu" '.$href.' data-section-id="'.esc_attr($secdata->sec_id).'" '.$cssarr['navmenu'].' data-sec-counter="'.$scnt.'" data-href="'.$linkhref.'">'.$mtitle.'</a>';
			}
			if ( isset( $obj->children ) && $obj->children ) {
				$html .= '<span class="doc-mtoggle expand"></span>';
			}
			$html .= '<div class="docmenu-border"></div>';
			if ( isset( $obj->children ) && $obj->children ) {
				$html .= '<ol>';
				$j = 0;
				foreach( $obj->children as $child ) {
					$j++;$k = 'child';
				    	$html .= $this->buildFrontMenus($child, $i, $k, $j);
				}
				$html .= '</ol>';
			}
			$html .= '</li>';
		}
	}
		return $html;
	}
	//build sections to display on front
	function buildFrontSections($obj, $i, $k, $j) {
		$stylesheetpath = Documentor::documentor_plugin_url( 'skins/mint/print.css' );
		if(isset($this->docid)) {
			if( class_exists( 'DocumentorSection' ) ) {
				$id = $this->docid;
				$ds = new DocumentorSection( $id, $obj->id);
			}
		}
		$html = "";
		if( $ds != null ) {
			$guide = new DocumentorGuide( $this->docid );
			$cssarr = $guide->get_inline_css();
			$sectiondata = $ds->getdata();
			$settings = $guide->get_settings();
			$root = 'skins/'.$settings['skin'];
			$tran_class = "";
			$cntcss = 'style="display:none"';
			if( $settings['indexformat'] == 1 ) {
				$cntcss = 'style="display:inline-block"';
			}
			global $wpdb,$table_prefix;
			$tbl_section = $table_prefix.DOCUMENTOR_SECTIONS;
			if( !empty( $settings['animation'] ) ) {
				$tran_class = "wow documentor-animated documentor-".$settings['animation'];
			}
			$starttag = '<h3'; 
			$endtag = '</h3>'; 
			if( isset( $settings['section_element'] ) ) {
				for( $h = 1; $h <= 6; $h++ ) {
					if( $settings['section_element'] == $h ) {
						$starttag = '<h'.$h; 
						$endtag = '</h'.$h.'>'; 
					} 
				}
			} 
			//new fields added in v1.1
			$settings['updated_date'] = isset( $settings['updated_date'] ) ? $settings['updated_date'] : 0;
			$settings['scrolltop'] = isset( $settings['scrolltop'] ) ? $settings['scrolltop'] : 1;
			
			//index formats added in 1.4 - only work for non Ajax sections
			if($settings['disable_ajax'] != 1) {
				$pif = isset( $settings['pif'] ) ? $settings['pif'] : 'decimal';
				$cif = isset( $settings['cif'] ) ? $settings['cif'] : 'decimal';
				switch($pif) {
					case 'lower-roman':
						$i_disp=documentor_convert_int_to_roman($i, false);
						break;
					case 'upper-roman':
						$i_disp=documentor_convert_int_to_roman($i);
						break;
					case 'lower-alpha':
						$i_disp=documentor_convert_int_to_alpha($i, false);
						break;
					case 'upper-alpha':
						$i_disp=documentor_convert_int_to_alpha($i);
						break;
					default:
						$i_disp=$i;
				}
				
				switch($cif) {
					case 'lower-roman':
						$j_disp=documentor_convert_int_to_roman($j, false);
						break;
					case 'upper-roman':
						$j_disp=documentor_convert_int_to_roman($j);
						break;
					case 'lower-alpha':
						$j_disp=documentor_convert_int_to_alpha($j, false);
						break;
					case 'upper-alpha':
						$j_disp=documentor_convert_int_to_alpha($j);
						break;
					default:
						$j_disp=$j;
				}
			}
			else{
				$i_disp=$i;
				$j_disp=$j;
			}
			
			foreach( $sectiondata as $secdata ) {	
				$shtml = $sectionid = "";
				$postid = $secdata->post_id;
				if( $secdata->type == 0 ) $type = 'documentor-sections';
				else if( $secdata->type == 1 ) $type = 'post';
				else if( $secdata->type == 2 ) $type = 'page';
				else if( $secdata->type == 3 ) $type = 'link';
				else if( $secdata->type == 4 ) {
					$type = get_post_type( $postid );
				}
				$section_title = $menu_title = '';
				if( $k == 'parent' ) { 
					$scnt = $i_disp;
				}
				else {
					$scnt = $i.'.'.$j_disp;
				}
				$i = $scnt;
				$tclass = '';
				if( $i == 1 ) { $tclass = ' doc-first-sectitle'; }
				if( $secdata->type == 3 ) {
					$sec_title = '';
					$shtml.= $starttag.' class="doc-sec-title'.$tclass.'" style="display:none;"> ';
					$shtml.='<span class="doc-sec-count" '.$cntcss.'>'.$scnt.'.</span>';
					$shtml.=$sec_title.$endtag;
				}
				if( $secdata->type != 3 ) { //If not a link section
					/* if in url section is mentioned and load section using ajax is enabled then load that section  */
					if( isset( $_REQUEST['section'] ) && !empty( $_REQUEST['section'] ) && $i== 1 && $settings['disable_ajax'] == 1 ) {
						$sec_slug = sanitize_title( $_REQUEST['section'] );
						$secid = $wpdb->get_var( $wpdb->prepare( 
							"SELECT sec_id 
								FROM $tbl_section 
								WHERE slug = %s
							", 
							$sec_slug
						) );
						$ds = new DocumentorSection( $id, $secid);
						if( ($tempSecData = $ds->getsection( $secid ) ) !== null ) {
							$secdata=$tempSecData;
							$postid = $secdata->post_id;
							if( $secdata->type == 0 ) $type = 'documentor-sections';
							else if( $secdata->type == 1 ) $type = 'post';
							else if( $secdata->type == 2 ) $type = 'page';
							else if( $secdata->type == 3 ) $type = 'link';
							else if( $secdata->type == 4 ) {
								$type = get_post_type( $postid );
							}
						}
					}
					//pretty links
					$sectionid = 'section-'.$secdata->sec_id;
					if( !empty( $secdata->slug ) ) {
						$sectionid = apply_filters( 'editable_slug', $secdata->slug );
					}
					//WPML
					if( function_exists('icl_plugin_action_links') ) {	
						$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
						$section_title = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
						$menu_title = get_post_meta( $lang_post_id, '_documentor_menutitle', true );
						$postid = $lang_post_id;
					} else {
						$section_title = get_post_meta( $postid, '_documentor_sectiontitle', true );
						$menu_title = get_post_meta( $postid, '_documentor_menutitle', true );
					}
					//get last modified date
					$modified_date = $wpdb->get_var("SELECT post_modified FROM {$wpdb->posts} WHERE ID = ".$postid);
					$modified_date = date_create($modified_date);
					$modified_date = date_format($modified_date, 'M d, Y');
					$sec_title = ( !empty( $section_title ) ) ? $section_title : $menu_title;
					$shtml.= $starttag.' class="doc-sec-title'.$tclass.'" '.$cssarr['sectitle'].'> <span class="doc-sec-count" '.$cntcss.'>'.$scnt.'.</span>'.$sec_title;
					if( $settings['scrolltop'] == '1' ) {
						$shtml.= '<a class="scrollup doc-noprint">↑ Back to Top</a>';
					}
					$shtml.= $endtag;
					//front-end edit section
					$shtml .= '<div class="documentor-social doc-noprint">';
					if( post_type_exists($type) ) { 
						if ( is_user_logged_in() && current_user_can('edit_post', $postid)) {
							$edtlink = get_edit_post_link($postid);
							$shtml.= '<span class="doc-postedit-link"><a href="'.esc_url($edtlink).'" target="_blank">'. __('Edit','documentor').'</a></span>';
						}
					}
					//1.4 :fix for NGINX server
					$servername=$_SERVER['SERVER_NAME'];
					if( strpos($servername, '*') === false ){
						$currurl = (!empty($_SERVER['HTTPS'])) ? "https://".$servername.$_SERVER['REQUEST_URI'] : "http://".$servername.$_SERVER['REQUEST_URI'];
					}
					else{
						$currurl = get_permalink();
					}
					if( $settings['disable_ajax'] == 1 ) {
						$currurl = add_query_arg( array('section' => $sectionid), $currurl );
					} else {
						$currurl = $currurl."#".$sectionid;
					}
					if( $settings['button'][1] == 1 ) { 
						$shtml.= '<span onclick="prompt(\'Press Ctrl + C, then Enter to copy to clipboard\',\''.$currurl.'\')"> <span class="icon-uniF0C1 doc-icons"></span> </span>';
					}
					if( $settings['button'][2] == 1 ) {	
					$subject = ( !empty( $section_title ) ) ? $section_title : $menu_title;
					$shtml.= '<span><a href="mailto:?subject='.$subject.'&body='.$currurl.'"><span class="icon-uniF003 doc-icons"></span></a></span>';
					}
					if( $settings['disable_ajax'] == 1 ) {
						if( $settings['button'][3] == 1 && $secdata->pdf_id != 0 ) {
							//check if pdf file exists
							$attachdata = get_post( $secdata->pdf_id );
							if( $attachdata != NULL ) {
								$pdfExists=$this->docFileExists( $attachdata->guid );
								if( $pdfExists ) {
									$shtml.= '<span><form method="post" class="save_docpdf"><span class="save_secpdf_inline"> <span class="icon-uniF1C1 doc-icons"></span> </span><input type="hidden" name="doc_pdf" value="section_pdf" /><input type="hidden" name="secid" value="'.esc_attr($secdata->sec_id).'"><input type="hidden" name="docid" value="'.esc_attr($this->docid).'"> </form></span>';
								}
							}
						}
						if( $settings['button'][4] == 1 ) {
							$settings['window_print'] = isset( $settings['window_print'] ) ? $settings['window_print'] : 0;
							$shtml.= '<span><a class="doc-print" data-printspath="'.$stylesheetpath.'"> <span class="icon-uniF02F doc-icons"></span> </span></a></span>';
						}
					}
					$shtml .= '</div>';
				} 
				if( $settings['disable_ajax'] == 0 ) {
					$html.= '<div class="documentor-section '.esc_attr($tran_class).' section-'.esc_attr($secdata->sec_id).'" id="'.esc_attr($sectionid).'" data-section-id="'.esc_attr($secdata->sec_id).'">';
					$html .= $shtml;
					$html .= '<div class="doc-sec-content" '.$cssarr['sectioncontent'].'>';
					if( $secdata->type != 3	) { //not link section
						$post = get_post( $postid );
						if( $post != null ) {
							$pcontent = $post->post_content;
							$html .= apply_filters( 'the_content' , $pcontent );
						}
					} 
					$html .= '</div>';
					if( $secdata->type != 3	) { //not link section
						$html.= '<div class="documentor-help">';
								if( $settings['feedback'] == 1 ) {
							 		$html .= '<span class="icon-uniF118 doc-icons doc-noprint"></span> <span class="doc-noprint">'.__("Was this helpful?","documentor").'</span>
									<span class="doc-noprint"><a class="positive-feedback" href="#" > Yes </a></span>
									<span class="doc-noprint"><a class="negative-feedback" href="#" > No </a></span>';
								}
								if( $settings['suggest_edit'] == 1 ) {  
									$html .= '<span class="doc-noprint"><a title="Give suggestion" href="#sugestedit_popup'.$this->docid.'" rel="leanModal" class="spopupopen"><span class="icon-uniF044 doc-icons"></span> '.__('Suggest edit','documentor').' </a></span>';
								}
								if( $settings['updated_date'] == 1 ) {
									$html.='<div class="doc-mdate">'.__('Last updated on','documentor').' '.$modified_date.'</div>';
								}
								if( $settings['feedbackcnt'] == 1 ) {
									$totalvotes = $secdata->upvote + $secdata->downvote;
									$upvotes = $secdata->upvote;
									$html.='<div class="doc-feedbackcnt"><span class="upvote">'.$upvotes.'</span> of <span class="totalvote">'.$totalvotes.'</span>'.__(' users found this section helpful','documentor').'</div>';
								}
								$html .= '<div class="negative-feedbackform doc-noprint">
								</div>
								<div class="feedback-msg doc-noprint"></div> 
							</div>';
					}
					$html .= '</div>';
				} else {
					/* if in url section is present or it is first section */
					if( $i == 1 || ( isset( $_REQUEST['section'] ) && !empty( $_REQUEST['section'] ) && $i == 1 ) ) {
						//pretty links
						$sectionid = 'section-'.$secdata->sec_id;
						if( !empty( $secdata->slug ) ) {
							$sectionid = apply_filters( 'editable_slug', $secdata->slug );
						}
						$html.= '<div class="documentor-section '.$tran_class.' section-'.esc_attr($secdata->sec_id).'" id="'.esc_attr($sectionid).'" data-section-id="'.$secdata->sec_id.'">';
						$html .= $shtml;
						$html .= '<div class="doc-sec-content" '.$cssarr['sectioncontent'].'>';
						if( $secdata->type != 3	) { //not link section
							//social share buttons
							if( $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'top' ) {
								$html .= $guide->get_social_buttons( $settings, $sec_title, $currurl );
							}
							$post = get_post( $postid );
							if( $post != null ) {
								$pcontent = $post->post_content;
								$html .= apply_filters( 'the_content' , $pcontent );
							}
						} 
						$html .= '</div>';
						if( $secdata->type != 3	) { //not link section
							$html.= '<div class="documentor-help">';
								if( $settings['feedback'] == 1 ) {
									$html .= '<span class="icon-uniF118 doc-icons doc-noprint"></span> <span class="doc-noprint">'.__("Was this helpful?","documentor").'</span>
									<span class="doc-noprint"><a class="positive-feedback" href="#" > Yes </a></span> 
									<span class="doc-noprint"><a class="negative-feedback" href="#" > No </a></span>';   
								}
								if( $settings['suggest_edit'] == 1 ) {  
									$html .= '<span class="doc-noprint"><a title="Give suggestion" href="#sugestedit_popup'.$this->docid.'" rel="leanModal" class="spopupopen" ><span class="icon-uniF044 doc-icons"></span> '.__('Suggest edit','documentor').' </a></span>';
								}
								if( $settings['updated_date'] == 1 ) {
									$html.='<div class="doc-mdate">'.__('Last updated on','documentor').' '.$modified_date.'</div>';
								}
								if( $settings['feedbackcnt'] == 1 ) {
									$totalvotes = $secdata->upvote + $secdata->downvote;
									$upvotes = $secdata->upvote;
									$html.='<div class="doc-feedbackcnt"><span class="upvote">'.$upvotes.'</span> of <span class="totalvote">'.$totalvotes.'</span>'.__(' users found this section helpful','documentor').'</div>';
								}
								$html .= '<div class="negative-feedbackform doc-noprint">
			
								</div>
								<div class="feedback-msg doc-noprint"></div>'; 
								//social share buttons
								if( $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'bottom' ) {
									$html .= $guide->get_social_buttons( $settings, $sec_title, $currurl );
								}
								$html .= '</div>';	
						}
						$html .= '<div class="doc-nav doc-noprint">';
						$html .= '<a data-href="#" class="doc-firstnext">'.__('Next', 'documentor').'</a>';
						$html .= '</div>';
						$html .= '</div>';
					}
				}

				if ( isset( $obj->children ) && $obj->children ) {
					$j = 0;
					foreach( $obj->children as $child ) {
					    $j++;$k = 'child';
					    $html .= $this->buildFrontSections($child, $i, $k, $j);
					}

				}
			}//foreach

		}// if documentor section present
		return $html;
	}
	//get section content with ajax if setting is enabled
	function get_section_ajaxcontent( $docid, $secid, $currenturl, $nextsecid, $prevsecid, $nextsecname, $prevsecname ) {
		$guide = new DocumentorGuide( $docid );
		$settings = $guide->get_settings();
		$cntcss = 'style="display:none"';
		if( $settings['indexformat'] == 1 ) {
			$cntcss = 'style="display:inline-block"';
		}
		//new field added in v1.1
		$settings['updated_date'] = isset( $settings['updated_date'] ) ? $settings['updated_date'] : 0;
		$settings['scrolltop'] = isset( $settings['scrolltop'] ) ? $settings['scrolltop'] : 1;
		if( $settings['disable_ajax'] == 1 ) { //if ajax is enabled
			global $wpdb,$table_prefix;
			$cssarr = $guide->get_inline_css();
			$sec = new DocumentorSection();
			$secdata = $sec->getsection( $secid );
			$html =	$shtml = "";
			$root = 'skins/'.$settings['skin'];
			$editpng = Documentor::documentor_plugin_url( $root."/images/edit.png" );
			$postid = $secdata->post_id;
			if( $secdata->type == 0 ) $type = 'documentor-sections';
			else if( $secdata->type == 1 ) $type = 'post';
			else if( $secdata->type == 2 ) $type = 'page';
			else if( $secdata->type == 3 ) $type = 'link';
			else if( $secdata->type == 4 ) {
				$type = get_post_type( $postid );
			}
			$section_title = '';
			//section title heading html tag setting
			$starttag = '<h3'; 
			$endtag = '</h3>'; 
			if( isset( $settings['section_element'] ) ) {
				for( $h = 1; $h <= 6; $h++ ) {
					if( $settings['section_element'] == $h ) {
						$starttag = '<h'.$h; 
						$endtag = '</h'.$h.'>'; 
					} 
				}
			} 
			//WPML
			if( function_exists('icl_plugin_action_links') ) {	
				$lang_post_id = icl_object_id( $postid , $type, true, ICL_LANGUAGE_CODE );
				$section_title = get_post_meta( $lang_post_id, '_documentor_sectiontitle', true );
				$menu_title = get_post_meta( $lang_post_id, '_documentor_menutitle', true );
				$postid = $lang_post_id;
			} else {
				$section_title = get_post_meta( $postid, '_documentor_sectiontitle', true );
				$menu_title = get_post_meta( $postid, '_documentor_menutitle', true );
			}
			//get last modified date
			$modified_date = $wpdb->get_var("SELECT post_modified FROM {$wpdb->posts} WHERE ID = ".$postid);
			$modified_date = date_create($modified_date);
			$modified_date = date_format($modified_date, 'M d, Y');
			
			$tran_class = "";
			if( !empty( $settings['animation'] ) ) {
				$tran_class = "wow documentor-animated documentor-".$settings['animation'];
			}
			if( $secdata->type != 3 ) { //If section is not a link
				$url1 = Documentor::documentor_plugin_url( $root."/images/link.png" );
				$url2 = Documentor::documentor_plugin_url( $root."/images/message.png" );
				$url3 = Documentor::documentor_plugin_url( $root."/images/pdf.png" );
				$url4 = Documentor::documentor_plugin_url( $root."/images/document-print.png" );
				$url5 = Documentor::documentor_plugin_url( $root."/images/feedback.png" );
				$stylesheetpath = Documentor::documentor_plugin_url( 'skins/mint/print.css' );
				//pretty links
				$sectionid = 'section-'.$secdata->sec_id;
				if( !empty( $secdata->slug ) ) {
					$sectionid = apply_filters( 'editable_slug', $secdata->slug );
				}
				$sec_title = ( !empty( $section_title ) ) ? $section_title : $menu_title;
				$html.= '<div class="documentor-section '.$tran_class.' section-'.esc_attr($secdata->sec_id).'" id="'.esc_attr($sectionid).'" data-section-id="'.$secdata->sec_id.'">';
				$shtml.= $starttag.' class="doc-sec-title doc-ajax-sectitle" '.$cssarr['sectitle'].'><span class="doc-sec-count" '.$cntcss.'></span> '.$sec_title;
				if( $settings['scrolltop'] == '1' ) {
					$shtml.= '<a class="scrollup doc-noprint" >↑ Back to Top</a>';
				}
				$shtml.= $endtag;
				//front-end edit section
				$shtml .= '<div class="documentor-social doc-noprint">';
				if( post_type_exists($type) ) { 
					if ( is_user_logged_in() && current_user_can('edit_post', $postid)) {
						$edtlink = get_edit_post_link($postid);
						$shtml.= '<span class="doc-postedit-link"><a href="'.$edtlink.'" target="_blank">'. __('Edit','documentor').'</a></span>';
					}
				}
				$currurl = '';
				if( !empty( $currenturl ) ) {
					$currurl = add_query_arg( array('section' => $sectionid), urldecode($currenturl) );
				}
				if( $settings['button'][1] == 1 ) { 
					$shtml.= '<span onclick="prompt(\'Press Ctrl + C, then Enter to copy to clipboard\',\''.$currurl.'\')"> <span class="icon-uniF0C1 doc-icons"></span> </span>';
				}
				if( $settings['button'][2] == 1 ) {
					$subject = ( !empty( $section_title ) ) ? $section_title : $menu_title;
					$shtml.= '<span><a href="mailto:?subject='.$subject.'&body='.$currurl.'"><span class="icon-uniF003 doc-icons"></span></a></span>';
				}
				if( $settings['disable_ajax'] == 1 ) {
					if( $settings['button'][3] == 1 && $secdata->pdf_id != 0 ) {
						$attachdata = get_post( $secdata->pdf_id );
						if( $attachdata != NULL ) {
							$pdfExists=$this->docFileExists( $attachdata->guid );
							if( $pdfExists ) {
								$shtml.= '<span><form method="post" class="save_docpdf"><span class="save_secpdf_inline"> <span class="icon-uniF1C1 doc-icons"></span> </span><input type="hidden" name="doc_pdf" value="section_pdf" /><input type="hidden" name="secid" value="'.esc_attr($secdata->sec_id).'"><input type="hidden" name="docid" value="'.esc_attr($docid).'"> </form></span>';
							}
						}
					}
					if( $settings['button'][4] == 1 ) {
						$settings['window_print'] = isset( $settings['window_print'] ) ? $settings['window_print'] : 0;
						$shtml.= '<span><a class="doc-print" data-printspath="'.$stylesheetpath.'"> <span class="icon-uniF02F doc-icons"></span> </span></a></span>';
					}
				}
				$shtml .= '</div>';
			} 
			$html .= $shtml;
			$html .= '<div class="doc-sec-content" '.$cssarr['sectioncontent'].'>';
			if( $secdata->type != 3 ) { //not link section
				//social share buttons
				if( $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'top' ) {
					$html .= $guide->get_social_buttons( $settings, $sec_title, $currurl );
				}
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
				} else {
					$post = get_post( $postid );
				}
				if( $post != null ) {
					$pcontent = $post->post_content;
					$html .= apply_filters( 'the_content' , $pcontent );
				}
			} 
			$html .= '</div>';
			if( $secdata->type != 3	) { //not link section
				$html.= '<div class="documentor-help">';
					if( $settings['feedback'] == 1 ) {
						$html .= '<span class="icon-uniF118 doc-icons doc-noprint"></span><span class="doc-noprint"> '.__("Was this helpful?","documentor").'</span>
						<span class="doc-noprint"><a class="positive-feedback" href="#" > '.__('Yes','documentor').' </a></span>
						<span class="doc-noprint"><a class="negative-feedback" href="#" > '.__('No','documentor').' </a></span>';
					} 
					if( $settings['suggest_edit'] == 1 ) {    
						$html .= '<span class="doc-noprint"><a title="Give suggestion" href="#sugestedit_popup'.$docid.'" rel="leanModal" class="spopupopen" ><img height="15" width="15" src='.$editpng.'> '.__('Suggest edit','documentor').' </a></span>';
					}
					if( $settings['updated_date'] == 1 ) {
						$html.='<div class="doc-mdate">'.__('Last updated on','documentor').' '.$modified_date.'</div>';
					}
					if( $settings['feedbackcnt'] == 1 ) {
						$totalvotes = $secdata->upvote + $secdata->downvote;
						$upvotes = $secdata->upvote;
						$html.='<div class="doc-feedbackcnt"><span class="upvote">'.$upvotes.'</span> of <span class="totalvote">'.$totalvotes.'</span>'.__(' users found this section helpful','documentor').'</div>';
					}
					$html .= '<div class="negative-feedbackform doc-noprint">

					</div>
					<div class="feedback-msg doc-noprint"></div>';
					//social share buttons 
					if( $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'bottom' ) {
						$html .= $guide->get_social_buttons( $settings, $sec_title, $currurl );
					}
					$html .= '</div>';
			}
			$html.= '<div class="doc-nav doc-noprint">';
			if( ( $prevsecid != '0' ) && !empty( $prevsecid ) ) {
				if( !empty( $prevsecname ) ) {
					$html .= '<a data-href="'.$prevsecid.'" class="doc-prev">&laquo; '.stripslashes($prevsecname).'</a>';
				} else {
					$html .= '<a data-href="'.$prevsecid.'" class="doc-prev">&laquo; '.__('Previous','documentor').'</a>';
				}
			}
			if( ( $nextsecid != '0' ) && !empty( $nextsecid ) ) {
				if( !empty( $nextsecname ) ) {
					$html .= '<a data-href="'.$nextsecid.'" class="doc-next">'.stripslashes($nextsecname).' &raquo;</a>';
				} else {
					$html .= '<a data-href="'.$nextsecid.'" class="doc-next">'.__('Next','documentor').' &raquo;</a>';
				}
			}
			$html .= '</div>';
			$html.= '</div>';
			return $html;
		} else {
			return "0";
		}
	}
	//function to display document at front-end
	function display() {
		$guideobj = new DocumentorGuide( $this->docid );
		$settings = $guideobj->get_settings();
		$currentlink = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$cssarr = $guideobj->get_inline_css();
		$stylesheetpath = Documentor::documentor_plugin_url( 'skins/mint/print.css' );
		//new field added in v1.1
		$settings['window_print'] = isset( $settings['window_print'] ) ? $settings['window_print'] : 0;
		
		//enqueue required files
		wp_enqueue_script( 'doc_fixedjs', Documentor::documentor_plugin_url( 'core/js/jquery.lockfixed.js' ), array('jquery'), DOCUMENTOR_VER, false);
		if( $settings['button'][4] == 1 ) {
			wp_enqueue_script( 'doc_print', Documentor::documentor_plugin_url( 'core/js/jQuery.print.js' ), array('jquery'), DOCUMENTOR_VER, false);
			if( $settings['window_print'] == 0 ) {
				wp_enqueue_style( 'doc_'.$settings['skin'].'_printcss', Documentor::documentor_plugin_url( 'skins/mint/print_style.css' ), false, DOCUMENTOR_VER, 'print');	
			} 
		}
		if( $settings['socialshare'] == 1 ) {
			wp_enqueue_style( 'doc_socialshare', Documentor::documentor_plugin_url( 'core/css/socialshare_fonts.css' ), false, DOCUMENTOR_VER);	
		} 
		wp_enqueue_script( 'doc_leanmodal', Documentor::documentor_plugin_url( 'core/js/jquery.leanModal.min.js' ), array('jquery'), DOCUMENTOR_VER, false);
		wp_enqueue_script( 'doc_js', Documentor::documentor_plugin_url( 'core/js/documentor.js' ), array( 'jquery','jquery-ui-autocomplete' ), DOCUMENTOR_VER );
		wp_localize_script( 'doc_js', 'DocAjax', array( 'docajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		//create html structure
		$html = "";
		if( !empty( $guideobj->sections_order ) ) {
			//include skin stylesheet
			global $doc_style_counter_mint;
			if( !isset( $doc_style_counter_mint ) or $doc_style_counter_mint < 1 ) {
				$html .="<link rel='stylesheet' href='".Documentor::documentor_plugin_url( 'skins/mint/style.css' )."' type='text/css' media='all' />";
				$doc_style_counter_mint++;
			}
			$rtl_support = isset($settings['rtl_support']) ? $settings['rtl_support'] : '0'; 
			$wrapclass = '';
			if( $rtl_support == '1' ) $wrapclass = ' documentor-rtl';
			//wrap div
			$html .= '<div id="documentor-'.$this->docid.'" class="documentor-'.$settings['skin'].' documentor-wrap'.$wrapclass.'" data-docid = "'.$this->docid.'" >';
			$root = 'skins/'.$settings['skin'];
			$sericonurl = Documentor::documentor_plugin_url( $root."/images/search.png" );
			if( $settings['disable_ajax'] == 0 ) {
				$html .= '<div class="dcumentor-topicons doc-noprint">';
				if( isset( $settings['search_box'] ) && $settings['search_box'] == '1' ) {
					$html .= '<span class="doc-search">
							<input type="text" name="search_document" class="search-document" placeholder="'.__('Search in Document','documentor').'" />
							<img src="'.$sericonurl.'" />
						</span>';
				}
				$html .= '<span class="doc-topiconswrap">';
				if( $settings['button'][3] == 1 && $guideobj->pdf_id != 0 ) {
					$attachdata = get_post( $guideobj->pdf_id );
					if( $attachdata != NULL ) {
						$pdfExists=$this->docFileExists( $attachdata->guid );
						if( $pdfExists ) {
							$html.= '<span><form method="post" class="save_docpdf"><span class="save_secpdf"> <span class="icon-uniF1C1 doc-icons"></span> </span><input type="hidden" name="doc_pdf" value="document_pdf" /><input type="hidden" name="doc_id" value="'.esc_attr($this->docid).'"></form></span>';
						}
					}
				}
				if( $settings['button'][4] == 1 ) {
					$html.= '<a class="doc-print" data-printspath="'.$stylesheetpath.'"><span class="icon-uniF02F doc-icons"></span> </a>';
				}
				$html .= '</span></div>';
			} else {
				if( isset( $settings['search_box'] ) && $settings['search_box'] == '1' ) {
					$html .= '<div class="dcumentor-topicons doc-noprint">
							<span class="doc-search">
								<input type="text" name="search_document" class="search-document" placeholder="'.__('Search in Document','documentor').'" />
								<img src="'.$sericonurl.'" />
							</span>
						</div>';
				}
			}
			//Guide Title
			if( $settings['guidetitle'] == 1 ) {
				$starttag = '<h2'; 
				$endtag = '</h2>'; 
				if( isset( $settings['guidet_element'] ) ) {
					for( $h = 1; $h <= 6; $h++ ) {
						if( $settings['guidet_element'] == $h ) {
							$starttag = '<h'.$h; 
							$endtag = '</h'.$h.'>'; 
						} 
					}
				} 
				$html .= '<div class="doc-guidetitle">'.$starttag.' class="doc-title" '.$cssarr['guidetitle'].'>'.$guideobj->title.$endtag.'</div>';
			}
			//navigation menu
			$menupos = isset($settings['menu_position']) ? $settings['menu_position'] : 'left'; 
			$menuclass = $sec_containerclass = '';
			if( $menupos == 'right' ) {
				$menuclass = ' doc-menuright';
				$sec_containerclass = ' doc-seccontainer-left';
			}
			if( $settings['togglemenu'] == 1 ) {
				$menuclass .= ' toggle';
			}
			$html .= ' 	<div class="doc-menu'.$menuclass.' doc-noprint" ><div class="doc-menurelated">';
			$obj = $guideobj->sections_order;
			if( !empty( $obj ) ) {
				$jsonObj = json_decode( $obj );
				$html.='<ol class="doc-list-front">';
				$i = 0;
				foreach( $jsonObj as $jobj ) {
					$i++;$k = 'parent';$j = '';
					$html.= $this->buildFrontMenus($jobj, $i, $k, $j);
				}
				$html.='</ol>';
				//relaevant links
				if( $settings['related_doc'] == 1 ) {
					$guide = $guideobj->get_guide( $this->docid );
					if( $guide->rel_id != 0 ) {
						$html .= '<div class="documentor-related" '.$cssarr['navmenu'].'>';
							$html .= '<div class="documentor-relatedtitle"> '.$guide->rel_title.' </div>';
							$menu_items = wp_get_nav_menu_items( $guide->rel_id );
							$html .= '<div class="doc-related-links">';
							foreach ( (array) $menu_items as $key => $menu_item ) {
							    $title = $menu_item->title;
							    $url = $menu_item->url;
							    $html .= '<div><img src="'.Documentor::documentor_plugin_url( 'skins/mint/images/lnk.png').'" /><a href="' . $url . '" '.$cssarr['navmenu'].' >' . $title . '</a></div>';
							}
							$html .= '</div>';
						$html .= '</div>';
					}	
				}
			} 
			$html.=	'</div></div>';
			if( !empty( $obj ) ) {
			$jsonObj = json_decode( $obj );
			$html.='<div class="doc-sec-container'.$sec_containerclass.'" id="documentor_seccontainer">';
			$i = 0;
			//display product information
			if( $settings['productdetails'] == 1 ) {
				$html .= '<div class="doc-prodinfo">';
					if( isset( $settings['prodimg'] ) && !empty( $settings['prodimg'] )) {
						$astart = $aend = '';
						if( isset( $settings['prodlink'] ) && !empty( $settings['prodlink'] )) {
							$astart = '<a href="'.$settings['prodlink'].'" target="_blank">';
							$aend = '</a>';
						}
						$html .= '<span class="doc-prodleft">'.$astart.'<img src="'.$settings['prodimg'].'" class="doc-prodimg" />'.$aend.'</span>';	
					}
					$html .= '<div class="doc-prodright">';
						if( isset( $settings['prodname'] ) && !empty( $settings['prodname'] ) ) {
							$html .= '<div class="doc-prodname">'.$settings['prodname'].'</div>';
						}
						if( isset( $settings['guide'] ) ) {
							$uidarr = $settings['guide'];
							if( is_array( $uidarr ) && count( $uidarr ) > 0 ) {
								$firstgm = $uidarr[0];
								$user_info = get_userdata($firstgm);
								$html .= '<div class="doc-prodauthor">'.__('Author: ').$user_info->display_name.'</div>';
							}
						}
						if( isset( $settings['prodversion'] ) && !empty( $settings['prodversion'] ) ) {
							$html .= '<div class="doc-prodversion">'.__('Version: ','documentor').$settings['prodversion'].'</div>';
						}
						if( isset( $guideobj->createdon ) && $guideobj->createdon != 0 ) {
							$createdon = new DateTime($guideobj->createdon);
							$createdon = $createdon->format('d M Y');
							$html .= '<div class="doc-productdt">'.__('Dated: ','documentor').$createdon.'</div>';
						}
						$html .= '</div>';
				$html .= '</div>';
			}
			//add social share buttons at top of the document
			if( $settings['disable_ajax'] == 0 && $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'top' ) {
				$guidetitle = $guideobj->title;
				$html .= $guideobj->get_social_buttons( $settings, $guidetitle, $currentlink ); 
			}  
			foreach( $jsonObj as $jobj ) {
				$i++;$k = 'parent';$j = '';
				$html.= $this->buildFrontSections($jobj, $i, $k, $j);
			}
			//add social share buttons at bottom of the document
			if( $settings['disable_ajax'] == 0 && $settings['socialshare'] == 1 && $settings['sbutton_position'] == 'bottom' ) {
				$guidetitle = $guideobj->title;
				$html .= $guideobj->get_social_buttons( $settings, $guidetitle, $currentlink ); 
			}  
			$html.='</div><!--.doc-sec-container-->';
			//suggestedit popup
			$html.='<div id="sugestedit_popup'.$this->docid.'" class="sugestedit_popup">
					<a class="modal_close"></a>
					<form name="documentor-suggestform" method="post" class="documentor-suggestform">
						<div class="doc-frmdiv" style="font-weight: bold;">
						'.__("Suggest Edit","documentor").'
						</div>
						<div class="doc-frmdiv">
							<input type="text" name="sec_title" class="sedit-sectitle txtinput" value="" />
						</div>';
						if( $settings['sedit_frmname'] == 1 ) {
							$html.='<div class="doc-frmdiv">
								<input type="text" name="name" class="txtinput" placeholder="Name" />
							</div>';	
						}
						if( $settings['sedit_frmemail'] == 1 ) {
							$html.='<div class="doc-frmdiv">
								<input type="email" class="emailinput" placeholder="Email" name="email" /> 
							</div>';
						}
						if( $settings['sedit_frmmsgbox'] == 1 ) {
							$html.='<div class="doc-frmdiv">
								<textarea name="content" class="textareainput" placeholder="Post your suggestion. . ."></textarea>
							</div>';
						}
						if( !empty( $settings['sedit_frminputs'] ) ) {
							$inputs = explode(',',$settings['sedit_frminputs']);
							foreach( $inputs as $input ) {
								$html.='<div class="doc-frmdiv"><input type="text" class="txtinput" name="'.trim($input).'" placeholder="'.trim($input).'"></div>';
							}
						}
						if( $settings['sedit_frmcapcha'] == 1 ) {
							$nm = 'sedit-doc-captcha'.$this->docid;
							$trans_name = 'sedit_session_id'.$this->docid;
							$html.='<div class="doc-frmdiv"><label> Captcha :&nbsp; </label><span class="doc-sedit-captcha"></span></div>';											
						}
						if( !empty( $settings['sedit_frminputs'] ) ) {
							$html .= '<input type="hidden" name="sedit_extrainputs" value="'.$settings['sedit_frminputs'].'">';
						}
						$html.='<input type="hidden" class="sedit-secid" name="secid" value="" />
						<input type="hidden" class="sedit-postid" name="sedit_postid" value="" />
						<input type="hidden" class="sedit-docid" name="docid" value="'.$this->docid.'" />
						<button class="docsubmit-suggestform"> Submit </button>
					</form>
				</div>';
			}       
			$clearclass = '';
			if( $rtl_support == '1' ) { $clearclass = ' cleardiv-rtl'; }   
			$html .='</div><div class="cleardiv'.$clearclass.'"> </div><div id="documentor-'.$this->docid.'-end"></div>' ;
			$secstyle ='';
			if( $settings['indexformat'] == 1 ) {
				$reptxt = 'style="';
				$secstyle = str_replace($reptxt,"",$cssarr['navmenu']);
				$secstyle = rtrim($secstyle, '"');
			}
			if( !empty ( $settings['animation'] ) ) {
				wp_enqueue_script( 'documentor_wowjs', Documentor::documentor_plugin_url( 'core/js/wow.js' ), array('jquery'), DOCUMENTOR_VER, false);
			}
			$settings['scrolling'] = ( !isset( $settings['scrolling'] )  ) ? 1 : $settings['scrolling']; 
			$settings['fixmenu'] = ( !isset( $settings['fixmenu'] )  ) ? 1 : $settings['fixmenu']; 
			$settings['menuTop'] = ( !isset( $settings['menuTop'] )  ) ? '0' : $settings['menuTop'];
			$settings['scroll_size'] = ( !isset( $settings['scroll_size'] )  ) ? 3 : $settings['scroll_size']; 
			$settings['scroll_color'] = ( !isset( $settings['scroll_color'] )  ) ? '#F45349' : $settings['scroll_color']; 
			$settings['scroll_opacity'] = ( !isset( $settings['scroll_opacity'] )  ) ? 0.4 : $settings['scroll_opacity'];
			$script =  '<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#documentor-'.$this->docid.'").documentor({
					documentid	: '.$this->docid.',
					docid		: "documentor-'.$this->docid.'",
					animation	: "'.$settings['animation'].'",
					indexformat	: "'.$settings['indexformat'].'",
					pformat		: "'.$settings['pif'].'",
					cformat		: "'.$settings['cif'].'",					
					secstyle	: "'.$secstyle.'",
					actnavbg_default: "'.$settings['actnavbg_default'].'",
					actnavbg_color	: "'.$settings['actnavbg_color'].'",
					enable_ajax	: "'.$settings['disable_ajax'].'",
					scrolling	: "'.$settings['scrolling'].'",
					fixmenu		: "'.$settings['fixmenu'].'",
					skin		: "mint",
					scrollBarSize	: "'.$settings['scroll_size'].'",
					scrollBarColor	: "'.$settings['scroll_color'].'",
					scrollBarOpacity: "'.$settings['scroll_opacity'].'",
					windowprint	: "'.$settings['window_print'].'",
					menuTop: "'.$settings['menuTop'].'",
					iconscroll	: '.$settings['iconscroll'].',
					socialshare	: '.$settings['socialshare'].',
					sharecount	: '.$settings['sharecount'].',
					fbshare		: '.$settings['socialbuttons'][0].',
					twittershare	: '.$settings['socialbuttons'][1].',
					gplusshare	: '.$settings['socialbuttons'][2].',
					pinshare	: '.$settings['socialbuttons'][3].',
				});	
			});</script>'; 
			$html .= $script;
			return $html;
		}//if section order is present
	}//function display ends
	
	/**
	 * Check if file exists out there in the upload folder.
	 *
	 * @param string $url - preferably a fully qualified URL
	 * @return boolean - true if it is out there somewhere
	 */
	function docFileExists($url) {
	    if (($url == '') || ($url == null)) { return false; }
	    $response = wp_remote_head( $url, array( 'timeout' => 5 ) );
	    $accepted_status_codes = array( 200, 301, 302 );
	    if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes ) ) {
		return true;
	    }
	    return false;
	}
}//class ends
?>
