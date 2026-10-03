<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Vérifier si le type de contrat est utilisé par une offre
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM offre
    WHERE id_type_contrat = :id
");

$stmt->execute([
    'id' => $id
]);

$offres = (int) $stmt->fetchColumn();

if ($offres > 0) {

    echo '<p style="color:red;">';
    echo 'Impossible de supprimer ce type de contrat car il est utilisé par une ou plusieurs offres.';
    echo '</p>';

    echo '<p>';
    echo '<a href="index.php">← Retour aux types de contrat</a>';
    echo '</p>';

    exit;
}

// Supprimer le type de contrat
$stmt = $pdo->prepare("
    DELETE FROM type_contrat
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

header('Location: index.php');
exit;