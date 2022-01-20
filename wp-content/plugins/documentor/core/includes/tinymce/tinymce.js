function documentorInit() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertDocumentorShortcode(args) {
	var defaults={
		createdDoc	:0,
		shortCode	:''
	}
	options=jQuery.extend({},defaults,args);
	var tagtext = '';
	if(options.createdDoc == '1') {
		tagtext = options.shortCode;
	} 
	if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		//execInstanceCommand is undefined from tinymce version 4
		if (typeof window.tinyMCE.execInstanceCommand != 'undefined') {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
        }
		else {
			if (typeof window.tinyMCE.execCommand != 'undefined') {
				window.tinyMCE.get('content').execCommand('mceInsertContent', false, tagtext);
			}
        }
		//window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}

function insertDocElementShortcode(args) {
	var defaults={
		shortCode	:''
	}
	options=jQuery.extend({},defaults,args);
	var tagtext = '';
	var end_tag_atts='';
	var shortcodeContents = 'The text goes here....';
	jQuery.each(jQuery("#docqtform").serializeArray(),function(index,field){
		if( field.name == 'callout_contents' && field.value != '' ) {
			shortcodeContents = field.value;
		}
		if( field.name != 'callout_contents' ) {
			end_tag_atts=end_tag_atts + ' ' + field.name + '="' + field.value + '"';
		} 
	})
	tagtext = tagtext + "[docembed" + end_tag_atts + "]"+shortcodeContents+"[/docembed]";
	if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		//execInstanceCommand is undefined from tinymce version 4
		if (typeof window.tinyMCE.execInstanceCommand != 'undefined') {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
        }
		else {
			if (typeof window.tinyMCE.execCommand != 'undefined') {
				window.tinyMCE.get('content').execCommand('mceInsertContent', false, tagtext);
			}
        }
		//window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
