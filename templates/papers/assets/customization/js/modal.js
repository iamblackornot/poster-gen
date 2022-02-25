var modal = $('#videoModal');
var openBtn = $('#open-video-modal');
var closeBtn = $('#close-video-modal');

openBtn.on('click', () => {
    modal.css('display', 'block');
});

closeBtn.on('click', () => {
    modal.css('display', 'none');
});

window.onclick = function(event) {
    
    if (event.target == modal[0]) {
        modal.css('display', 'none');
    }
}