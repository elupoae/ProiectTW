let passOne = document.getElementById("passOne");
let passTwo = document.getElementById("passTwo");

// function for generate a decent password length 15-20 with all characters
function randomPassword() {
    let string = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()+<>ABCDEFGHIJKLMNOP1234567890";
    let password = "";
    let length = Math.floor(Math.random() * (5) + 20);
    for (let ii = 0; ii < length; ii++) {
        let i = Math.floor(Math.random() * string.length);
        password += string.charAt(i);
    }
    return password;
}

// function for put the same password in input -> account/settings
function generate() {
    let random_password = randomPassword();
    form_ch_ps.passOne.value = random_password;
    form_ch_ps.passTwo.value = random_password;
}

// function for show password -> account/settings
function showPassword() {
    if (passOne.type === "password") {
        passOne.type = "text";
        passTwo.type = "text";
    } else {
        passOne.type = "password";
        passTwo.type = "password";
    }
}

function check() {
    if (passOne.value === passTwo.value) {
        passOne.style.cssText = "border: 1px solid #0F9D58;";
        passTwo.style.cssText = "border: 1px solid #0F9D58;";
    } else {
        passTwo.style.cssText = "border: 2px solid red;";
    }
}

function addListenerMulti(element, eventNames, listener) {
    let events = eventNames.split(' ');
    for (let i = 0, len = events.length; i < len; i++) {
        element.addEventListener(events[i], listener, false);
    }
}

addListenerMulti(document, 'keyup click', function () {

    let current_pass = passOne.value;
    if (current_pass.length === 0) {
        document.getElementById("progress-text").innerHTML = "";
        document.getElementById("progress-bar").value = "0";
        return;
    }

    let valid_check = [/[!@#$%^&*()+<>]/, /[A-Z]/, /[0-9]/, /[a-z]/, /\w{2,5}/g, /\D{2,10}/g]
        .reduce((memo, test) => memo + test.test(current_pass), 0);
    valid_check -= [/\d{10,20}/g, /\w{10,20}/g]
        .reduce((memo, test) => memo + test.test(current_pass), 0);

    valid_check *= 10;
    // alert(valid_check);
    if (valid_check >= 50 && current_pass.length > 14) {
        valid_check += 40;
    } else if (valid_check > 40 && current_pass.length > 10) {
        valid_check += 20;
    } else if (valid_check > 20 && current_pass.length > 7) {
        valid_check += 10
    } else if (valid_check < 20 && current_pass.length < 7) {
        valid_check = 5;
    }

    let progress = valid_check.toString();

    document.getElementById("progress-text").innerHTML = progress + "%";
    document.getElementById("progress-bar").value = progress;
});

