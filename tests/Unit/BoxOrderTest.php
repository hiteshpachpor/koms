<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;
use App\BoxOrder;

class BoxOrderTest extends TestCase
{
    /**
     * Unit tests for BoxOrder::isDeliveryDateSlotValid method
     * Should return true if the given date & slot is valid
     * Should return false if the given date & slot is not valid
     *
     * @return void
     */
    public function testIsDeliveryDateSlotValid()
    {
        // Facades are not available in unit test scope
        // Set the dependent config values manually
        Config::shouldReceive('get')
            ->with('constants.box_order_delivery_slot')
            ->andReturn(['Morning', 'Afternoon', 'Evening']);

        // Morning slot 3 days from today - valid slot
        $date = new \DateTime('+3 days');
        $slot = 'Morning';

        $isValid = BoxOrder::isDeliveryDateSlotValid(
            $date->format('Y-m-d'),
            $slot
        );

        $this->assertTrue($isValid);

        // Evening slot today - not a valid slot
        $date = new \DateTime();
        $slot = 'Evening';

        $isValid = BoxOrder::isDeliveryDateSlotValid(
            $date->format('Y-m-d'),
            $slot
        );

        $this->assertFalse($isValid);

        // Afternoon slot 2 days ago - not a valid slot
        $date = new \DateTime('-2 days');
        $slot = 'Afternoon';

        $isValid = BoxOrder::isDeliveryDateSlotValid(
            $date->format('Y-m-d'),
            $slot
        );

        $this->assertFalse($isValid);

        // Today morning slot (default when no arguments passed) - not a valid slot
        $isValid = BoxOrder::isDeliveryDateSlotValid();

        $this->assertFalse($isValid);
    }
}
