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

    if ($('#title_clean').val() === '') {
        $('#title').on('keyup change', function() {
            var title_clean = $(this).val();
            title_clean = title_clean.toLowerCase();
            title_clean = title_clean.replace(/ /g, '-');
            title_clean = title_clean.replace(/[^a-zA-Z0-9-\._~@$&\*\+=]+/g, '');
            $('#title_clean').val(title_clean);
        });
    }
});