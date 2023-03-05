<?php

use PHPUnit\Framework\TestCase;

class MysqlPdoMutexTest extends TestCase
{
    public function testLock()
    {
        $pdo = new PDO('mysql:host=127.0.0.1', 'root', 'root');
        $lock = new MysqlPdoMutex($pdo);
        $lock->acquire('lock1');
        $this->assertTrue($lock->isAcquired('lock1'));
        $lock->release('lock1');
        $this->assertFalse($lock->isAcquired('lock1'));
    }
}
