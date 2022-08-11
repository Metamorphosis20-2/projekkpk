CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles' ] },
		{ name: 'paragraph', groups: [ 'list', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		'/',
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];
	config.removePlugins = 'elementspath,resize';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_BR;
	config.autoParagraph = false;
	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Copy,Paste,Cut,Undo,Redo,Replace,Find,SelectAll,Scayt,Form,RemoveFormat,Outdent,Indent,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Unlink,Anchor,Link,BidiLtr,BidiRtl,Language,Blockquote,CreateDiv,Image,Flash,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Maximize,ShowBlocks,About,PasteText,PasteFromWord,Font,FontSize,HorizontalRule,Subscript,Superscript,Strike';
};