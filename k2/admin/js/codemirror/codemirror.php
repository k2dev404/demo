<link rel="stylesheet" href="/k2/admin/codemirror/codemirror.css">
<script type="text/javascript" src="/k2/admin/codemirror/codemirror.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/matchbrackets.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/htmlmixed.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/xml.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/javascript.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/css.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/clike.js"></script>
<script type="text/javascript" src="/k2/admin/codemirror/php.js"></script>
<script>
	jQuery(document).ready(function(){
		jQuery('textarea').each(function(){
			var elm = jQuery(this);
			if(!elm.hasClass('tinymce')){
				CodeMirror.fromTextArea(elm[0], {
					lineNumbers: true,
					matchBrackets: true,
					mode: "application/x-httpd-php",
					indentUnit: 4,
					indentWithTabs: true
				});
			}
		});
	});
</script>