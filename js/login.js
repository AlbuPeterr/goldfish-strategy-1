document.getElementById('loginForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  const response = await fetch('api/login.php', {
    method: 'POST',
    body: JSON.stringify(data),
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include' // <--- FONTOS: küldi a session cookie-t!
  });

  const result = await response.json();

  if (result.success) {
    window.location.href = result.redirect;
  } else {
    alert(result.message);
  }
});
