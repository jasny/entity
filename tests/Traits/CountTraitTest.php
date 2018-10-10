<?php

namespace Jasny\EntityCollection\Tests\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\Traits\CountTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\CountTrait
 */
class CountTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|CountTrait
     */
    public $collection;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(CountTrait::class);
    }

    /**
     * Provide data for testing 'count' method
     *
     * @return array
     */
    public function countProvider()
    {
        $entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        return [
            [$entities, 3],
            [[], 0]
        ];
    }

    /**
     * Test 'count' method
     *
     * @dataProvider countProvider
     */
    public function testCount($entities, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $this->assertSame($expected, $this->collection->count());
    }
}
