<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Recibir datos
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos."]);
    exit;
}

$fecha = $data['fecha'] ?? null;
$hora = $data['hora'] ?? null;
$estilista = $data['estilista'] ?? null;
$categoria = $data['categoria'] ?? null;
$servicio = $data['servicio'] ?? null;
$id_usuario = $data['id_usuario'] ?? null;

if (!$fecha || !$hora || !$estilista || !$categoria || !$servicio || !$id_usuario) {
    echo json_encode(["success" => false, "message" => "Faltan campos obligatorios."]);
    exit;
}

// Conexión a MySQL
$host = "localhost";
$usuario = "root";
$contrasena_db = "11098"; // tu contraseña de MySQL si aplica
$baseDeDatos = "SalonUnas";

$conn = new mysqli($host, $usuario, $contrasena_db, $baseDeDatos);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Conexión fallida", "error" => $conn->connect_error]);
    exit;
}

// Insertar en la tabla
$sql = "INSERT INTO Reservas (id_usuario, fecha, hora, estilista, categoria_servicio, servicio_especifico) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssss", $id_usuario, $fecha, $hora, $estilista, $categoria, $servicio);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Reserva guardada correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar la reserva", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
