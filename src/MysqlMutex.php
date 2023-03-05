<?php

abstract class MysqlMutex extends Mutex
{
    protected function acquireLock($name, $timeout = 0)
    {
        $sql = "SELECT GET_LOCK(:name, :timeout)";
        $params = [
            ':name' => md5($name),
            ':timeout' => $timeout,
        ];
        return (bool) $this->databaseQuery($sql, $params);
    }

    protected function releaseLock($name)
    {
        $sql = "SELECT RELEASE_LOCK(:name)";
        $params = [
            ':name' => md5($name),
        ];
        return (bool) $this->databaseQuery($sql, $params);
    }

    abstract protected function databaseQuery($sql, $params);
}
