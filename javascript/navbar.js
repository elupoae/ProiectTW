const nav = () => {
    const navClose = document.querySelector('.nav-close');
    const nav = document.querySelector('.nav-elements');
    const navElements = document.querySelectorAll('.nav-elements li');
    // slide nav-bar
    navClose.addEventListener('click',()=>{
        nav.classList.toggle('nav-active');

        // animation elements nav-bar
        navElements.forEach((item, index) => {
            if(item.style.animation){
                item.style.animation = '';
            } else {
                item.style.animation = `navFade 0.5s ease forwards ${index / 8 + 0.3 }s`;
            }
        });
        // animation close nav-bar
        navClose.classList.toggle('nav-X');
    });
};

nav();
