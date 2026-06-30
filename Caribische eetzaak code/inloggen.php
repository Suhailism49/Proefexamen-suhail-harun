<?php
session_start();
header('Content-Type: application/json');
require 'database.php';
require 'src/LoginService.php';

$data = json_decode(file_get_contents('php://input'), true);
$service = new LoginService(new PdoLoginRepository($pdo));
$result = $service->authenticate($data ?? [], $_SESSION);

echo json_encode($result);
exit;
?>
