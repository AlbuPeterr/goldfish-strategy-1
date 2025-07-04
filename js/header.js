document.addEventListener('DOMContentLoaded', async () => {
  const loginLink = document.getElementById('loginLink');
  const registerLink = document.getElementById('registerLink');
  const currentLang = localStorage.getItem('lang') || 'en';

  let translations = {};

  // Fordítások betöltése
  try {
    const langResponse = await fetch(`lang/${currentLang}.json`);
    translations = await langResponse.json();
  } catch (err) {
    console.error("Could not load translations:", err);
  }

  // Session ellenőrzése
  try {
    const response = await fetch('api/session.php');
    const result = await response.json();

    if (result.loggedIn) {
      loginLink.textContent = result.username;
      loginLink.href = '#';

      registerLink.textContent = translations["logout"] || "Logout";
      registerLink.href = 'api/logout.php';
    }
  } catch (error) {
    console.error('Session check failed:', error);
  }
});
