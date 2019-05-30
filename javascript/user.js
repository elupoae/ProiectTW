let titleInfo = document.getElementsByClassName("title-info");
let varInfo = document.getElementsByClassName("expand-info");
let secondInfo = document.getElementsByClassName("second-info");

let editButton = document.getElementsByClassName("button-edit");
let saveButton = document.getElementsByClassName("button-save");
let generateButton = document.getElementsByClassName("button-generate");
let editInputPass = document.getElementsByClassName("password-info");
let editInputUser = document.getElementsByClassName("username-info");

for (let i = 0; i < titleInfo.length; i++) {
    titleInfo[i].ondblclick = function () {
        if (varInfo[i].style.height === 'auto') {
            varInfo[i].style.height = '50px';
            secondInfo[i].style.display = 'none';
        } else {
            varInfo[i].style.height = 'auto';
            secondInfo[i].style.display = 'inline';
        }
    }
}

// double click selection text
document.addEventListener('mousedown', function (event) {
    if (event.detail > 1) {
        event.preventDefault();
    }
}, false);

function myFunction() {
    let inputPass = document.getElementsByClassName("password-info");
    let checkPass = document.getElementsByClassName("password-checkbox");

    for (let i = 0; i <= inputPass.length; i++) {
        if (inputPass[i].type === "password" && checkPass[i].checked) {
            inputPass[i].type = "text";
            break;
        } else if (inputPass[i].type === 'text' && !checkPass[i].checked) {
            inputPass[i].type = "password";
        }
    }
}

let checkBox = document.getElementsByClassName("password-checkbox");
for (let i = 0; i <= editButton.length; i++) {
    editButton[i].onclick = function () {
        if (editButton[i].style.display === "block") {
            editButton[i].style.display = "none";
            saveButton[i].style.display = "block";
            generateButton[i].style.display = "block";
            editInputPass[i].style.cssText = "pointer-events: auto; border-bottom: 1px solid #fff";
            editInputUser[i].style.cssText = "pointer-events: auto; border-bottom: 1px solid #fff";
            editInputPass[i].type = "text";
            checkBox[i].checked = true;
        }
    };
    saveButton[i].onclick = function () {
        if (saveButton[i].style.display === "block") {
            editButton[i].style.display = "block";
            saveButton[i].style.display = "none";
            generateButton[i].style.display = "none";
            editInputPass[i].style.pointerEvents = "none";
            editInputUser[i].style.pointerEvents = "none";
            editInputPass[i].type = "password";
            checkBox[i].checked = false;
        }
    };
}
