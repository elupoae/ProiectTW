let objProText = document.getElementsByClassName("progress-text-obj");
let objProBar = document.getElementsByClassName("progress-bar-obj");
let objInputPass = document.getElementsByClassName("password-info");

function addListenerMulti(element, eventNames, listener) {
    let events = eventNames.split(' ');
    for (let i = 0, len = events.length; i < len; i++) {
        element.addEventListener(events[i], listener, false);
    }
}

addListenerMulti(document, 'keyup click', function () {
    for (let ii = 0; ii <= objInputPass.length; ii++) {
        if (objInputPass[ii].value.length === 0) {
            objProText[ii].innerHTML = "";
            objProBar[ii].value = "0";
            return;
        }

        let valid_check = [/[!@#$%^&*()+<>]/, /[A-Z]/, /[0-9]/, /[a-z]/, /\w{2,5}/g, /\D{2,10}/g]
            .reduce((memo, test) => memo + test.test(objInputPass[ii].value), 0);
        valid_check -= [/\d{10,20}/g, /\w{10,20}/g]
            .reduce((memo, test) => memo + test.test(objInputPass[ii].value), 0);

        valid_check *= 10;

        if (valid_check >= 50 && objInputPass[ii].value.length > 14) {
            valid_check += 40;
        } else if (valid_check > 40 && objInputPass[ii].value.length > 10) {
            valid_check += 20;
        } else if (valid_check > 20 && objInputPass[ii].value.length > 7) {
            valid_check += 10
        } else if (valid_check < 20 && objInputPass[ii].value.length < 7) {
            valid_check = 5;
        }

        let progress = valid_check.toString();

        objProText[ii].innerHTML = progress + "%";
        objProBar[ii].value = progress;
    }
});

// function for generate a decent password length 15-20 with all characters
function randomPasswordUsr() {
    let string = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()+<>ABCDEFGHIJKLMNOP1234567890";
    let password = "";
    let length = Math.floor(Math.random() * (5) + 20);
    for (let ii = 0; ii < length; ii++) {
        let i = Math.floor(Math.random() * string.length);
        password += string.charAt(i);
    }
    return password;
}

objGeneratePass = document.getElementsByClassName('button-generate');

for (let ii = 0; ii <= objInputPass.length; ii++) {
    objGeneratePass[ii].onclick = function() {
        objInputPass[ii].value = randomPasswordUsr();
    }
}
