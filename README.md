# php-mutex
php-mutex是一个互斥锁的实现，支持多种方式，包括文件锁、MySQL、Redis。

 
## 使用方法

### 初始化实列

文件锁
```php
$lock = new FileMutex();
```

MySQL锁
```php
$pdo = new PDO('mysql:host=127.0.0.1', 'root', 'root');
$lock = new MysqlPdoMutex($pdo);
```

Redis锁
```php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$lock = new PhpRedisMutex($redis);
```

### 获得和释放锁

```php
$mutexName = 'xxx';
if ($lock->acquire($mutexName, 3)) {
    // 业务逻辑代码……

    // 释放锁
    $lock->release($mutexName);
} else {
    // 获取锁失败，停止执行后续
    throw new Exception('Unable to gain lock!');
}
```

## 执行测试

```bash
composer install
vendor/bin/phpunit
```

## 链接

* [PHP文件锁](https://www.php.net/flock)
* [MySQL锁](https://dev.mysql.com/doc/refman/5.7/en/locking-functions.html)
* [Redis锁](https://redis.io/commands/set/)