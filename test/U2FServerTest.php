<?php

namespace Samyoul\U2F\U2FServer\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Samyoul\U2F\U2FServer\U2FServer;

class U2FServerTest extends TestCase
{
    protected static function getClassMethod(string $className, string $methodName) {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    public function testCreateChallenge(): void
    {
        $foo = self::getClassMethod(U2FServer::class, 'createChallenge');
        $challengeResult = $foo->invokeArgs(new U2FServer(), []);
        $this->assertNotEmpty($challengeResult);
        $this->assertGreaterThan(20, strlen($challengeResult));
    }
}
