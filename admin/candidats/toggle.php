<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($id <= 0) {
    exit('Candidat invalide.');
}

if ($action === 'desactiver') {

    $stmt = $pdo->prepare("
        UPDATE candidat
        SET statut = 'desactive'
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);

} elseif ($action === 'activer') {

    $stmt = $pdo->prepare("
        UPDATE candidat
        SET statut = 'actif'
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);

} else {

    exit('Action invalide.');

}

header('Location: index.php');
exit;