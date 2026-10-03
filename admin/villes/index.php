<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$stmt = $pdo->prepare("
    SELECT id, libelle
    FROM ville
    ORDER BY libelle ASC
");

$stmt->execute();

$villes = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Villes - JobLink Bénin</title>
</head>

<body>

<h1>Gestion des villes</h1>

<p>
    <a href="create.php">➕ Ajouter une ville</a>
</p>

<table border="1" cellpadding="8">

    <thead>
        <tr>
            <th>ID</th>
            <th>Libellé</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($villes as $ville): ?>

        <tr>

            <td>
                <?= (int) $ville['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($ville['libelle']) ?>
            </td>

            <td>

                <a href="edit.php?id=<?= (int) $ville['id'] ?>">
                    ✏️ Modifier
                </a>

                |

                <a
                    href="delete.php?id=<?= (int) $ville['id'] ?>"
                    onclick="return confirm('Voulez-vous vraiment supprimer cette ville ?');"
                >
                    🗑️ Supprimer
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<br>

<a href="../dashboard.php">
    ← Retour au dashboard
</a>

</body>

</html>