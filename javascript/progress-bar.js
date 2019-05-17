// function for generate a decent password length 10-15 with all characters
function randomPassword() {
    let string = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()+<>ABCDEFGHIJKLMNOP1234567890";
    let password = "";
    let length = Math.floor(Math.random() * (6) + 10);
    for (let ii = 0; ii < length; ii++) {
        let i = Math.floor(Math.random() * string.length);
        password += string.charAt(i);
    }
    return password;
}

// function for put the same password in input -> account/settings
function generate() {
    let random_password = randomPassword();
    form_ch_ps.first_password.value = random_password;
    form_ch_ps.second_password.value = random_password;
}

// function for show password
function showPassword() {
    let passOne = document.getElementById("passOne");
    let passTwo = document.getElementById("passTwo");


    if (passOne.type === "password") {
        passOne.type = "text";
        passTwo.type = "text";
    } else {
        passOne.type = "password";
        passTwo.type = "password";
    }
}

// progress bar for strength password

let password = document.getElementById("passOne");

function addListenerMulti(element, eventNames, listener) {
    let events = eventNames.split(' ');
    for (let i = 0, len = events.length; i < len; i++) {
        element.addEventListener(events[i], listener, false);
    }
}

addListenerMulti(document, 'keyup click', function () {

    let current_password = password.value;

    // Reset if password length is zero
    if (current_password.length === 0) {
        document.getElementById("progress-text").innerHTML = "";
        document.getElementById("progress-bar").value = "0";
        return;
    }

    let valid_check = [/[!@#$%^&*()+<>]/, /[A-Z]/, /[0-9]/, /[a-z]/]
        .reduce((memo, test) => memo + test.test(current_password), 0);

    if (valid_check > 2 && current_password.length > 7) {
        valid_check++;
    } else {
        valid_check = 0;
    }

    let progress = "";
    let strength = "";
    switch (valid_check) {
        case 0:
        case 1:
        case 2:
            strength = "25%";
            progress = "25";
            break;
        case 3:
            strength = "50%";
            progress = "50";
            break;
        case 4:
            strength = "75%";
            progress = "75";
            break;
        case 5:
            strength = "100%";
            progress = "100";
            break;
    }
    document.getElementById("progress-text").innerHTML = strength;
    document.getElementById("progress-bar").value = progress;

});
