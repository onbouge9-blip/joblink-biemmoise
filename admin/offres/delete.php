<?php

require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('Offre invalide.');
}

/*
 * Vérifier si des candidatures existent
 * pour cette offre.
 */
$q = $pdo->prepare("
    SELECT COUNT(*)
    FROM candidature
    WHERE id_offre = :id
");

$q->execute(['id' => $id]);

$nombreCandidatures = (int)$q->fetchColumn();

if ($nombreCandidatures > 0) {

    exit(
        'Impossible de supprimer cette offre car elle possède '
        . $nombreCandidatures
        . ' candidature(s).'
    );
}

/*
 * Suppression de l'offre.
 */
$q = $pdo->prepare("
    DELETE FROM offre
    WHERE id = :id
");

$q->execute(['id' => $id]);

header('Location: index.php');
exit;