window.addEventListener("load", () => {
    const container = document.querySelector(".slideshow-container");
    const slides = container.querySelectorAll(".mySlides");
    let slideIndex = 0;

    function showSlide(n) {
        slides.forEach(slide => slide.style.display = "none");
        slides[n].style.display = "block";
    }

    function nextSlide() {
        slideIndex = (slideIndex + 1) % slides.length;
        showSlide(slideIndex);
    }

    function prevSlide() {
        slideIndex = (slideIndex - 1 + slides.length) % slides.length;
        showSlide(slideIndex);
    }

    // Affiche la première slide
    if (slides.length > 0) showSlide(slideIndex);

    // Événements
    container.querySelector(".prev").addEventListener("click", prevSlide);
    container.querySelector(".next").addEventListener("click", nextSlide);
});
