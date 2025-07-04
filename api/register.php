<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

// Kapcsolódás az adatbázishoz
$conn = new mysqli('localhost', 'root', '', 'goldfish-strategy');
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'message' => 'Database connection failed']);
  exit;
}

$name = $conn->real_escape_string($input['name']);
$email = $conn->real_escape_string($input['email']);
$phone = $conn->real_escape_string($input['phone']);
$password = password_hash($input['password'], PASSWORD_BCRYPT);

// Ellenőrizzük, hogy már regisztrálták-e a nevet vagy e-mailt
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR name = ?");
$check->bind_param("ss", $email, $name);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo json_encode(['success' => false, 'message' => 'Name or email already registered']);
  $check->close();
  $conn->close();
  exit;
}
$check->close();

// Beszúrás
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $password);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

$stmt->close();
$conn->close();
?>
