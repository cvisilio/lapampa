<?php
declare(strict_types=1);

// No envíes salida antes de session_start() (la clase ya lo hace internamente).
require_once "../conexion.php";
require_once "../classes/cart.php";

$cart = new Cart();

// --- INPUT SEGURO ---
$id_localidad = filter_input(INPUT_POST, 'id_localidad1', FILTER_VALIDATE_INT);
$id_motivo    = filter_input(INPUT_POST, 'afectacion',   FILTER_VALIDATE_INT);

// Monto puede tener decimales (p. ej. "1234.56" o "1234,56")
$monto_raw = $_POST['monto'] ?? null;
if ($monto_raw !== null) {
    // Normalizamos coma decimal a punto
    $monto_norm = str_replace(',', '.', trim((string)$monto_raw));
    $monto = filter_var($monto_norm, FILTER_VALIDATE_FLOAT);
} else {
    $monto = false;
}

// qty/cuota: debe ser entero >=1. Si no viene, por defecto 1.
$cuota_raw = $_POST['cuota'] ?? $_POST['qty'] ?? $_POST['cantidad'] ?? '';
$cuota = filter_var($cuota_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($cuota === false) {
    $cuota = 1; // valor por defecto razonable
}

// id_compromiso puede venir vacío
$id_compromiso = (string)($_POST['id_compromiso'] ?? '');

// Validaciones mínimas
if ($id_localidad === false || $id_motivo === false || $monto === false) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'error' => 'Parámetros inválidos. Enviá id_localidad1, afectacion y monto válidos.'
    ]);
    exit;
}

// Construimos un ID compuesto estable (mismo que tu versión)
$id_localidad_id_Motivo = $id_localidad . '-' . $id_motivo;

// Armamos el item para insertar
$itemData = [
    'id'            => $id_localidad_id_Motivo,
    'id_localidad'  => $id_localidad,
    'id_compromiso' => $id_compromiso,
    'id_motivo'     => $id_motivo,
    'price'         => (float)$monto, // float, no int
    'qty'           => (int)$cuota    // entero >= 1
];

// Insertamos en el carrito
$insertItem = $cart->insert($itemData);

// Respuesta
header('Content-Type: application/json; charset=utf-8');
if ($insertItem === false) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'No se pudo insertar el item en el carrito (revisá qty/price/id).'
    ]);
} else {
    echo json_encode([
        'ok' => true,
        'rowid' => $insertItem,
        'total_items' => $cart->total_items(),
        'cart_total'  => $cart->total()
    ]);
}
