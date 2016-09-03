$(function () {
	$('textarea.tinymce').tinymce({
		script_url: '/k2/admin/tinymce/tinymce.min.js',

		plugins: [
			"filemanager advlist autolink lists link image print preview",
			"searchreplace visualblocks code fullscreen",
			"media table contextmenu paste"
		],
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link filemanager",

		language: 'ru',
		content_css: '/k2/tinymce.css',
		relative_urls: false,
		remove_script_host: true,
		accessibility_warnings: false,
		theme_advanced_resizing: true,
		theme_advanced_resize_horizontal: false
	});
});