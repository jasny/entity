<?php

namespace Jasny\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\EntityCollection\Traits\JsonSerializeTrait;

/**
 * @covers Jasny\EntityCollection\Traits\JsonSerializeTrait
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
