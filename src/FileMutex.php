<?php

class FileMutex extends Mutex
{
    /**
     * @var string 锁文件存放路径
     */
    public $mutexPath;

    /**
     * @var resource[] 锁文件资源句柄
     */
    private $_files = [];

    public function init()
    {
        parent::init();
        $this->initMutextPath();
    }

    protected function acquireLock($name, $timeout = 0)
    {
        $filePath = $this->mutexPath . DIRECTORY_SEPARATOR . md5($name) . '.lock';
        return $this->retryAcquire($timeout, function () use ($filePath, $name) {
            $fp = fopen($filePath, 'w+');
            if ($fp === false) {
                return false;
            }
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                fclose($fp);
                return false;
            }
            $this->_files[$name] = $fp;
            return true;
        });
    }

    protected function releaseLock($name)
    {
        if (!isset($this->_files[$name])) {
            return false;
        }
        $filePath = $this->mutexPath . DIRECTORY_SEPARATOR . md5($name) . '.lock';
        flock($this->_files[$name], LOCK_UN);
        fclose($this->_files[$name]);
        @unlink($filePath);
        unset($this->_files[$name]);
        return true;
    }

    private function initMutextPath()
    {
        if ($this->mutexPath === null) {
            $this->mutexPath = sys_get_temp_dir() . '/mutex';
        } else {
            $this->mutexPath = rtrim($this->mutexPath, '\\/');
        }
        if (!is_dir($this->mutexPath)) {
            @mkdir($this->mutexPath, 0755, true);
        }
    }
}
