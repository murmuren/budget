<?php

class DB
{
    public static function execute($strQuery, $boundParameters = array())
    {
        $benchmark = false;

        if ($benchmark) {
            $preparedStatement = SQLConnectionSingleton::getInstance()->createPreparedStatement();

            $preparedStatement->prepare(preg_replace("/^(select)/", "SELECT SQL_NO_CACHE", $strQuery));

            foreach ($boundParameters as $strParameter => $mixValue) {
                $preparedStatement->bind($strParameter, $mixValue);
            }

            require_once dirname(dirname(__DIR__)).'/hlis/QueryProfiler.php';
            $profiler = new QueryProfiler($strQuery);
            return $preparedStatement->execute();
        } else {
            $preparedStatement = SQLConnectionSingleton::getInstance()->createPreparedStatement();
            $preparedStatement->prepare($strQuery);
            foreach ($boundParameters as $strParameter => $mixValue) {
                $preparedStatement->bind($strParameter, $mixValue);
            }
            return $preparedStatement->execute();
        }
    }
}