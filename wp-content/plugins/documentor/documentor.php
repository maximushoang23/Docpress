<?php /*********************************************************
Plugin Name: Documentor
Plugin URI: http://documentor.in/
Description: Best plugin to create documentation and guides on WordPress.
Version: 1.4.6
Author: Tejaswini Deshpande
Author URI: http://tejaswinideshpande.com/
Text Domain: documentor
Wordpress version supported: 3.6 and above
*----------------------------------------------------------------*
* Copyright 2015 - 2016 WebFanzine Media  (email : support@documentor.in)
 * This is version edited by ThimPress
*****************************************************************/
class Documentor{
	var $documentor;
	public $default_documentor_settings;
	public $documentor_global_options;
	function __construct()
	{
		$blog_title = get_bloginfo( 'name' );
		$guide_subtitle = ( !empty( $blog_title ) ) ? 'by '.$blog_title : '';
		$this->_define_constants();
		$this->default_documentor_settings = array(
			'skin' => 'default',
			'animation' => '',
			'indexformat' => 1,
			'pif' =>'decimal',
			'cif' =>'decimal',			
			'navmenu_default' => 1,
			'navt_font' =>'regular',
			'navmenu_tfont' => 'Arial,Helvetica,sans-serif',
			'navmenu_tfontg' => '',
			'navmenu_tfontgw' => '',
			'navmenu_tfontgsubset' => '',
			'navmenu_custom' => '',
			'navmenu_color' => '#000000',
			'navmenu_fsize' => '14',
			'navmenu_fstyle' => 'normal',
			'actnavbg_default' => 1,
			'actnavbg_color' =>'#f3b869',
			'section_element' => '3',
			'sectitle_default' => 1,
			'sect_font' => 'regular',
			'sectitle_color' => '#000000',
			'sectitle_font' => 'Helvetica,Arial,sans-serif',
			'sectitle_fontg' => '',
			'sectitle_fontgw' => '',
			'sectitle_fontgsubset' => '',
			'sectitle_custom' => '',
			'sectitle_fsize' => '28',
			'sectitle_fstyle' => 'normal',
			'seccont_default' => 1,
			'seccont_color' => '#000000',
			'secc_font' => 'regular',
			'seccont_font' => 'Arial,Helvetica,sans-serif',
			'seccont_fontg' => '',
			'seccont_fontgw' => '',
			'seccont_fontgsubset' => '',
			'seccont_custom' => '',
			'seccont_fsize' => '14',
			'seccont_fstyle' => 'normal',
			'disable_ajax' => '0',
			'scrolling' => '1',
			'button'=>array('1','1','1','1','1','1'),
			'suggest_edit' => '1',
			'guide' => array(),
			'related_doc' => '',
			'feedback' => '1',
			'sedit_frmname' => '1',
			'sedit_frmemail' => '1',
			'sedit_frminputs' => '',
			'sedit_frmmsgbox' => '1',
			'sedit_frmcapcha' => '1',
			'sedit_frmsubject' => 'Edit Suggested for {doc-title} - {section-title}',
			'sedit_thankyoumsg' => 'Thank you for suggesting the edit. Your inputs, suggestions and feedback are extremely valuable and help us serve our customers better',
			'guide_subtitle' => $guide_subtitle,
			'feedback_frmname' => '1',
			'feedback_frmemail' => '1',
			'feedback_frminputs' => '',
			'feedback_frmtext' => '1',
			'feedback_frmcapcha' => '1',
			'feedback_frmsubject' => 'Feedback Submited for {doc-title} - {section-title}',
			'feedback_thankyoumsg' => 'Thank you for your feedback. Your inputs, suggestions and feedback are extremely valuable and help us serve our customers better',
			'pdf_title_font' => 'helveticab',
			'pdf_title_fsize' => '22',
			'pdf_subt_font' => 'helvetica',
			'pdf_subt_fsize' => '14',
			'pdf_menut_font' => 'helvetica',
			'pdf_menut_fsize' => '8',
			'pdf_sect_font' => 'helveticab',
			'pdf_sect_fsize' => '14',
			'pdf_secc_font' => 'helvetica',
			'pdf_secc_fsize' => '10',
			'pdf_headertitle' => '',
			'pdf_headerlogo' => '',
			'pdf_headerlogow' => '',
			'pdf_headerborder' => '0',
			'pdf_headercolor' => '#646464',
			'pdf_headerbrcolor' => '#FFFFFF',
			'pdf_headerfirst' => '1',
			'pdf_footertxt' => '',
			'pdf_footerfirst' => '1',
			'pdflinks' => '1',
			'fixmenu' => '1',
			'menuTop' => '0',
			'scroll_size' => '3', 
			'scroll_color' => '#F45349', 
			'scroll_opacity' => '0.4',
			'rtl_support' => '0',
			'menu_position' => 'left',
			'window_print' => '0',
			'updated_date' => '0',
			'scrolltop' => '1',
			'search_box' => '0',
			'iconscroll' => '1',
			'feedbackcnt' => '0',
			'socialshare' => '0',
			'sharecount' => '1', 
			'socialbuttons' => array('1','1','1','1'),
			'sbutton_style' => 'square',
			'sbutton_position' => 'bottom',
			'togglemenu' => '0',
			'productdetails' => '0',
			'prodlink' => '',
			'prodimg' => '',
			'prodname' => '',
			'prodversion' => '',
			'guidetitle' => '0',
			'guidet_element' => '2',
			'guidet_default' => 1,
			'guidet_font' => 'regular',
			'guidet_color' => '#000000',
			'guidetitle_font' => 'Arial,Helvetica,sans-serif',
			'guidet_fontg' => '',
			'guidet_fontgw' => '',
			'guidet_fontgsubset' => '',
			'guidet_custom' => '',
			'guidet_fsize' => '38',
			'guidet_fstyle' => 'normal',
		);
		$this->documentor_global_options = array( 'custom_post' => '1',
							  'custom_posts' => array('post','page'),
							  'custom_styles' => '',
							  'user_level' => 'publish_posts',
							);
		$this->_register_hooks();
		$this->include_files();
		$this->create_custom_post();
	}
	// Create Text Domain For Translations
	function _define_constants()
	{
		if ( ! defined( 'DOCUMENTOR_TABLE' ) ) define('DOCUMENTOR_TABLE','documentor'); //Documentor TABLE NAME
		if ( ! defined( 'DOCUMENTOR_SECTIONS' ) ) define('DOCUMENTOR_SECTIONS','documentor_sections'); //sections TABLE NAME
		if ( ! defined( 'DOCUMENTOR_FEEDBACK' ) ) define('DOCUMENTOR_FEEDBACK','documentor_feedback'); //feedback TABLE NAME
		if ( ! defined( 'DOCUMENTOR_VER' ) ) define("DOCUMENTOR_VER","1.4.6",false);//Current Version of Documentor
		if ( ! defined( 'DOCUMENTOR_PLUGIN_BASENAME' ) )
			define( 'DOCUMENTOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		if ( ! defined( 'DOCUMENTOR_CSS_DIR' ) )
			define( 'DOCUMENTOR_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/skins/' );
		if ( ! defined( 'DOCPRO_PATH' ) )
			define( 'DOCPRO_PATH', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
		if ( ! defined( 'DOCPRO_URLPATH' ) )
			define('DOCPRO_URLPATH', trailingslashit( WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) ) );
		if ( ! defined( 'DOCUMENTOR_STORE_URL' ) )		
			define( 'DOCUMENTOR_STORE_URL', 'http://documentor.in/' );
		if ( ! defined( 'DOCUMENTOR_ITEM_NAME' ) )	
			define( 'DOCUMENTOR_ITEM_NAME', 'Documentor' );
	}
	function _register_hooks()
	{
		add_action('plugins_loaded', array(&$this, 'documentor_update_db_check'));
		add_action('wp_footer', array(&$this, 'documentor_custom_styles') );
		load_plugin_textdomain('documentor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		add_action( 'admin_init', array(&$this, 'documentor_plugin_updater'), 0 );
		if (!shortcode_exists( 'documentor' ) ) add_shortcode('documentor', array(&$this,'shortcode'));
		if (!shortcode_exists( 'docembed' ) ) add_shortcode('docembed', array(&$this,'docembed_shortcode'));
	}
	function install_documentor() {
		//Deactivate Lite if Pro is activated 	
		if (defined('DOCUMENTORLITE_PLUGIN_BASENAME')){	
			$installed_ver_doclite = get_site_option( "documentorlite_db_version" );		
			if( is_plugin_active(DOCUMENTORLITE_PLUGIN_BASENAME) ) add_action('update_option_active_plugins', array(&$this, 'deactivate_documentor_lite'));
		}
		global $wpdb, $table_prefix;
		$documentor_db_version = DOCUMENTOR_VER;
		$installed_ver = get_option( "documentor_db_version" );
		if( $installed_ver != $documentor_db_version ) {
			$table_name = $table_prefix.DOCUMENTOR_TABLE;
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				
				//ver1.4
				$sql = "CREATE TABLE $table_name (
					doc_id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(5) NOT NULL,
					UNIQUE KEY doc_id(doc_id)
				);";				
				$rs = $wpdb->query($sql);
			}
			//Added to update settings if having lite version and updating to pro
			
			else { //if table already present				
				if(!empty($installed_ver_doclite) && $installed_ver_doclite > '0' && $installed_ver_doclite >= '1.3' ) { // To add default settings for documentor lite guide
					$results= $wpdb->get_results("SELECT * FROM $table_name" );
					if( count( $results ) > 0 ) {				  
						foreach($results as $guide){					        
						        $settings= get_post_meta($guide->post_id,'_doc_settings',true);			
					   		$curr_settings = json_decode( $settings, true );
							foreach($this->default_documentor_settings as $key=>$value) {
							   if(!isset($curr_settings[$key])) {
							      $curr_settings[$key] = $value;
							   }
							}
							$curr_settings = json_encode($curr_settings);					
							update_post_meta($guide->post_id,'_doc_settings',$curr_settings);
					        }
					 }
				}				
				if( (!empty($installed_ver) && $installed_ver > '0' && $installed_ver < '1.4') || ( !empty($installed_ver_doclite) && $installed_ver_doclite > '0' && $installed_ver_doclite < '1.3') ) {
					$default_settings = $this->default_documentor_settings;				
					//ver1.4 start
					if($wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE 'post_id'") != 'post_id'){
					  $sql = "ALTER TABLE $table_name
						ADD COLUMN post_id INT(5) NOT NULL";
					  $rs5 = $wpdb->query($sql);
					}				
					//Code for inserting custom post type deleted
				}			        			
			}// else ver1.4 end		
				
			$table_name = $table_prefix.DOCUMENTOR_SECTIONS;
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
							sec_id int(5) NOT NULL AUTO_INCREMENT,
							doc_id int(5) NOT NULL,
							post_id bigint(20) NOT NULL,
							type varchar(50) NOT NULL,
							upvote int(5) NOT NULL,
							downvote int(5) NOT NULL,
							slug varchar(200) NOT NULL,
							UNIQUE KEY sec_id(sec_id)
						);";
				$rs = $wpdb->query($sql);
			}
			//add column for pdf id v-1.0.1
			if($wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE 'pdf_id'") != 'pdf_id') {
				// Add Columns 
				$sql = "ALTER TABLE $table_name
				ADD COLUMN pdf_id INT(5) NOT NULL";
				$rs5 = $wpdb->query($sql);
			}
			//add column for slug v-1.0.2
			if($wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE 'slug'") != 'slug') {
				// Add Column
				$sql = "ALTER TABLE $table_name
				ADD COLUMN slug varchar(200) NOT NULL";
				$rs5 = $wpdb->query($sql);
				
				//update slug column of sections table with post_name column from posts table
				$posts_table = $table_prefix."posts";
				$sqlsel = "SELECT * FROM $table_name sec, $posts_table post WHERE sec.post_id = post.ID;";
				$results = $wpdb->get_results($sqlsel);
				if( $wpdb->num_rows > 0 ) {
					$sqlupdate = "UPDATE $table_name
						      SET slug = CASE post_id";
					foreach( $results as $result ) {
						$sqlupdate .= " WHEN $result->ID THEN '$result->post_name'";
					}
					$sqlupdate .= " END;";
					$rs5 = $wpdb->query($sqlupdate);
				}
			}
			$table_name = $table_prefix.DOCUMENTOR_FEEDBACK;
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
							id int(5) NOT NULL AUTO_INCREMENT,
							doc_id int(5) NOT NULL,							
							sec_id int(5) NOT NULL,
							ip varchar(100) NOT NULL,
							vote varchar(100) NOT NULL,
							date TIMESTAMP NOT NULL,
							UNIQUE KEY id(id)
						);";
				$rs = $wpdb->query($sql);
			}
			update_option( "documentor_db_version", $documentor_db_version );
			//global setting
			$global_settings = $this->documentor_global_options;
			$global_settings_curr = get_option('documentor_global_options');
			if( !$global_settings_curr ) {
				$global_settings_curr = array();
			}
			foreach($global_settings as $key=>$value) {
				if(!isset($global_settings_curr[$key])) {
					$global_settings_curr[$key] = $value;
				}
			}
			update_option('documentor_global_options',$global_settings_curr);
		}//end of if db version chnage
	}

	function shortcode( $atts ) {
		$doc_id = isset($atts[0])?$atts[0]:'';
		$id = intVal($doc_id);
		$guide = new DocumentorGuide( $id );
		$html = $guide->view();
		return $html;
	}
	
	function docembed_shortcode( $atts, $content ) {
		$doc_atts_arr=array(
			'element' => 'callout',
			'type' => 'message',
			'background' => '',
			'color' => '',
			'border_color' => '',
		);
		$doc_atts_arr=apply_filters('docembed_atts',$doc_atts_arr);
		extract(shortcode_atts($doc_atts_arr, $atts));
		$eatts = shortcode_atts($doc_atts_arr, $atts);
		$return = '';
		$style = 'style="';
		foreach( $eatts as $key => $value ) {
			if( $key != 'element' && $key != 'type'  ) {
				if( !empty( $value ) ) {
					$key = str_replace("_", "-", $key);
					$style .= $key.':'.$value.';';
				}
			}
		}
		$style .= '"';
		$return = '<div class="doc-callouts doc-'.$type.'" '.$style.'>'.$content.'</div>';
		return $return;
	}
	
	function include_files() { 
		require_once (dirname (__FILE__) . '/core/includes/fonts.php');
		require_once (dirname (__FILE__) . '/core/admin.php');
		require_once (dirname (__FILE__) . '/core/guide.php');
		require_once (dirname (__FILE__) . '/core/section.php');
		require_once (dirname (__FILE__) . '/core/ajax.php');
		require_once (dirname (__FILE__) . '/core/includes/tinymce/tinymce.php');
		// Load the auto update class
		if( !class_exists( 'DOCUMENTOR_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/core/includes/upgrade.php' );
		}
		require_once (dirname (__FILE__) . '/core/includes/license.php');
		require_once (dirname (__FILE__) . '/core/includes/compat.php');
		require_once (dirname (__FILE__) . '/core/includes/functions.php');
	}
	
	public static function documentor_plugin_url( $path = '' ) {
		return plugins_url( $path, __FILE__ );
	}

	public static function documentor_admin_url( $query = array() ) {
		global $plugin_page;

		if ( ! isset( $query['page'] ) )
			$query['page'] = $plugin_page;

		$path = 'admin.php';

		if ( $query = build_query( $query ) )
			$path .= '?' . $query;

		$url = admin_url( $path );

		return esc_url_raw( $url );
	}
	/* Added for auto update - start */
	function documentor_update_db_check() {
		$documentor_db_version = DOCUMENTOR_VER;
		if (get_site_option('documentor_db_version') != $documentor_db_version) {
			$this->install_documentor();
		}
	}
	function documentor_plugin_updater() {
		$license_key = trim( get_option( 'documentor_license_key' ) );

		$edd_updater = new DOCUMENTOR_Plugin_Updater( DOCUMENTOR_STORE_URL, __FILE__, array( 
				'version' 	=> DOCUMENTOR_VER, 				// current version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_name' => DOCUMENTOR_ITEM_NAME, 	// name of this plugin
				'author' 	=> 'WebFanzine Media'  // author of this plugin
			)
		);

	}
	//deactivate lite version if pro activated
	function deactivate_documentor_lite(){
		deactivate_plugins(DOCUMENTORLITE_PLUGIN_BASENAME);
	}
	//New Custom Post Type sections
	function create_custom_post() {
		//New Custom Post Type
		$global_settings_curr = get_option('documentor_global_options');
		if( isset( $global_settings_curr['custom_post'] ) && $global_settings_curr['custom_post'] == '1' && !post_type_exists('documentor-sections') ){
			add_action( 'init', array( &$this, 'section_post_type'), 11 );
			//add filter to ensure the text Sections, or Section, is displayed when user updates a Section 
			add_filter('post_updated_messages', array( &$this, 'section_updated_messages') );
		} //if custom_post is true //ver1.4 start
		if(!post_type_exists('guide')){		
		  add_action( 'init', array( &$this, 'guide_post_type'), 11 );
		}
	}	
	function section_post_type() {
		$labels = array(
		'name' => _x('Sections', 'post type general name'),
		'singular_name' => _x('Section', 'post type singular name'),
		'add_new' => _x('Add New', 'documentor'),
		'add_new_item' => __('Add New Documentor Section'),
		'edit_item' => __('Edit Documentor Section'),
		'new_item' => __('New Documentor Section'),
		'all_items' => __('All Documentor Sections'),
		'view_item' => __('View Documentor Section'),
		'search_items' => __('Search Documentor Sections'),
		'not_found' =>  __('No Documentor sections found'),
		'not_found_in_trash' => __('No Documentor section found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Sections'
		);
		$args = array(
		'labels' => $labels,
		'menu_position'       => 5,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'section','with_front' => false),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		//'menu_position' => null,
		'can_export' => true,
		'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
		); 
		register_post_type('documentor-sections',$args);
	} //ver1.4start
	function guide_post_type() {
		$labels = array(
		'name' => _x('Guides', 'post type general name'),
		'singular_name' => _x('Guide', 'post type singular name'),
		'add_new' => _x('Add New', 'documentor'),
		'add_new_item' => __('Add New Documentor Guide'),
		'edit_item' => __('Edit Documentor Guide'),
		'new_item' => __('New Documentor Guide'),
		'all_items' => __('All Documentor Guides'),
		'view_item' => __('View Documentor Guide'),
		'search_items' => __('Search Documentor Guides'),
		'not_found' =>  __('No Documentor guides found'),
		'not_found_in_trash' => __('No Documentor guides found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Guides'
		);
		$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => false, 
		'show_in_menu' => false, 
		'show_in_nav_menus' => false,
		'query_var' => true,
		'rewrite' => array('slug' => 'guide','with_front' => false),
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'can_export' => true,
		'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
		); 
		register_post_type('guide',$args); //ver1.4 end
	}
	function section_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages['document'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Documentor Section updated. <a href="%s">View Documentor section</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Documentor Section updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Documentor section restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Documentor Section published. <a href="%s">View Documentor section</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Section saved.'),
		8 => sprintf( __('Documentor Section submitted. <a target="_blank" href="%s">Preview Documentor Section</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Documentor Sections scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Documentor Section</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Documentor Section draft updated. <a target="_blank" href="%s">Preview Documentor Section</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);

		return $messages;
	}
	function documentor_custom_styles() {
		global $doc_customstyles;
		if( !isset( $doc_customstyles ) or $doc_customstyles < 1 ) {
			$global_curr = get_option('documentor_global_options');
			if( !empty( $global_curr['custom_styles'] ) ) {  ?>
				<style type="text/css"><?php echo $global_curr['custom_styles'];?></style>
			<?php }
			$doc_customstyles++;
		}
	}
	
}
if(!function_exists('get_documentor')){
	function get_documentor( $id=0 ) {
		$guide = new DocumentorGuide( $id );
		$html = $guide->view();
		echo $html;
	}
}
if( class_exists( 'Documentor' ) ) {
  $cn = new Documentor();
  // Register for activation
  register_activation_hook( __FILE__, array( &$cn, 'install_documentor') );
}
?>
