<?php

namespace Jasny\Entity\Tests;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\AbstractBasicEntity
 */
class AbstractBasicEntityTest extends TestCase
{
    use TestHelper;

    protected function createObjectWithTrigger(callable $trigger)
    {
        return new class($trigger) extends AbstractBasicEntity {
            private $trigger;

            public function __construct(callable $trigger) {
                $this->trigger = $trigger;
            }

            public function trigger(string $event, $payload = null) {
                $args = func_get_args();
                return call_user_func_array($this->trigger, $args);
            }
        };
    }

    public function testDestruct()
    {
        $callback = $this->createCallbackMock($this->once(), ['destruct']);
        $entity = $this->createObjectWithTrigger($callback);

        unset($entity);
    }
}
