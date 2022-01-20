<?php // Hook for adding admin menus
if( !class_exists( 'DocumentorAjax' ) ) {
	class DocumentorAjax {
		function __construct() {
			$this->include_functions();
		}
		function include_functions(){
			if( class_exists( 'DocumentorSection' ) and is_admin() ) {
				add_action('wp_ajax_doc_create_section', array('DocumentorSection','create'));
				add_action('wp_ajax_documentor_show', array('DocumentorSection','show'));
				add_action('wp_ajax_doc_section_add_linkform', array('DocumentorSection','section_add_linkform'));
				add_action('wp_ajax_doc_update_section', array('DocumentorSection','update'));
				add_action('wp_ajax_doc_negative_feedback', array('DocumentorSection','negative_feedback'));
				add_action('wp_ajax_nopriv_doc_negative_feedback', array('DocumentorSection','negative_feedback'));
				add_action('wp_ajax_doc_positive_feedback', array('DocumentorSection','positive_feedback'));
				add_action('wp_ajax_nopriv_doc_positive_feedback', array('DocumentorSection','positive_feedback'));
				add_action('wp_ajax_doc_suggest_edit', array('DocumentorSection','suggest_edit'));
				add_action('wp_ajax_nopriv_doc_suggest_edit', array('DocumentorSection','suggest_edit'));
				add_action('wp_ajax_doc_suggest_editsecdata', array('DocumentorSection','suggest_editsecdata'));
				add_action('wp_ajax_nopriv_doc_suggest_editsecdata', array('DocumentorSection','suggest_editsecdata'));
				add_action('wp_ajax_doc_get_ajaxcontent', array('DocumentorSection','get_ajaxcontent'));
				add_action('wp_ajax_nopriv_doc_get_ajaxcontent', array('DocumentorSection','get_ajaxcontent'));
				add_action('wp_ajax_doc_get_feedback_form', array('DocumentorSection','get_feedback_form'));
				add_action('wp_ajax_nopriv_doc_get_feedback_form', array('DocumentorSection','get_feedback_form'));
				add_action('wp_ajax_doc_section_pdf', array('DocumentorSection','save_pdf'));
				add_action('wp_ajax_doc_reset_section_feedbackcnt', array('DocumentorSection','reset_feedbackcnt'));
				add_action('wp_ajax_nopriv_doc_reset_section_feedbackcnt', array('DocumentorSection','reset_feedbackcnt'));
			}
			if( class_exists( 'DocumentorGuide' ) and is_admin() ) {
				add_action('wp_ajax_doc_show_posts', array('DocumentorGuide','doc_show_posts'));
				add_action('wp_ajax_doc_save_pdf', array('DocumentorGuide','save_pdf')); 
				//add_action('wp_ajax_nopriv_doc_save_pdf', array('DocumentorGuide','save_pdf')); // frontEnd generate pdf
				add_action('wp_ajax_doc_show_search_results', array('DocumentorGuide','show_search_results'));
				add_action('wp_ajax_doc_save_sections', array('DocumentorGuide','save_sections'));
				add_action('wp_ajax_doc_load_preview', array('DocumentorGuide','load_preview'));
				add_action('wp_ajax_doc_search_results', array('DocumentorGuide','get_search_results'));
				add_action('wp_ajax_nopriv_doc_search_results', array('DocumentorGuide','get_search_results'));
				add_action('wp_ajax_doc_reset_feedbackcnt', array('DocumentorGuide','reset_feedback_count'));
				add_action('wp_ajax_nopriv_doc_reset_feedbackcnt', array('DocumentorGuide','reset_feedback_count'));
				add_action('wp_ajax_doc_suggest_guides_toattach', array('DocumentorGuide','doc_suggest_guides_toattach'));
				add_action('wp_ajax_nopriv_doc_suggest_guides_toattach', array('DocumentorGuide','doc_suggest_guides_toattach'));
				add_action('init', 'add_ob_start');
				add_action('wp_footer','flush_ob_end');
			}
			if( class_exists( 'DocumentorFonts' ) and is_admin() ) {
				add_action('wp_ajax_documentor_disp_gfweight',array('DocumentorFonts','google_font_weight'));
				add_action('wp_ajax_documentor_load_fontsdiv',array('DocumentorFonts','load_fontsdiv_callback'));
				
			}
			
		}
		
	}
}//end-if
if( class_exists( 'DocumentorAjax' ) ) {
	new DocumentorAjax();
}
//added for wp_redirect
if( !function_exists( 'callback' ) ) {
	function callback($buffer){
		return $buffer;
	}
}
if( !function_exists( 'add_ob_start' ) ) {
	function add_ob_start(){
		ob_start("callback");
	}
}
if( !function_exists( 'flush_ob_end' ) ) {
	function flush_ob_end(){
		ob_end_flush();
	}
}
?>
