/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	config.uiColor = '#FDF5E6';
	config.autoGrow_maxHeight = 200; // set to 0 for to allow grow to full size
	config.toolbarCanCollapse = true;
	config.toolbar = [
		["Source"],
		["Cut", "Copy", "Paste", "PasteText", "PasteFromWord"],
		["Undo", "Redo", "-", "Find", "Replace", "-", "SelectAll"],
		["Styles"],
		["Link", "Unlink", "Anchor"],
		"/",
		["Bold", "Italic", "Underline", "-", "Subscript", "Superscript", "RemoveFormat"],
		["NumberedList", "BulletedList", "-", "Outdent", "Indent", "Blockquote", "CreateDiv"],
		["JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"],
		["Image", "Table", "HorizontalRule", "SpecialChar"],
		"/",
		["Format", "Font", "FontSize"],
		["TextColor", "BGColor"],
		["Maximize", "ShowBlocks"]
	];
	config.skin = "moono"; // moonocolor also available
	config.extraPlugins = 'wordcount';
	config.wordcount = {
        showWordCount: false,
	    showCharCount: true,
	    countHTML: true,
		charLimit: 65535,
	    wordLimit: 'unlimited'
	};
};
