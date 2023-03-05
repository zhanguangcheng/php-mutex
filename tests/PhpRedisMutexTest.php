<?php

use PHPUnit\Framework\TestCase;

class PhpRedisMutexTest extends TestCase
{
    public function testLock()
    {
        $redis = new \Redis('127.0.0.1', 6379);
        $lock = new PhpRedisMutex($redis);
        $this->assertTrue($lock->acquire('lock1'));
        $this->assertTrue($lock->isAcquired('lock1'));
        $this->assertTrue($lock->release('lock1'));
        $this->assertFalse($lock->isAcquired('lock1'));
        $this->assertFalse($lock->release('lock1'));
    }
}
