$(function() {

    $('#title').on('keyup change', function() {
        $('.editPreview h1 a').html($(this).val());
    });

    $('#title_clean').on('keyup change', function() {
        var href = $('.editPreview h1 a').attr('href');
        href = href.split('/').slice(0, -2).join('/');
        href += '/' + $(this).val() + '/';
        $('.editPreview h1 a').attr('href', href);
    });

    $('#content').on('keyup change', function() {
        $('.editPreview div').html($(this).val());
    });
});