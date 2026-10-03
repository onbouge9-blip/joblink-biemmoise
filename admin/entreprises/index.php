<?php
require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$r = $pdo->query("
    SELECT
        e.id,
        e.nom,
        e.email,
        e.telephone,
        e.adresse,
        s.libelle AS secteur,
        v.libelle AS ville
    FROM entreprise e
    JOIN secteur s ON e.id_secteur = s.id
    JOIN ville v ON e.id_ville = v.id
    ORDER BY e.nom
")->fetchAll();
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entreprises - Administration</title>
</head>

<body>

<h1>Entreprises</h1>

<p>
    <a href="create.php">➕ Ajouter une entreprise</a>
</p>

<table border="1" cellpadding="8">
    <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Adresse</th>
        <th>Secteur</th>
        <th>Ville</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($r as $x): ?>
        <tr>
            <td><?= htmlspecialchars($x['nom']) ?></td>

            <td><?= htmlspecialchars($x['email'] ?? '') ?></td>

            <td><?= htmlspecialchars($x['telephone'] ?? '') ?></td>

            <td><?= htmlspecialchars($x['adresse'] ?? '') ?></td>

            <td><?= htmlspecialchars($x['secteur']) ?></td>

            <td><?= htmlspecialchars($x['ville']) ?></td>

            <td>
                <a href="edit.php?id=<?= (int)$x['id'] ?>">
                    ✏️ Modifier
                </a>

                |

                <a
                    href="delete.php?id=<?= (int)$x['id'] ?>"
                    onclick="return confirm('Voulez-vous vraiment supprimer cette entreprise ?');"
                >
                    🗑️ Supprimer
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<p>
    <a href="../dashboard.php">← Retour au dashboard</a>
</p>

</body>
</html>