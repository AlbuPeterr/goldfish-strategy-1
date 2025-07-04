document.getElementById('registerForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const data = Object.fromEntries(formData);

  const response = await fetch('../goldfish-strategy/api/register.php', {
    method: 'POST',
    body: JSON.stringify(data),
    headers: { 'Content-Type': 'application/json' }
  });

  const result = await response.json();
  if (result.success) {
    alert("Registration successful!");
    window.location.href = '../goldfish-strategy/login.html';
  } else {
    alert(result.message);
  }
});
