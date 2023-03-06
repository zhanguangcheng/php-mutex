<?php

/**
 * 互斥锁
 * @author ZhanGuangcheng <14712905@qq.com>
 * @link https://github.com/zhanguangcheng/php-mutex
 */
abstract class Mutex
{
    /**
     * @var bool 自动释放锁
     */
    public $autoRelease = true;

    /**
     * @var int 重试间隔，单位毫秒
     */
    public $retryDelay = 50;

    /**
     * @var string[] 锁记录
     */
    private $_locks = [];

    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
        $this->init();
    }

    public function __destruct()
    {
        if ($this->autoRelease) {
            $locks = &$this->_locks;
            foreach ($locks as $lock) {
                $this->release($lock);
            }
        }
    }

    public function init()
    {
    }

    /**
     * 获取锁
     * @param string $name 锁名称
     * @param int $timeout 超时时间，单位秒
     * @return bool
     */
    public function acquire($name, $timeout = 0)
    {
        if (!in_array($name, $this->_locks, true) && $this->acquireLock($name, $timeout)) {
            $this->_locks[] = $name;
            return true;
        }

        return false;
    }

    /**
     * 释放锁
     * @param string $name 锁名称
     * @return bool
     */
    public function release($name)
    {
        if ($this->releaseLock($name)) {
            $index = array_search($name, $this->_locks);
            if ($index !== false) {
                unset($this->_locks[$index]);
            }

            return true;
        }

        return false;
    }

    /**
     * 是否已锁定
     * @param string $name
     * @return bool
     */
    public function isAcquired($name)
    {
        return in_array($name, $this->_locks, true);
    }

    /**
     * 生成随机数，来自于Yii2框架
     * @param  integer $length 字节长度，返回的是2倍字符串长度的字符
     * @return string
     */
    public function generateRandomToken($length = 16)
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            return bin2hex(file_get_contents('/dev/urandom', false, null, 0, $length));
        }
        // Last resort which you probably should just get rid of:
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);

        return substr(hash('sha512', $randomData), 0, $length * 2);
    }

    /**
     * 在一个限定时间内不断得重试
     * @param int 超时时间，单位秒，可使用小数
     * @param Closure 重试回调
     * @return bool
     */
    protected function retryAcquire($timeout, Closure $callback)
    {
        $start = microtime(true);
        do {
            if ($callback()) {
                return true;
            }
            usleep($this->retryDelay * 1000);
        } while (microtime(true) - $start < $timeout);

        return false;
    }

    /**
     * 获取锁
     * @param string $name 锁名称
     * @param int $timeout 超时时间，单位秒
     * @return bool
     */
    abstract protected function acquireLock($name, $timeout = 0);

    /**
     * 释放锁
     * @param string $name 锁名称
     * @return bool
     */
    abstract protected function releaseLock($name);
}
