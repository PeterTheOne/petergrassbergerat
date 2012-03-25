function updateTitle() {
	title = $('input.title').val();
	$('#preview-title').html(title);
}

function updateContent() {	
	content = $('textarea.content').val();
	$('#preview-content').html(content);
}

$(function() {
	$('textarea').tabby();
	
	updateTitle();
	updateContent();
	$('input.title').keyup(updateTitle);
	$('textarea.content').keyup(updateContent);
});
