var modal = $('#videoModal');
var openBtn = $('#open-video-modal');
var closeBtn = $('#close-video-modal');
var iframe = $('#youtube-frame');

openBtn.on('click', () => {
    modal.css('display', 'block');
});

closeBtn.on('click', () => {
    modal.css('display', 'none');
    stopVideo();
});

window.onclick = function(event) {
    if (event.target == modal[0]) {
        modal.css('display', 'none');
        stopVideo();
    }
}

function stopVideo() {
    var src = iframe.attr('src');
    iframe.attr('src', src);
}