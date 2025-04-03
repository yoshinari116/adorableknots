document.addEventListener("DOMContentLoaded", () => {
    const slider = document.querySelector(".slider");
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");

    let translateX = 0; // Track current position
    const cardWidth = 300; // Adjust based on your `.category-card` size + gap
    const maxTranslateX = -(slider.scrollWidth - slider.clientWidth);

    nextBtn.addEventListener("click", () => {
        if (translateX > maxTranslateX) {
            translateX -= cardWidth;
            slider.style.transform = `translateX(${translateX}px)`;
        }
    });

    prevBtn.addEventListener("click", () => {
        if (translateX < 0) {
            translateX += cardWidth;
            slider.style.transform = `translateX(${translateX}px)`;
        }
    });
});
