window.onscroll = function() {myFunction()};

function myFunction() {
    if (document.body.scrollTop > 700 || document.documentElement.scrollTop > 700) {
        document.getElementById("nav-scroll").className = "nav-bar-scrolled";
    } else {
        document.getElementById("nav-scroll").className = "nav-bar";
    }
}