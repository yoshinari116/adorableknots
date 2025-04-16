const buttons = document.querySelectorAll(".nav-links button");

buttons.forEach(button => {
    const img = button.querySelector("img");
    const src = img.getAttribute("src");
    const activeSrc = src.replace(".png", "-active.png");

    // If this is the active page, set active image
    if (button.classList.contains("active")) {
        img.src = activeSrc;
    }

    // Hover effect
    button.addEventListener("mouseenter", () => {
        img.src = activeSrc;
    });
    button.addEventListener("mouseleave", () => {
        if (!button.classList.contains("active")) {
            img.src = src;
        }
    });
});