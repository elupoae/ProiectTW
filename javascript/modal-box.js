let modalLogin = document.getElementById('login');
let modalAddAccount = document.getElementById('add-account');
let modalChangePass = document.getElementById('reset-pass');

let closeLogin = document.getElementById('forgot-pass');

window.onclick = function (event) {
    if (event.target === modalLogin) {
        modalLogin.style.display = "none";
    } else if (event.target === modalAddAccount) {
        modalAddAccount.style.display = 'none';
    } else if (event.target === modalChangePass) {
        modalChangePass.style.display = 'none'
    }
};

closeLogin.onclick = function () {
    modalChangePass.style.display = 'block';
    modalLogin.style.display = 'none';
};
