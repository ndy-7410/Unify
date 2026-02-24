<?php
function getCountFromPossibleNames($db, array $names)
{
    if ($db === null)
        return 0;

    foreach ($names as $table) {
        try {
            // PDO
            if ($db instanceof PDO) {
                $sql = "SELECT COUNT(*) FROM `$table`";
                $stmt = $db->query($sql);
                if ($stmt !== false) {
                    $val = $stmt->fetchColumn();
                    if ($val !== false)
                        return (int) $val;
                }
            }

            if ($db instanceof mysqli) {
                $res = $db->query("SELECT COUNT(*) as c FROM `$table`");
                if ($res) {
                    $row = $res->fetch_assoc();
                    if (isset($row['c']))
                        return (int) $row['c'];
                }
            }
        } catch (Exception $e) {
            // ignore et essaie le nom de table suivant
            continue;
        }
    }
    return 0;
}

// Variantes possibles pour les noms de tables (ajoute les tiennes si nécessaire)
$userTables = ['user', 'users', 'utilisateur', 'utilisateurs'];
$projectTables = ['project', 'projects', 'projet', 'projets'];
$taskTables = ['task', 'tasks', 'tache', 'taches', 'todo', 'todos'];

// Récupération des compteurs
$userCount = getCountFromPossibleNames($db, $userTables);
$projectCount = getCountFromPossibleNames($db, $projectTables);
$taskCount = getCountFromPossibleNames($db, $taskTables);
