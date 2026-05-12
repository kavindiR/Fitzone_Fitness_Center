const sliders = document.querySelectorAll('.slider');

sliders.forEach(slider => {
    let slides = slider.querySelectorAll('.slide');
    let index = 0;

    setInterval(() => {
        slides[index].classList.remove('active');
        index = (index + 1) % slides.length;
        slides[index].classList.add('active');
    }, 3000); // 3 seconds
});
