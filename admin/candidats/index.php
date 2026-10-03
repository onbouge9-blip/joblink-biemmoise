<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.nom,
        c.prenom,
        c.email,
        c.telephone,
        c.cv_fichier,
        c.statut,
        v.libelle AS ville
    FROM candidat c
    LEFT JOIN ville v ON c.id_ville = v.id
    ORDER BY c.id DESC
");

$stmt->execute();

$rows = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Candidats</title>
</head>

<body>

<h1>Candidats</h1>

<table border="1">

    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Ville</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($rows as $r): ?>

        <tr>

            <td><?= htmlspecialchars($r['nom']) ?></td>

            <td><?= htmlspecialchars($r['prenom']) ?></td>

            <td><?= htmlspecialchars($r['email']) ?></td>

            <td><?= htmlspecialchars($r['telephone']) ?></td>

            <td><?= htmlspecialchars($r['ville'] ?? '') ?></td>

            <td><?= htmlspecialchars($r['statut']) ?></td>

            <td>

                <a href="view.php?id=<?= (int)$r['id'] ?>">
                    👁️ Voir
                </a>

                <?php if (!empty($r['cv_fichier'])): ?>

                    |
                    <a href="../../uploads/cv/<?= htmlspecialchars($r['cv_fichier']) ?>" target="_blank">
                        📄 CV
                    </a>

                <?php endif; ?>

                |

                <?php if ($r['statut'] === 'actif'): ?>

                    <a
                        href="toggle.php?id=<?= (int)$r['id'] ?>&action=desactiver"
                        onclick="return confirm('Voulez-vous vraiment désactiver ce candidat ?');"
                    >
                        🔒 Désactiver
                    </a>

                <?php else: ?>

                    <a
                        href="toggle.php?id=<?= (int)$r['id'] ?>&action=activer"
                        onclick="return confirm('Voulez-vous vraiment réactiver ce candidat ?');"
                    >
                        🔓 Activer
                    </a>

                <?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="../dashboard.php">Retour</a>

</body>
</html>