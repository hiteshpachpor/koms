<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Inventory;

class InventoryTest extends TestCase
{
    /**
     * Unit tests for Inventory::databaseSpecificFieldCast method
     * Should correctly return statement as per given database
     *
     * @return void
     */
    public function testDatabaseSpecificFieldCastMethod()
    {
        // Test for mysql database
        $database = 'mysql';
        $field = 'ingredient_name';
        $alias = 'name';

        $result = Inventory::databaseSpecificFieldCast(
            $database,
            $field,
            $alias
        );

        $this->assertEquals($result, 'ANY_VALUE(ingredient_name) AS name');

        // Test for sqlite database
        $database = 'sqlite';
        $field = 'ingredient_measure';
        $alias = 'measure';

        $result = Inventory::databaseSpecificFieldCast(
            $database,
            $field,
            $alias
        );

        $this->assertEquals($result, 'ingredient_measure AS measure');
    }
}
