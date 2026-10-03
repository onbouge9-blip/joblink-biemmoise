<?php require_once __DIR__.'/../classes/Database.php';require_once __DIR__.'/../classes/Session.php';require_once __DIR__.'/../classes/AdminAuth.php';Session::start();AdminAuth::requireLogin();$pdo=(new Database())->getConnection();$stmtEntreprises = $pdo->prepare(
    'SELECT COUNT(*) FROM entreprise'
);
$stmtEntreprises->execute();

$stmtOffres = $pdo->prepare(
    'SELECT COUNT(*) FROM offre'
);
$stmtOffres->execute();

$stmtCandidats = $pdo->prepare(
    'SELECT COUNT(*) FROM candidat'
);
$stmtCandidats->execute();

$stmtCandidatures = $pdo->prepare(
    'SELECT COUNT(*) FROM candidature'
);
$stmtCandidatures->execute();

$stats = [
    'entreprises' => $stmtEntreprises->fetchColumn(),
    'offres' => $stmtOffres->fetchColumn(),
    'candidats' => $stmtCandidats->fetchColumn(),
    'candidatures' => $stmtCandidatures->fetchColumn()
];;?><!doctype html><html lang="fr"><head><meta charset="UTF-8"><title>Dashboard admin</title></head><body><h1>Dashboard administrateur</h1><p>Bonjour <?=htmlspecialchars(Session::get('admin_nom'))?></p><ul><li>Entreprises: <?= (int)$stats['entreprises'] ?></li>
<li>Offres: <?= (int)$stats['offres'] ?></li>
<li>Candidats: <?= (int)$stats['candidats'] ?></li>
<li>Candidatures: <?= (int)$stats['candidatures'] ?></li></ul><a href="entreprises/index.php">Entreprises</a> | <a href="offres/index.php">Offres</a> | <a href="candidats/index.php">Candidats</a> | <a href="candidatures/index.php">Candidatures</a> | <a href="logout.php">Déconnexion</a></body></html>