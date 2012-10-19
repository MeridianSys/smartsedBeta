/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.plugins.addExternal('fmath_formula', 'plugins/fmath_formula/', 'plugin.js');
CKEDITOR.editorConfig = function( config )
{
config.extraPlugins = 'fmath_formula';
        config.uiColor = 'green';


        config.filebrowserBrowseUrl = '/drsmarts/js/ckeditor/filemanager/index.html';
config.filebrowserImageBrowseUrl = '/drsmarts/js/ckeditor/filemanager/index.html?type=Images';
config.filebrowserFlashBrowseUrl = '/drsmarts/js/ckeditor/filemanager/index.html?type=Flash';
config.filebrowserUploadUrl = '/drsmarts/js/ckeditor/filemanager/connectors/php/filemanager.php';
config.filebrowserImageUploadUrl = '/drsmarts/js/ckeditor/filemanager/connectors/php/filemanager.php?command=QuickUpload&type;=Images';
config.filebrowserFlashUploadUrl = '/drsmarts/js/ckeditor/filemanager/connectors/php/filemanager.php?command=QuickUpload&type;=Flash';



	config.toolbar = 'MyToolbar';

	config.toolbar_MyToolbar =
	[
		{ name: 'document',items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
	{ name: 'clipboard',	items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	{ name: 'editing',	items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	{ name: 'forms',	items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	{ name: 'basicstyles',	items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
        { name: 'insert',	items : [ 'Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
        { name: 'links',	items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'tools',	items : [ 'Maximize', 'ShowBlocks' ] },
	'/',
	{ name: 'styles',	items : [ 'Styles','Format','Font','FontSize' ] },
	{ name: 'colors',	items : [ 'TextColor','BGColor' ] }
	
];
 // Made a admin ck layout //
 config.toolbar = 'StaticAdminToolbar';
        config.toolbar_StaticAdminToolbar =
	[
	{ name: 'basicstyles',	items : ['Source', 'Bold','Italic'] },
        { name: 'colors',	items : [ 'TextColor','BGColor' ] },
        { name: 'links',	items : [ 'Link','Unlink','Anchor' ] }
        ];
 // Made a mail ck layout //
 config.toolbar = 'MailToolbar';
        config.toolbar_MailToolbar =
	[
	{ name: 'basicstyles',	items : ['Underline', 'Bold','Italic' ] },
        { name: 'colors',	items : [ 'TextColor','BGColor' ] },
        { name: 'styles',	items : [ 'Font','FontSize' ] }
        ];
     // Made a Math ck layout //
        config.toolbar = 'MathToolbar';
               config.toolbar_MathToolbar =
       	[
       	{ name: 'basicstyles',	items : ['Underline', 'Bold','Italic','fmath_formula' ] },
               { name: 'colors',	items : [ 'TextColor','BGColor' ] },
               { name: 'styles',	items : [ 'Font','FontSize' ] }
               ];

};
