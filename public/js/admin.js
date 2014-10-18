$(function() {

    hljs.initHighlightingOnLoad();

    $('#title').on('keyup change', function() {
        $('.editPreview h1 a').html($(this).val());
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

    $('#title_clean').on('keyup change', function() {
        var href = $('.editPreview h1 a').attr('href');
        href = href.split('/').slice(0, -2).join('/');
        href += '/' + $(this).val() + '/';
        $('.editPreview h1 a').attr('href', href);
    });

    $('#content').tabby({tabString: '    '});

    $('#content').on('keyup change', function() {
        $('.editPreview div').html($(this).val());
        videoEmbed();
        $('pre code').each(function(i, e) {
            hljs.highlightBlock(e)
        });
    });

    $('#name').on('keyup change', function() {
        $('.editPreview li a').html($(this).val());
    });

    if ($('#name_clean').val() === '') {
        $('#name').on('keyup change', function() {
            var title_clean = $(this).val();
            title_clean = title_clean.toLowerCase();
            title_clean = title_clean.replace(/ /g, '-');
            title_clean = title_clean.replace(/[^a-zA-Z0-9-\._~@$&\*\+=]+/g, '');
            $('#name_clean').val(title_clean);
        });
    }

    $('#name_clean').on('keyup change', function() {
        var href = $('.editPreview li a').attr('href');
        href = href.split('/').slice(0, -2).join('/');
        href += '/' + $(this).val() + '/';
        $('.editPreview li a').attr('href', href);
    });

    $('#color').on('keyup change', function() {
        $('.editPreview li a').css('background-color', $(this).val());
    });
});