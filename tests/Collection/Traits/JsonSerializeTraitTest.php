<?php

namespace Jasny\EntityCollection\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\Traits\JsonSerializeTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\JsonSerializeTrait
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * Test 'jsonSerialize' method
     */
    public function testJsonSerialize()
    {
        $expected = ['foo', 'bar'];

        $collection = $this->getMockForTrait(JsonSerializeTrait::class);
        $collection->expects($this->once())->method('toArray')->willReturn($expected);

        $result = $collection->jsonSerialize();

        $this->assertSame($expected, $result);
    }
}
