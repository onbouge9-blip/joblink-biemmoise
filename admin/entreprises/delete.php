<?php

require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('Entreprise invalide.');
}

/*
 * Vérifier si l'entreprise possède des offres.
 */
$q = $pdo->prepare("
    SELECT COUNT(*)
    FROM offre
    WHERE id_entreprise = :id
");

$q->execute(['id' => $id]);

$nombreOffres = (int)$q->fetchColumn();

if ($nombreOffres > 0) {

    exit(
        'Impossible de supprimer cette entreprise car elle possède '
        . $nombreOffres
        . ' offre(s). Supprimez ou modifiez d’abord ses offres.'
    );
}

/*
 * Suppression de l'entreprise
 */
$q = $pdo->prepare("
    DELETE FROM entreprise
    WHERE id = :id
");

$q->execute(['id' => $id]);

header('Location: index.php');
exit;