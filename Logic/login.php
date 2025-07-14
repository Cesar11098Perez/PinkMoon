<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Obtener y validar entrada JSON
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

// Datos de conexión MySQL
$servername = "localhost"; // o tu servidor
$username = "root";
$password_db = "11098";
$dbname = "SalonUnas";

// Crear conexión
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos',
        'error' => $conn->connect_error
    ]);
    exit;
}

// Preparar consulta segura
$stmt = $conn->prepare("SELECT ID, Nombre, Correo, Contrasena FROM Clientes WHERE Correo = ?");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la preparación de la consulta',
        'error' => $conn->error
    ]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Comparar contraseña (texto plano, ideal usar password_verify)
    if ($password === $user['Contrasena']) {
        $_SESSION['usuario_id'] = $user['ID'];
        $_SESSION['usuario_nombre'] = $user['Nombre'];
        $_SESSION['usuario_email'] = $user['Correo'];

        echo json_encode([
            'success' => true,
            'nombre' => $user['Nombre'],
            'id' => $user['ID']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales inválidas']);
}

$stmt->close();
$conn->close();
