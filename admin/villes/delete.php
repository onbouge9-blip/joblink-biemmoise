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

// Vérifier si la ville est utilisée par un candidat
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM candidat
    WHERE id_ville = :id
");

$stmt->execute([
    'id' => $id
]);

$candidats = (int) $stmt->fetchColumn();

if ($candidats > 0) {
    echo '<p style="color:red;">';
    echo 'Impossible de supprimer cette ville car elle est utilisée par un ou plusieurs candidats.';
    echo '</p>';
    echo '<p><a href="index.php">← Retour aux villes</a></p>';
    exit;
}

// Vérifier si la ville est utilisée par une entreprise
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM entreprise
    WHERE id_ville = :id
");

$stmt->execute([
    'id' => $id
]);

$entreprises = (int) $stmt->fetchColumn();

if ($entreprises > 0) {
    echo '<p style="color:red;">';
    echo 'Impossible de supprimer cette ville car elle est utilisée par une ou plusieurs entreprises.';
    echo '</p>';
    echo '<p><a href="index.php">← Retour aux villes</a></p>';
    exit;
}

// Vérifier si la ville est utilisée par une offre
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM offre
    WHERE id_ville = :id
");

$stmt->execute([
    'id' => $id
]);

$offres = (int) $stmt->fetchColumn();

if ($offres > 0) {
    echo '<p style="color:red;">';
    echo 'Impossible de supprimer cette ville car elle est utilisée par une ou plusieurs offres.';
    echo '</p>';
    echo '<p><a href="index.php">← Retour aux villes</a></p>';
    exit;
}

// Supprimer la ville
$stmt = $pdo->prepare("
    DELETE FROM ville
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

header('Location: index.php');
exit;