<?php

require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$r = $pdo->query("
    SELECT
        o.id,
        o.titre,
        o.statut,
        o.date_limite,
        e.nom AS entreprise
    FROM offre o
    JOIN entreprise e ON o.id_entreprise = e.id
    ORDER BY o.id DESC
")->fetchAll();

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Offres - Administration</title>
</head>

<body>

<h1>Offres</h1>

<p>
    <a href="create.php">➕ Ajouter une offre</a>
</p>

<table border="1" cellpadding="8">

    <tr>
        <th>Titre</th>
        <th>Entreprise</th>
        <th>Statut</th>
        <th>Date limite</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($r as $x): ?>

        <tr>

            <td>
                <?= htmlspecialchars($x['titre']) ?>
            </td>

            <td>
                <?= htmlspecialchars($x['entreprise']) ?>
            </td>

            <td>
                <?= htmlspecialchars($x['statut']) ?>
            </td>

            <td>
                <?= htmlspecialchars($x['date_limite']) ?>
            </td>

            <td>

                <a href="edit.php?id=<?= (int)$x['id'] ?>">
                    ✏️ Modifier
                </a>

                |

                <a
                    href="delete.php?id=<?= (int)$x['id'] ?>"
                    onclick="return confirm('Voulez-vous vraiment supprimer cette offre ?');"
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