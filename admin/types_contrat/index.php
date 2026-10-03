<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$stmt = $pdo->prepare("
    SELECT id, libelle
    FROM type_contrat
    ORDER BY libelle ASC
");

$stmt->execute();

$typesContrat = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Types de contrat - JobLink Bénin</title>
</head>

<body>

<h1>Gestion des types de contrat</h1>

<p>
    <a href="create.php">➕ Ajouter un type de contrat</a>
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

    <?php foreach ($typesContrat as $type): ?>

        <tr>

            <td>
                <?= (int) $type['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($type['libelle']) ?>
            </td>

            <td>

                <a href="edit.php?id=<?= (int) $type['id'] ?>">
                    ✏️ Modifier
                </a>

                |

                <a
                    href="delete.php?id=<?= (int) $type['id'] ?>"
                    onclick="return confirm('Voulez-vous vraiment supprimer ce type de contrat ?');"
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