<?php

class MysqlPdoMutex extends MysqlMutex
{
    public $pdo;

    public function __construct($pdo)
    {
        parent::__construct();
        $this->pdo = $pdo;
    }

    protected function databaseQuery($sql, $params)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn(0);
    }
}
