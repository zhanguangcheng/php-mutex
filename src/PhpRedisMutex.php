<?php

class PhpRedisMutex extends RedisMutex
{
    public $redis;
    
    public function __construct($redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }

    protected function executeCommand($params)
    {
        return call_user_func_array([$this->redis, 'rawCommand'], $params);
    }
}
