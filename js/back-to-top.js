const backToTop = document.querySelector('a.back-to-top');

window.addEventListener('scroll', () => {
    if (window.scrollY > 200) { // si scroll > 200px
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});
