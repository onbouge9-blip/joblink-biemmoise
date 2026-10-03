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

// Vérifier si le secteur est utilisé par une entreprise
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM entreprise
    WHERE id_secteur = :id
");

$stmt->execute([
    'id' => $id
]);

$entreprises = (int) $stmt->fetchColumn();

if ($entreprises > 0) {
    echo '<p style="color:red;">';
    echo 'Impossible de supprimer ce secteur car il est utilisé par une ou plusieurs entreprises.';
    echo '</p>';
    echo '<p><a href="index.php">← Retour aux secteurs</a></p>';
    exit;
}

// Vérifier si le secteur est utilisé par une offre
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM offre
    WHERE id_secteur = :id
");

$stmt->execute([
    'id' => $id
]);

$offres = (int) $stmt->fetchColumn();

if ($offres > 0) {
    echo '<p style="color:red;">';
    echo 'Impossible de supprimer ce secteur car il est utilisé par une ou plusieurs offres.';
    echo '</p>';
    echo '<p><a href="index.php">← Retour aux secteurs</a></p>';
    exit;
}

// Supprimer le secteur
$stmt = $pdo->prepare("
    DELETE FROM secteur
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

header('Location: index.php');
exit;