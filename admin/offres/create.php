<?php require_once __DIR__.'/../../classes/Database.php';require_once __DIR__.'/../../classes/Session.php';require_once __DIR__.'/../../classes/AdminAuth.php';Session::start();AdminAuth::requireLogin();$p=(new Database())->getConnection();$stmtEntreprises = $p->prepare(
    'SELECT id,nom FROM entreprise ORDER BY nom'
);
$stmtEntreprises->execute();
$e = $stmtEntreprises->fetchAll();

$stmtSecteurs = $p->prepare(
    'SELECT id,libelle FROM secteur ORDER BY libelle'
);
$stmtSecteurs->execute();
$s = $stmtSecteurs->fetchAll();

$stmtVilles = $p->prepare(
    'SELECT id,libelle FROM ville ORDER BY libelle'
);
$stmtVilles->execute();
$v = $stmtVilles->fetchAll();

$stmtTypes = $p->prepare(
    'SELECT id,libelle FROM type_contrat ORDER BY libelle'
);
$stmtTypes->execute();
$t = $stmtTypes->fetchAll();if($_SERVER['REQUEST_METHOD']==='POST'){ if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
} $st=$_POST['statut'];$q=$p->prepare("INSERT INTO offre(titre,description,salaire,date_limite,statut,id_entreprise,id_secteur,id_ville,id_type_contrat,date_publication) VALUES(:t,:d,:s,:l,:st,:e,:sec,:v,:tc,:dp)");$q->execute(['t'=>trim($_POST['titre']),'d'=>trim($_POST['description']),'s'=>$_POST['salaire']!==''?$_POST['salaire']:null,'l'=>$_POST['date_limite'],'st'=>$st,'e'=>(int)$_POST['id_entreprise'],'sec'=>(int)$_POST['id_secteur'],'v'=>(int)$_POST['id_ville'],'tc'=>(int)$_POST['id_type_contrat'],'dp'=>$st==='publiee'?date('Y-m-d H:i:s'):null]);header('Location: index.php');exit;}?><!doctype html><html lang="fr"><body><h1>Ajouter offre</h1><form method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>"><input name="titre" required><textarea name="description" required></textarea><input name="salaire" type="number" step="0.01"><input name="date_limite" type="date" required><select name="statut"><option>brouillon</option><option>publiee</option><option>cloturee</option></select><select name="id_entreprise"><?php foreach($e as $x):?><option value="<?= (int)$x['id'] ?>"><?=htmlspecialchars($x['nom'])?></option><?php endforeach;?></select><select name="id_secteur"><?php foreach($s as $x):?><option value="<?=$x['id']?>"><?=htmlspecialchars($x['libelle'])?></option><?php endforeach;?></select><select name="id_ville"><?php foreach($v as $x):?><option value="<?=$x['id']?>"><?=htmlspecialchars($x['libelle'])?></option><?php endforeach;?></select><select name="id_type_contrat"><?php foreach($t as $x):?><option value="<?=$x['id']?>"><?=htmlspecialchars($x['libelle'])?></option><?php endforeach;?></select><button>Enregistrer</button></form></body></html>