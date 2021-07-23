
function initCkeditor() {


	$('.ckeditor').each(function() {
		CKEDITOR.replace(this.id, {
            height: 120,
            width: 1000,
            toolbar: 'short',
            allowedContent: true,
        });
        CKEDITOR.instances[this.id].updateElement();
	})
	
}