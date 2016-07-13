<?php

class ApiSession
{
    private $dbConnection;

    public function __construct($dbConn)
    {
        $this->dbConnection = $dbConn;
    }

    public function GetExerciseById($id)
    {
        self::VerifyInputIsInteger($id);

        $returnArray = null;

        $exerciseInfoQuery = 'SELECT name, reference_video FROM exercises WHERE id=%1$d';
        $exerciseInfoRes = $this->dbConnection->query(sprintf($exerciseInfoQuery, $id));
        $this->CheckDBError();
        if ($exerciseInfoRes->num_rows <= 0)
        {
            throw new Exception('This exercise does not exist.');
        }

        $returnArray = $exerciseInfoRes->fetch_assoc();
        $returnArray['muscles'] = array();

        $muscleListQuery = 'SELECT m.id, m.name FROM exercise_muscle_mapping emm INNER JOIN muscles m ON m.id=emm.muscle_id WHERE emm.exercise_id=%1$d';
        $muscleListRes = $this->dbConnection->query(sprintf($muscleListQuery, $id));
        $this->CheckDBError();
        while ($row = $muscleListRes->fetch_assoc())
        {
            $returnArray['muscles'][] = $row;
        }

        return $returnArray;
    }

    public function GetPlanByExercise($id)
    {
        self::VerifyInputIsInteger($id);

        $planInfoQuery = 'SELECT goal_reps, goal_weights FROM planned_exercise WHERE exercise_id=%1$d AND planned_date=CURRENT_DATE';
        $planInfoRes = $this->dbConnection->query(sprintf($planInfoQuery, $id));
        $this->CheckDBError();
        if ($planInfoRes->num_rows <= 0)
        {
            throw new Exception('There are no plans for this exercise today.');
        }

        return $planInfoRes->fetch_assoc();
    }

    public function SavePerformance($id, $repetitions, $weight, $readyToIncrease)
    {
        self::VerifyInputIsRepetition($repetitions);
        self::VerifyInputIsWeight($weight);
        self::VerifyInputIsBoolean($readyToIncrease);

        $savePerformanceQuery = 'INSERT INTO performed_exercise (`performed_date`, `exercise_id`, `performed_reps`, `performed_weights`, `ready_to_increase`)'
            . ' VALUES (CURRENT_DATE, %1$d, \'%2$s\', \'%3$s\', %4$d) ON DUPLICATE KEY UPDATE `performed_reps`=\'%2$s\', `performed_weights`=\'%3$s\', `ready_to_increase`=%4$s';
        $this->dbConnection->query(sprintf($savePerformanceQuery, $id, $repetitions, $weight, $readyToIncrease));
        $this->CheckDBError();
    }

    public function GetTodaysExercises()
    {
        $todaysExercisesQuery = 'SELECT p.exercise_id, e.name, (if((SELECT pe.performed_reps FROM performed_exercise pe WHERE pe.performed_date=CURRENT_DATE AND pe.exercise_id=p.exercise_id) IS NULL, 0, 1)) as \'completed\' FROM planned_exercise p LEFT JOIN exercises e ON p.exercise_id=e.id WHERE p.planned_date=CURRENT_DATE ORDER BY completed ASC';
        $todaysExercisesRes = $this->dbConnection->query($todaysExercisesQuery);
        $this->CheckDBError();
        $returnArray = array();

        while ($row = $todaysExercisesRes->fetch_assoc())
        {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    public function GetWorkoutCategories()
    {
        $categoriesQuery = 'SELECT id, category FROM workout_categories';
        $categoriesRes = $this->dbConnection->query($categoriesQuery);
        $this->CheckDBError();
        $returnArray = array();

        while ($row = $categoriesRes->fetch_assoc())
        {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    public function SetWorkoutCategory($date, $categoryId)
    {
        self::VerifyInputIsInteger($categoryId);
        self::VerifyInputIsDate($date);

        $setCategoryQuery = 'INSERT INTO planned_workout (planned_date, category) VALUES (\'%1$s\', %2$d) ON DUPLICATE KEY UPDATE category=%2$d';
        $this->dbConnection->query(sprintf($setCategoryQuery, $date, $categoryId));
        $this->CheckDBError();
    }

    public function GetCurrentWorkoutCategory($date)
    {
        self::VerifyInputIsDate($date);

        $selectCategoryQuery = 'SELECT category FROM planned_workout WHERE planned_date=\'%1$s\'';
        $selectCategoryRes = $this->dbConnection->query(sprintf($selectCategoryQuery, $date));
        $this->CheckDBError();
        if ($selectCategoryRes->num_rows <= 0)
        {
            return null;
        }
        else
        {
            return $selectCategoryRes->fetch_assoc();
        }
    }

    private function CheckDBError()
    {
        if ($this->dbConnection->errno != 0)
        {
            throw new Exception('A database error has occurred: ' . $this->dbConnection->errno);
        }
    }

    private static function VerifyInputIsDate($input)
    {
        if (preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $input) == 0)
        {
            throw new Exception('Given input is not valid for a date.');
        }
    }

    private static function VerifyInputIsBoolean($input)
    {
        if (preg_match('/^0|1$/', $input) == 0)
        {
            throw new Exception('Given input is not valid for a boolean.');
        }
    }

    private static function VerifyInputIsRepetition($input)
    {
        if (preg_match('/^[0-9A-Za-z,\s]+$/', $input) == 0)
        {
            throw new Exception('Given input is not valid for repetitions.');
        }
    }

    private static function VerifyInputIsWeight($input)
    {
        if (preg_match('/^[0-9A-Za-z,\s]+$/', $input) == 0)
        {
            throw new Exception('Given input is not valid for weights.');
        }
    }


    private static function VerifyInputIsInteger($input)
    {
        if (preg_match('/^[0-9]+$/', $input) == 0)
        {
            throw new Exception('Given input is not a valid integer.');
        }
    }
}
?>