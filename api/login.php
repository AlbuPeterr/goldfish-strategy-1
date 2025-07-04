<?php
session_start();

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$conn = new mysqli('localhost', 'root', '', 'goldfish-strategy');
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'message' => 'Connection failed']);
  exit;
}

$name = $conn->real_escape_string($input['name']);
$password = $input['password'];

// Admin külön logika
if ($name === 'admin' && $password === 'admin') {
  $_SESSION['username'] = 'admin';
  echo json_encode([
    'success' => true,
    'redirect' => '../goldfish-strategy/admin.php'
  ]);
  exit;
}

// Egyéb felhasználók
$sql = "SELECT * FROM users WHERE name = '$name'";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
  if (password_verify($password, $row['password'])) {
    $_SESSION['username'] = $row['name'];
    echo json_encode([
      'success' => true,
      'redirect' => '../goldfish-strategy/index.html'
    ]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'User not found']);
}

$conn->close();
?>
