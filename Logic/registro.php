<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Validación básica
if (
    !isset($data['nombre'], $data['apellidos'], $data['edad'], 
            $data['correo'], $data['telefono'], $data['contrasena'])
) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

// Asignar a variables
$nombre = $data['nombre'];
$apellidos = $data['apellidos'];
$edad = (int)$data['edad'];
$correo = $data['correo'];
$telefono = $data['telefono'];
$contrasena = $data['contrasena']; // Se recomienda hacer hash con password_hash()

// Conexión a MySQL
$host = "localhost";
$usuario = "root"; // tu usuario MySQL
$contrasena_db = "11098"; // tu contraseña de MySQL
$baseDeDatos = "SalonUnas"; // nombre de tu base de datos

$conn = new mysqli($host, $usuario, $contrasena_db, $baseDeDatos);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión', 'error' => $conn->connect_error]);
    exit;
}

// Verificar si ya existe el correo
$checkSql = "SELECT * FROM Clientes WHERE Correo = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $correo);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

// Insertar nuevo usuario
$insertSql = "INSERT INTO Clientes (Nombre, Apellidos, Edad, Correo, Telefono, Contrasena) 
              VALUES (?, ?, ?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("ssisss", $nombre, $apellidos, $edad, $correo, $telefono, $contrasena);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar usuario']);
}

$insertStmt->close();
$conn->close();
?>
