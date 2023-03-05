<?php

use PHPUnit\Framework\TestCase;

class FileMutexTest extends TestCase
{
    public function testLock()
    {
        @rmdir(sys_get_temp_dir() . '/mutex');
        $lock = new FileMutex();
        $lock->acquire('lock1');
        $filePath = $lock->mutexPath . DIRECTORY_SEPARATOR . md5('lock1') . '.lock';
        $this->assertTrue($lock->isAcquired('lock1'));
        $this->assertFileExists($filePath);
        $lock->release('lock1');
        $this->assertFileNotExists($filePath);
        $this->assertFalse($lock->isAcquired('lock1'));
    }

    public function testAutoRelease()
    {
        $lock = new FileMutex();
        $filePath = $lock->mutexPath . DIRECTORY_SEPARATOR . md5('lock2') . '.lock';
        $lock->acquire('lock2');
        unset($lock);
        $this->assertFileNotExists($filePath);
    }
}
