<?php require_once __DIR__.'/../classes/Database.php'; $pdo=(new Database())->getConnection(); $stmtOffres = $pdo->prepare("
    SELECT
        o.id,
        o.titre,
        e.nom AS entreprise,
        v.libelle AS ville
    FROM offre o
    JOIN entreprise e ON o.id_entreprise = e.id
    JOIN ville v ON o.id_ville = v.id
    WHERE o.statut = 'publiee'
    ORDER BY o.date_publication DESC
");

$stmtOffres->execute();

$offres = $stmtOffres->fetchAll();?><!doctype html><html lang="fr"><head><meta charset="UTF-8"><title>JobLink Bénin</title></head><body><h1>JobLink Bénin</h1><p><a href="../candidat/offres.php">Voir les offres</a> | <a href="../candidat/login.php">Espace candidat</a> | <a href="../admin/login.php">Administration</a></p><?php foreach($offres as $o): ?><article><h2><?=htmlspecialchars($o['titre'])?></h2><p><?=htmlspecialchars($o['entreprise'])?> — <?=htmlspecialchars($o['ville'])?></p></article><?php endforeach;?></body></html>