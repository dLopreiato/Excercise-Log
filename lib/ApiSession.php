<?php

class ApiSession
{
    private $dbConnection;

    public function __construct($dbConn)
    {
        $this->dbConnection = $dbConn;
    }

    public function GetExcerciseById($id)
    {
        self::VerifyInputIsInteger($id);

        $returnArray = null;

        $excerciseInfoQuery = 'SELECT name, reference_video FROM excercises WHERE id=%1$d';
        $excerciseInfoRes = $this->dbConnection->query(sprintf($excerciseInfoQuery, $id));
        $this->CheckDBError();
        if ($excerciseInfoRes->num_rows <= 0)
        {
            throw new Exception('This excercise does not exist.');
        }

        $returnArray = $excerciseInfoRes->fetch_assoc();
        $returnArray['muscles'] = array();

        $muscleListQuery = 'SELECT m.id, m.name FROM excercise_muscle_mapping emm INNER JOIN muscles m ON m.id=emm.muscle_id WHERE emm.excercise_id=%1$d';
        $muscleListRes = $this->dbConnection->query(sprintf($muscleListQuery, $id));
        $this->CheckDBError();
        while ($row = $muscleListRes->fetch_assoc())
        {
            $returnArray['muscles'][] = $row;
        }

        return $returnArray;
    }

    public function GetPlanByExcercise($id)
    {
        self::VerifyInputIsInteger($id);

        $planInfoQuery = 'SELECT goal_reps, goal_weights FROM planned_excercise WHERE excercise_id=%1$d AND planned_date=CURRENT_DATE';
        $planInfoRes = $this->dbConnection->query(sprintf($planInfoQuery, $id));
        $this->CheckDBError();
        if ($planInfoRes->num_rows <= 0)
        {
            throw new Exception('There are no plans for this excercise today.');
        }

        return $planInfoRes->fetch_assoc();
    }

    public function SavePerformance($id, $repetitions, $weight, $readyToIncrease)
    {
        self::VerifyInputIsRepetition($repetitions);
        self::VerifyInputIsWeight($weight);
        self::VerifyInputIsBoolean($readyToIncrease);

        $savePerformanceQuery = 'INSERT INTO performed_excercise (`performed_date`, `excercise_id`, `performed_reps`, `performed_weights`, `ready_to_increase`)'
            . ' VALUES (CURRENT_DATE, %1$d, \'%2$s\', \'%3$s\', %4$d) ON DUPLICATE KEY UPDATE `performed_reps`=\'%2$s\', `performed_weights`=\'%3$s\', `ready_to_increase`=%4$s';
        $this->dbConnection->query(sprintf($savePerformanceQuery, $id, $repetitions, $weight, $readyToIncrease));
        $this->CheckDBError();
    }

    public function GetTodaysExcercises()
    {
        $todaysExcercisesQuery = 'SELECT p.excercise_id, e.name, (if((SELECT pe.performed_reps FROM performed_excercise pe WHERE pe.performed_date=CURRENT_DATE AND pe.excercise_id=p.excercise_id) IS NULL, 0, 1)) as \'completed\' FROM planned_excercise p LEFT JOIN excercises e ON p.excercise_id=e.id WHERE p.planned_date=CURRENT_DATE ORDER BY completed ASC';
        $todaysExcercisesRes = $this->dbConnection->query($todaysExcercisesQuery);
        $this->CheckDBError();
        $returnArray = array();

        while ($row = $todaysExcercisesRes->fetch_assoc())
        {
            $returnArray[] = $row;
        }

        return $returnArray;
    }

    private function CheckDBError()
    {
        if ($this->dbConnection->errno != 0)
        {
            throw new Exception('A database error has occurred: ' . $this->dbConnection->errno);
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