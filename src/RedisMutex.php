<?php

abstract class RedisMutex extends Mutex
{
    public $expire = 30;
    private $_lockValues = [];

    protected function acquireLock($name, $timeout = 0)
    {
        $tokenValue = $this->generateRandomToken();
        $result = $this->retryAcquire($timeout, function () use ($name, $tokenValue) {
            return $this->executeCommand(['SET', $name, $tokenValue, 'NX', 'EX', $this->expire]);
        });
        if ($result) {
            $this->_lockValues[$name] = $tokenValue;
        }
        return $result;
    }

    protected function releaseLock($name)
    {
        if (!isset($this->_lockValues[$name])) {
            return false;
        }
        // https://redis.io/commands/set/
        static $script = <<<LUA
if redis.call("GET",KEYS[1])==ARGV[1]
then
    return redis.call("DEL",KEYS[1])
else
    return 0
end
LUA;
        if (!$this->executeCommand(['EVAL', $script, 1, $name, $this->_lockValues[$name]])) {
            return false;
        }
        unset($this->_lockValues[$name]);
        return true;
    }

    /**
     * 执行Redis命令
     * @param array $params
     */
    abstract protected function executeCommand($params);
}
