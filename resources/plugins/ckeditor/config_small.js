CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		'/',
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,clipboard,colors,paragraph,NewPage,Preview,Print,Templates,Copy,Paste,Cut,Undo,Redo,Replace,Find,SelectAll,Scayt,Form,RemoveFormat,Outdent,Indent,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Unlink,Anchor,Link,BidiLtr,BidiRtl,Language,Blockquote,CreateDiv,Image,Flash,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Maximize,ShowBlocks,About,PasteText,PasteFromWord,Font,FontSize,HorizontalRule,Subscript,Superscript,Strike';
};