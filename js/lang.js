document.addEventListener("DOMContentLoaded", () => {
  const savedLang = localStorage.getItem("lang") || "en";
  const langSelect = document.getElementById("langSwitcher");
  if (langSelect) {
      langSelect.value = savedLang;
      langSelect.addEventListener("change", (e) => {
        const selectedLang = e.target.value;
        localStorage.setItem("lang", selectedLang);
        location.reload();  // oldal újratöltése a kiválasztott nyelvvel
      });
    }
    loadLanguage(savedLang);  // ez a rész opcionális, ha minden betöltéskor újratöltöd az oldalt
  });


  // Fordítás betöltése
  async function loadLanguage(lang) {
    try {
      const response = await fetch(`lang/${lang}.json`);
      const data = await response.json();

      document.querySelectorAll("[data-translate]").forEach((el) => {
        const key = el.getAttribute("data-translate");
        if (data[key]) el.innerText = data[key];
      });

      document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
        const key = el.getAttribute("data-i18n-placeholder");
        if (data[key]) el.placeholder = data[key];
      });
    } catch (error) {
      console.error("Nyelvi fájl betöltési hiba:", error);
    }
  }