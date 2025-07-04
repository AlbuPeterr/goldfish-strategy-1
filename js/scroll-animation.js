// js/scroll-animation.js

document.addEventListener("DOMContentLoaded", () => {
  const sections = document.querySelectorAll(".section");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          observer.unobserve(entry.target); // Csak egyszer animáljuk
        }
      });
    },
    {
      threshold: 0.2, // 20% látszódjon a szekcióból
    }
  );

  sections.forEach((section) => {
    observer.observe(section);
  });
});
