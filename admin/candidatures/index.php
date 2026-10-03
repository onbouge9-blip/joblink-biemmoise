<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

/*
|--------------------------------------------------------------------------
| Modification du statut d'une candidature
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
}

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $statut = $_POST['statut'] ?? '';

    $statutsAutorises = [
        'en_attente',
        'retenue',
        'refusee'
    ];

    if ($id > 0 && in_array($statut, $statutsAutorises, true)) {

        $q = $pdo->prepare("
            UPDATE candidature
            SET statut = :statut
            WHERE id = :id
        ");

        $q->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Récupération des candidatures
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.statut,
        c.date_candidature,
        c.lettre_motivation,
        ca.prenom,
        ca.nom,
        o.titre,
        e.nom AS entreprise
    FROM candidature c
    JOIN candidat ca ON c.id_candidat = ca.id
    JOIN offre o ON c.id_offre = o.id
    JOIN entreprise e ON o.id_entreprise = e.id
    ORDER BY c.date_candidature DESC
");

$stmt->execute();

$rows = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Candidatures</title>
</head>

<body>

<h1>Candidatures</h1>

<table border="1">

    <tr>
        <th>Candidat</th>
        <th>Offre</th>
        <th>Entreprise</th>
        <th>Date</th>
        <th>Statut</th>
        <th>Action</th>
    </tr>

    <?php foreach ($rows as $r): ?>

        <tr>

            <td>
                <?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['titre']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['entreprise']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['date_candidature']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['statut']) ?>
            </td>

            <td>

                <form method="post">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $r['id'] ?>"
                    >

                    <select name="statut">

                        <?php foreach (['en_attente', 'retenue', 'refusee'] as $s): ?>

                            <option
                                value="<?= htmlspecialchars($s) ?>"
                                <?= $r['statut'] === $s ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($s) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <button type="submit">
                        Modifier
                    </button>

                </form>

                <br>

                <a href="view.php?id=<?= (int) $r['id'] ?>">
                    👁️ Voir
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

<br>

<a href="../dashboard.php">
    ← Retour au dashboard
</a>

</body>

</html>