
// copy to clipboard button
let clipBoard = document.getElementsByClassName('far');
for (let i = 0; i <= varInfo.length; i++) {
    clipBoard[i].onclick = function () {
        editInputPass[i].select();
        document.execCommand("copy");
        alert("Copied the text: " + copyText.value);
    };
}
