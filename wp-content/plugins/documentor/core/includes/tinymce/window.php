<?php
// look up for the path
require_once( dirname( dirname(__FILE__) ) . '/doc-config.php');
// check for rights
if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
global $wpdb,$table_prefix;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Embed Documents or Elements</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php wp_print_scripts('jquery');?>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo DOCPRO_URLPATH; ?>core/includes/tinymce/tinymce.js"></script> 
	<base target="_self" />
	<style>
		.doc-qt-content {
			padding: 20px;
		}
		.doc-select-document {
			font-family: 'Century Gothic', 'Avant Garde', 'Trebuchet MS', sans-serif;
			font-size: 10px;
		}
		.doc-qt-title {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 15px;
			font-weight: 600;
			margin-bottom: 10px;
			margin-top: 10px;
			color: #555;
			float: left;
			width: 100%;
		}
		.documentor-created-docs {
			min-width: 150px;
			width: auto;
			text-align: center;
			margin: 15px 15px 15px 0;
			float: left;
		}
		.documentor-created-doc {
			min-width: 150px;
			font-size: 12px;
			font-weight: bold;
			padding: 5px;
			margin-bottom: 5px;
			color: #fff;
			background: #E85A5A;
			border: 0;
			cursor: pointer;
		}
		.doc-elementsfrm, .doc-elementsfrm div {
			float: left;
			width: 100%;
		}
		.doc-frmdiv {
			margin-bottom: 20px;
		}
		.styled-select select {
			border: 1px solid #ddd;
			-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			background-color: #fff;
			color: #333;
			outline: 0;
			-webkit-transition: .05s border-color ease-in-out;
			transition: .05s border-color ease-in-out;
			padding: 2px;
			line-height: 28px;
			height: 28px;
			vertical-align: middle;
			margin: 1px;
			font-size: 14px;
			width: 180px;
		}
		.doc-frmdiv textarea {
			border: 1px solid #ddd;
			-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			background-color: #fff;
			color: #333;
			outline: 0;
			-webkit-transition: .05s border-color ease-in-out;
			transition: .05s border-color ease-in-out;
			width: 180px;
			font-size: 14px;
			line-height: 1.4;
		}
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(".doc-callouts").change(function() {
				var elval=jQuery(".doc-callouts").val();
				var docatts = '';
				if( elval == 'callout' ){
					docatts = '<div class="doc-frmdiv"><div class="styled-select"><select name="type" class="doc-eltype"><option value="warning">Warning</option><option value="error">Error</option><option value="message">Message</option><option value="note">Note</option></select></div></div><div class="doc-frmdiv"><textarea name="callout_contents" cols="20" rows="4"></textarea></div>';
				}
				jQuery(".doc-elcontent").html(docatts);	
			});
		});
	</script>
</head>
<body id="link" onLoad="tinyMCEPopup.executeOnLoad('documentorInit();');document.body.style.display='';" style="display: none">
	<div class="doc-qt-body">
		<div class="doc-qt-content">
			<form name="documentor-qtform" id="docqtform" action="#" class="doc-select-document">
				<?php
				$html = '';	
				$html .= '<div class="doc-qt-title">
					'.__('Embed Already Built Documents','documentor').'
				</div>'; 
				$doc_table = $table_prefix.DOCUMENTOR_TABLE;
				$sql = "SELECT * FROM $doc_table ORDER BY doc_id DESC";
				$result = $wpdb->get_results($sql);
				foreach($result as $res) {
					$html .= '<div class="documentor-created-docs">';
					$shortcode = "[documentor $res->doc_id]";
					$title = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM ".$table_prefix."posts WHERE ID = %d", $res->post_id ) );
					
					$html .= '<button value="'.$shortcode.'" class="documentor-created-doc" onClick="insertDocumentorShortcode({createdDoc:1,shortCode:this.value});" >'.$title.'</button>';
					$html .= '</div>';
				}
				$html .= '<div class="doc-qt-title">
					'.__('Insert Elements','documentor').'
				</div>
				<div class="doc-elementsfrm">
						<div class="doc-frmdiv">
							<div class="styled-select">
								<select name="element" class="doc-callouts">
									<option value="">Select Element</option>
									<option value="callout">Callouts</option>
								</select>
							</div>
						</div>
						<div class="doc-elcontent">
						
						</div>
						<div class="doc-frmdiv">
							<input type="submit" class="button-primary" id="insert" name="insert" value="Insert" onclick="insertDocElementShortcode();">
						</div>
				</div>'; 
				print($html);
			?>
			</form>
		</div>
	</div>
</body>
</html>
