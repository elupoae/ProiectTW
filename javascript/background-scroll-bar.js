window.onscroll = function() {myFunction()};

function myFunction() {
    if (document.body.scrollTop > 260 || document.documentElement.scrollTop > 260) {
        document.getElementById("nav-scroll").className = "main-nav-scrolled";
    } else {
        document.getElementById("nav-scroll").className = "main-nav";
    }
}