CKEDITOR.editorConfig = function( config ) {
	config.enterMode = CKEDITOR.ENTER_BR // pressing the ENTER KEY input <br/>
	config.shiftEnterMode = CKEDITOR.ENTER_P; //pressing the SHIFT + ENTER KEYS input <p>
	config.autoParagraph = false;	
	config.toolbarGroups = [
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		'/',
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];
	config.extraPlugins = 'liststyle';
	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Copy,Paste,Cut,Undo,Redo,Replace,Find,SelectAll,Scayt,Form,RemoveFormat,Outdent,Indent,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Unlink,Anchor,Link,BidiLtr,BidiRtl,Language,Blockquote,CreateDiv,Image,Flash,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Maximize,ShowBlocks,About,PasteText,PasteFromWord,Font,FontSize,HorizontalRule,Subscript,Superscript,Strike';
};