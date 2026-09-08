<?php

require_once 'db.php';

$section = $_GET['section'] ?? 'employes';
$action = $_POST['action'] ?? '';


// =====================================================
// TRAITEMENT DES ACTIONS
// =====================================================

try {

    // -------------------------------------------------
    // DEPARTEMENT : AJOUT
    // -------------------------------------------------

    if ($action === 'add_departement') {

        $stmt = $pdo->prepare("
            INSERT INTO departements (deptEmpl, mgrDept)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $_POST['deptEmpl'],
            $_POST['mgrDept'] !== '' ? $_POST['mgrDept'] : null
        ]);

        header("Location: index.php?section=departements");
        exit;
    }


    // -------------------------------------------------
    // DEPARTEMENT : MODIFICATION
    // -------------------------------------------------

    if ($action === 'update_departement') {

        $stmt = $pdo->prepare("
            UPDATE departements
            SET deptEmpl = ?, mgrDept = ?
            WHERE deptEmpl = ?
        ");

        $stmt->execute([
            $_POST['newDeptEmpl'],
            $_POST['mgrDept'] !== '' ? $_POST['mgrDept'] : null,
            $_POST['oldDeptEmpl']
        ]);

        header("Location: index.php?section=departements");
        exit;
    }


    // -------------------------------------------------
    // DEPARTEMENT : SUPPRESSION
    // -------------------------------------------------

    if ($action === 'delete_departement') {

        $stmt = $pdo->prepare("
            DELETE FROM departements
            WHERE deptEmpl = ?
        ");

        $stmt->execute([$_POST['deptEmpl']]);

        header("Location: index.php?section=departements");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE : AJOUT
    // -------------------------------------------------

    if ($action === 'add_employe') {

        $stmt = $pdo->prepare("
            INSERT INTO employes (nomEmpl, salaire, deptEmpl)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $_POST['nomEmpl'],
            $_POST['salaire'],
            $_POST['deptEmpl'] !== '' ? $_POST['deptEmpl'] : null
        ]);

        header("Location: index.php?section=employes");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE : MODIFICATION
    // -------------------------------------------------

    if ($action === 'update_employe') {

        $stmt = $pdo->prepare("
            UPDATE employes
            SET nomEmpl = ?, salaire = ?, deptEmpl = ?
            WHERE idEmpl = ?
        ");

        $stmt->execute([
            $_POST['nomEmpl'],
            $_POST['salaire'],
            $_POST['deptEmpl'] !== '' ? $_POST['deptEmpl'] : null,
            $_POST['idEmpl']
        ]);

        header("Location: index.php?section=employes");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE : SUPPRESSION
    // -------------------------------------------------

    if ($action === 'delete_employe') {

        $stmt = $pdo->prepare("
            DELETE FROM employes
            WHERE idEmpl = ?
        ");

        $stmt->execute([$_POST['idEmpl']]);

        header("Location: index.php?section=employes");
        exit;
    }


    // -------------------------------------------------
    // PROJET : AJOUT
    // -------------------------------------------------

    if ($action === 'add_projet') {

        $stmt = $pdo->prepare("
            INSERT INTO projets
                (nomProj, budget, mgrProj, dateDeut)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['nomProj'],
            $_POST['budget'],
            $_POST['mgrProj'] !== '' ? $_POST['mgrProj'] : null,
            $_POST['dateDeut'] !== '' ? $_POST['dateDeut'] : null
        ]);

        header("Location: index.php?section=projets");
        exit;
    }


    // -------------------------------------------------
    // PROJET : MODIFICATION
    // -------------------------------------------------

    if ($action === 'update_projet') {

        $stmt = $pdo->prepare("
            UPDATE projets
            SET nomProj = ?, budget = ?, mgrProj = ?, dateDeut = ?
            WHERE nomProj = ?
        ");

        $stmt->execute([
            $_POST['newNomProj'],
            $_POST['budget'],
            $_POST['mgrProj'] !== '' ? $_POST['mgrProj'] : null,
            $_POST['dateDeut'] !== '' ? $_POST['dateDeut'] : null,
            $_POST['oldNomProj']
        ]);

        header("Location: index.php?section=projets");
        exit;
    }


    // -------------------------------------------------
    // PROJET : SUPPRESSION
    // -------------------------------------------------

    if ($action === 'delete_projet') {

        $stmt = $pdo->prepare("
            DELETE FROM projets
            WHERE nomProj = ?
        ");

        $stmt->execute([$_POST['nomProj']]);

        header("Location: index.php?section=projets");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE-PROJET : AJOUT
    // -------------------------------------------------

    if ($action === 'add_affectation') {

        $stmt = $pdo->prepare("
            INSERT INTO employe_projet
                (idEmpl, nomProj, heures, evalEmpl)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['idEmpl'],
            $_POST['nomProj'],
            $_POST['heures'],
            $_POST['evalEmpl']
        ]);

        header("Location: index.php?section=affectations");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE-PROJET : MODIFICATION
    // -------------------------------------------------

    if ($action === 'update_affectation') {

        $stmt = $pdo->prepare("
            UPDATE employe_projet
            SET heures = ?, evalEmpl = ?
            WHERE idEmpl = ?
              AND nomProj = ?
        ");

        $stmt->execute([
            $_POST['heures'],
            $_POST['evalEmpl'],
            $_POST['idEmpl'],
            $_POST['nomProj']
        ]);

        header("Location: index.php?section=affectations");
        exit;
    }


    // -------------------------------------------------
    // EMPLOYE-PROJET : SUPPRESSION
    // -------------------------------------------------

    if ($action === 'delete_affectation') {

        $stmt = $pdo->prepare("
            DELETE FROM employe_projet
            WHERE idEmpl = ?
              AND nomProj = ?
        ");

        $stmt->execute([
            $_POST['idEmpl'],
            $_POST['nomProj']
        ]);

        header("Location: index.php?section=affectations");
        exit;
    }


} catch (PDOException $e) {

    $error = $e->getMessage();
}


// =====================================================
// RECUPERATION DES DONNEES
// =====================================================

$departements = $pdo
    ->query("SELECT * FROM departements ORDER BY deptEmpl")
    ->fetchAll();

$employes = $pdo
    ->query("
        SELECT e.*, d.deptEmpl AS departement
        FROM employes e
        LEFT JOIN departements d
            ON e.deptEmpl = d.deptEmpl
        ORDER BY e.idEmpl
    ")
    ->fetchAll();

$projets = $pdo
    ->query("
        SELECT p.*, e.nomEmpl AS manager
        FROM projets p
        LEFT JOIN employes e
            ON p.mgrProj = e.idEmpl
        ORDER BY p.nomProj
    ")
    ->fetchAll();

$affectations = $pdo
    ->query("
        SELECT ep.*, e.nomEmpl, p.nomProj
        FROM employe_projet ep
        JOIN employes e
            ON ep.idEmpl = e.idEmpl
        JOIN projets p
            ON ep.nomProj = p.nomProj
        ORDER BY p.nomProj, e.nomEmpl
    ")
    ->fetchAll();


// =====================================================
// ELEMENT A MODIFIER
// =====================================================

$edit = $_GET['edit'] ?? null;
$id = $_GET['id'] ?? null;

$editEmploye = null;
$editDepartement = null;
$editProjet = null;
$editAffectation = null;

if ($edit === 'employe' && $id !== null) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM employes
        WHERE idEmpl = ?
    ");

    $stmt->execute([$id]);
    $editEmploye = $stmt->fetch();
}

if ($edit === 'departement' && $id !== null) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM departements
        WHERE deptEmpl = ?
    ");

    $stmt->execute([$id]);
    $editDepartement = $stmt->fetch();
}

if ($edit === 'projet' && $id !== null) {

    $stmt = $pdo->prepare("
        SELECT *
        FROM projets
        WHERE nomProj = ?
    ");

    $stmt->execute([$id]);
    $editProjet = $stmt->fetch();
}

if ($edit === 'affectation' && $id !== null) {

    $parts = explode('|', $id);

    if (count($parts) === 2) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM employe_projet
            WHERE idEmpl = ?
              AND nomProj = ?
        ");

        $stmt->execute([$parts[0], $parts[1]]);
        $editAffectation = $stmt->fetch();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion Entreprise</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Gestion Entreprise</h1>

    <nav>
        <a href="index.php?section=departements">Départements</a>
        <a href="index.php?section=employes">Employés</a>
        <a href="index.php?section=projets">Projets</a>
        <a href="index.php?section=affectations">Employés / Projets</a>
    </nav>
</header>


<main>

<?php if (isset($error)): ?>

    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<!-- =================================================
     DEPARTEMENTS
================================================== -->

<?php if ($section === 'departements'): ?>

<h2>Départements</h2>

<?php if ($editDepartement): ?>

<h3>Modifier le département</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="update_departement">

    <input type="hidden"
           name="oldDeptEmpl"
           value="<?= $editDepartement['deptEmpl'] ?>">

    <label>Numéro :</label>

    <input type="number"
           name="newDeptEmpl"
           value="<?= $editDepartement['deptEmpl'] ?>"
           required>

    <label>Manager :</label>

    <select name="mgrDept">

        <option value="">-- Aucun --</option>

        <?php foreach ($employes as $e): ?>

            <option value="<?= $e['idEmpl'] ?>"
                <?= $e['idEmpl'] == $editDepartement['mgrDept']
                    ? 'selected'
                    : '' ?>>

                <?= htmlspecialchars($e['nomEmpl']) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <button>Modifier</button>

    <a href="index.php?section=departements">Annuler</a>

</form>

<?php else: ?>

<h3>Ajouter un département</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="add_departement">

    <label>Numéro :</label>

    <input type="number"
           name="deptEmpl"
           required>

    <label>Manager :</label>

    <select name="mgrDept">

        <option value="">-- Aucun --</option>

        <?php foreach ($employes as $e): ?>

            <option value="<?= $e['idEmpl'] ?>">
                <?= htmlspecialchars($e['nomEmpl']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button>Ajouter</button>

</form>

<?php endif; ?>


<table>

<tr>
    <th>ID</th>
    <th>Manager</th>
    <th>Actions</th>
</tr>

<?php foreach ($departements as $d): ?>

<tr>

    <td><?= $d['deptEmpl'] ?></td>

    <td>

        <?php

        $manager = '';

        foreach ($employes as $e) {
            if ($e['idEmpl'] == $d['mgrDept']) {
                $manager = $e['nomEmpl'];
            }
        }

        echo htmlspecialchars($manager ?: '-');

        ?>

    </td>

    <td>

        <a href="index.php?section=departements&edit=departement&id=<?= $d['deptEmpl'] ?>">
            Modifier
        </a>

        <form method="POST" class="inline">

            <input type="hidden"
                   name="action"
                   value="delete_departement">

            <input type="hidden"
                   name="deptEmpl"
                   value="<?= $d['deptEmpl'] ?>">

            <button class="delete">Supprimer</button>

        </form>

    </td>

</tr>

<?php endforeach; ?>

</table>


<!-- =================================================
     EMPLOYES
================================================== -->

<?php elseif ($section === 'employes'): ?>

<h2>Employés</h2>

<?php if ($editEmploye): ?>

<h3>Modifier l'employé</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="update_employe">

    <input type="hidden"
           name="idEmpl"
           value="<?= $editEmploye['idEmpl'] ?>">

    <label>Nom :</label>

    <input type="text"
           name="nomEmpl"
           value="<?= htmlspecialchars($editEmploye['nomEmpl']) ?>"
           required>

    <label>Salaire :</label>

    <input type="number"
           step="0.01"
           name="salaire"
           value="<?= $editEmploye['salaire'] ?>"
           required>

    <label>Département :</label>

    <select name="deptEmpl">

        <option value="">-- Aucun --</option>

        <?php foreach ($departements as $d): ?>

            <option value="<?= $d['deptEmpl'] ?>"
                <?= $d['deptEmpl'] == $editEmploye['deptEmpl']
                    ? 'selected'
                    : '' ?>>

                Département <?= $d['deptEmpl'] ?>

            </option>

        <?php endforeach; ?>

    </select>

    <button>Modifier</button>

    <a href="index.php?section=employes">Annuler</a>

</form>

<?php else: ?>

<h3>Ajouter un employé</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="add_employe">

    <label>Nom :</label>

    <input type="text"
           name="nomEmpl"
           required>

    <label>Salaire :</label>

    <input type="number"
           step="0.01"
           name="salaire"
           required>

    <label>Département :</label>

    <select name="deptEmpl">

        <option value="">-- Aucun --</option>

        <?php foreach ($departements as $d): ?>

            <option value="<?= $d['deptEmpl'] ?>">
                Département <?= $d['deptEmpl'] ?>
            </option>

        <?php endforeach; ?>

    </select>

    <button>Ajouter</button>

</form>

<?php endif; ?>


<table>

<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Salaire</th>
    <th>Département</th>
    <th>Actions</th>
</tr>

<?php foreach ($employes as $e): ?>

<tr>

    <td><?= $e['idEmpl'] ?></td>

    <td><?= htmlspecialchars($e['nomEmpl']) ?></td>

    <td><?= $e['salaire'] ?> €</td>

    <td><?= $e['departement'] ?? '-' ?></td>

    <td>

        <a href="index.php?section=employes&edit=employe&id=<?= $e['idEmpl'] ?>">
            Modifier
        </a>

        <form method="POST" class="inline">

            <input type="hidden"
                   name="action"
                   value="delete_employe">

            <input type="hidden"
                   name="idEmpl"
                   value="<?= $e['idEmpl'] ?>">

            <button class="delete">Supprimer</button>

        </form>

    </td>

</tr>

<?php endforeach; ?>

</table>


<!-- =================================================
     PROJETS
================================================== -->

<?php elseif ($section === 'projets'): ?>

<h2>Projets</h2>

<?php if ($editProjet): ?>

<h3>Modifier le projet</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="update_projet">

    <input type="hidden"
           name="oldNomProj"
           value="<?= htmlspecialchars($editProjet['nomProj']) ?>">

    <label>Nom :</label>

    <input type="text"
           name="newNomProj"
           value="<?= htmlspecialchars($editProjet['nomProj']) ?>"
           required>

    <label>Budget :</label>

    <input type="number"
           step="0.01"
           name="budget"
           value="<?= $editProjet['budget'] ?>"
           required>

    <label>Manager :</label>

    <select name="mgrProj">

        <option value="">-- Aucun --</option>

        <?php foreach ($employes as $e): ?>

            <option value="<?= $e['idEmpl'] ?>"
                <?= $e['idEmpl'] == $editProjet['mgrProj']
                    ? 'selected'
                    : '' ?>>

                <?= htmlspecialchars($e['nomEmpl']) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <label>Date :</label>

    <input type="date"
           name="dateDeut"
           value="<?= $editProjet['dateDeut'] ?>">

    <button>Modifier</button>

    <a href="index.php?section=projets">Annuler</a>

</form>

<?php else: ?>

<h3>Ajouter un projet</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="add_projet">

    <label>Nom :</label>

    <input type="text"
           name="nomProj"
           required>

    <label>Budget :</label>

    <input type="number"
           step="0.01"
           name="budget"
           required>

    <label>Manager :</label>

    <select name="mgrProj">

        <option value="">-- Aucun --</option>

        <?php foreach ($employes as $e): ?>

            <option value="<?= $e['idEmpl'] ?>">
                <?= htmlspecialchars($e['nomEmpl']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <label>Date :</label>

    <input type="date"
           name="dateDeut">

    <button>Ajouter</button>

</form>

<?php endif; ?>


<table>

<tr>
    <th>Nom</th>
    <th>Budget</th>
    <th>Manager</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php foreach ($projets as $p): ?>

<tr>

    <td><?= htmlspecialchars($p['nomProj']) ?></td>

    <td><?= $p['budget'] ?> €</td>

    <td><?= htmlspecialchars($p['manager'] ?? '-') ?></td>

    <td><?= $p['dateDeut'] ?? '-' ?></td>

    <td>

        <a href="index.php?section=projets&edit=projet&id=<?= urlencode($p['nomProj']) ?>">
            Modifier
        </a>

        <form method="POST" class="inline">

            <input type="hidden"
                   name="action"
                   value="delete_projet">

            <input type="hidden"
                   name="nomProj"
                   value="<?= htmlspecialchars($p['nomProj']) ?>">

            <button class="delete">Supprimer</button>

        </form>

    </td>

</tr>

<?php endforeach; ?>

</table>


<!-- =================================================
     AFFECTATIONS
================================================== -->

<?php elseif ($section === 'affectations'): ?>

<h2>Employés / Projets</h2>

<?php if ($editAffectation): ?>

<h3>Modifier l'affectation</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="update_affectation">

    <input type="hidden"
           name="idEmpl"
           value="<?= $editAffectation['idEmpl'] ?>">

    <input type="hidden"
           name="nomProj"
           value="<?= htmlspecialchars($editAffectation['nomProj']) ?>">

    <p>
        Employé :
        <strong><?= $editAffectation['idEmpl'] ?></strong>
    </p>

    <p>
        Projet :
        <strong><?= htmlspecialchars($editAffectation['nomProj']) ?></strong>
    </p>

    <label>Heures :</label>

    <input type="number"
           name="heures"
           value="<?= $editAffectation['heures'] ?>"
           required>

    <label>Évaluation :</label>

    <input type="number"
           step="0.01"
           name="evalEmpl"
           value="<?= $editAffectation['evalEmpl'] ?>"
           required>

    <button>Modifier</button>

    <a href="index.php?section=affectations">Annuler</a>

</form>

<?php else: ?>

<h3>Affecter un employé à un projet</h3>

<form method="POST">

    <input type="hidden"
           name="action"
           value="add_affectation">

    <label>Employé :</label>

    <select name="idEmpl" required>

        <option value="">-- Choisir --</option>

        <?php foreach ($employes as $e): ?>

            <option value="<?= $e['idEmpl'] ?>">

                <?= htmlspecialchars($e['nomEmpl']) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <label>Projet :</label>

    <select name="nomProj" required>

        <option value="">-- Choisir --</option>

        <?php foreach ($projets as $p): ?>

            <option value="<?= htmlspecialchars($p['nomProj']) ?>">

                <?= htmlspecialchars($p['nomProj']) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <label>Heures :</label>

    <input type="number"
           name="heures"
           required>

    <label>Évaluation :</label>

    <input type="number"
           step="0.01"
           name="evalEmpl"
           required>

    <button>Ajouter</button>

</form>

<?php endif; ?>


<table>

<tr>
    <th>Employé</th>
    <th>Projet</th>
    <th>Heures</th>
    <th>Évaluation</th>
    <th>Actions</th>
</tr>

<?php foreach ($affectations as $a): ?>

<tr>

    <td><?= htmlspecialchars($a['nomEmpl']) ?></td>

    <td><?= htmlspecialchars($a['nomProj']) ?></td>

    <td><?= $a['heures'] ?></td>

    <td><?= $a['evalEmpl'] ?></td>

    <td>

        <a href="index.php?section=affectations&edit=affectation&id=<?= $a['idEmpl'] ?>|<?= urlencode($a['nomProj']) ?>">
            Modifier
        </a>

        <form method="POST" class="inline">

            <input type="hidden"
                   name="action"
                   value="delete_affectation">

            <input type="hidden"
                   name="idEmpl"
                   value="<?= $a['idEmpl'] ?>">

            <input type="hidden"
                   name="nomProj"
                   value="<?= htmlspecialchars($a['nomProj']) ?>">

            <button class="delete">Supprimer</button>

        </form>

    </td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</main>

</body>
</html>