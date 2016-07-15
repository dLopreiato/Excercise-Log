<?php

require_once('../lib/server_variables.php');

$databaseConnection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DBNAME);

/*Input Handling
=================*/
$successfullyAdded = null;
if (isset($_POST['exercisename']))
{
    $successfullyAdded = false;

    $insertExerciseQuery = 'INSERT INTO exercises (`name`) VALUES (\'%1$s\')';
    if ($databaseConnection->query(sprintf($insertExerciseQuery, $databaseConnection->real_escape_string($_POST['exercisename']))))
    {
       $successfullyAdded = true;
    }
}

if (isset($_POST['muscles']) && !empty($_POST['muscles']))
{
    $lastId = $databaseConnection->insert_id;
    $insertMappingQuery = 'INSERT INTO exercise_muscle_mapping (`exercise_id`, `muscle_id`) VALUES (' . $lastId
        . ',' . implode('),(' . $lastId . ',', $_POST['muscles']) . ')';
    $databaseConnection->query($insertMappingQuery);
}

/*Output Handling
=================*/
$musclesRes = $databaseConnection->query('SELECT id, name FROM muscles');
?>

<html>
<head>
    <title>Add an Exercise Utility</title>
</head>
<body>
    <form method="post">
        <input type="text" name="exercisename" />
        <br />
        <!-- Muscle Listing -->
        <?php while ($muscleRow = $musclesRes->fetch_assoc()) { ?>
        <input type="checkbox" name="muscles[]" value="<?php echo $muscleRow['id']; ?>" />
        <?php echo $muscleRow['name']; ?>
        <br />
        <?php } ?>
        <!-- End Muscle Listing -->
        <input type="submit" value="Submit" />
    </form>

    <?php echo (($successfullyAdded) ? ('You have successfully added "' . $_POST['exercisename'] . '" to the exercises.') : ('')); ?>
</body>
</html>