var videoEmbed = function() {
    $('div.videoEmbed').off('click').on('click', function() {
        $(this).find('img').hide();
        $(this).html(
            '<iframe src="' + $(this).data('video-url') + '?title=0&byline=0&portrait=0&autoplay=1" width="100%" height="100%" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>'
        );
    });
};