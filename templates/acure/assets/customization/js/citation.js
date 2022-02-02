function copyToClipboard() {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($('#citation').text().trim().replace(/(\s+)/g,' ').replace('Citation: ', '')).select();
    document.execCommand("copy");
    $temp.remove();
}