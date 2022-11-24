<?php

namespace Tests\App\DeliveryService;

use App\DeliveryService\DeliveryCost;
use Tests\TestCase;

class DeliveryCostTest extends TestCase
{
    private DeliveryCost $delivery_cost;

    public function setUp(): void
    {
        parent::setUp();

        $this->delivery_cost = new DeliveryCost();
        $this->delivery_cost->setBaseCost(100);
        $this->delivery_cost->setWeight(10);
        $this->delivery_cost->setDistance(100);
    }

    public function test_set_base_cost()
    {
        $this->assertSame(100.00, $this->delivery_cost->getBaseCost());
    }

    public function test_set_weight()
    {
        $this->assertSame(10.00, $this->delivery_cost->getWeight());
    }

    public function test_set_distance()
    {
        $this->assertSame(100.00, $this->delivery_cost->getDistance());
    }

    public function test_discount_eligibility_is_false()
    {
        $this->delivery_cost->initiateOffer('OFR001');
        $this->delivery_cost->setWeight(5);
        $this->delivery_cost->setDistance(5);
        $this->assertFalse($this->delivery_cost->isEligible());
    }

    public function test_discount_eligibility_is_true()
    {
        $this->delivery_cost->initiateOffer('OFR003');
        $this->assertTrue($this->delivery_cost->isEligible());
    }

    public function test_discount_amount_is_zero_if_criteria_not_met()
    {
        $this->delivery_cost->setWeight(5);
        $this->delivery_cost->setDistance(5);

        $this->delivery_cost->initiateOffer('OFR001');
        $this->delivery_cost->calculate();

        $this->assertSame(0.0, $this->delivery_cost->getDiscountAmount());
    }

    public function test_discount_amount_is_not_zero_if_criteria_are_met()
    {
        $this->delivery_cost->initiateOffer('OFR003');
        $this->delivery_cost->calculate();

        $this->assertSame(35.0, $this->delivery_cost->getDiscountAmount());
    }

    public function test_delivery_cost_without_promo()
    {
        $this->delivery_cost->initiateOffer();
        $this->assertSame(700.0, $this->delivery_cost->calculate());
    }

    public function test_delivery_cost_with_promo()
    {
        $this->delivery_cost->initiateOffer('OFR003');
        $this->assertSame(665.0, $this->delivery_cost->calculate());
    }

    public function test_delivery_cost_with_invalid_promo_throws_exception()
    {
        $this->markTestSkipped('Code updated/refactored');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Offer code not existed!');

        $this->offer_factory->initialize('NOTEXISTEDPROMO', $this->delivery_cost);
    }

    public function test_discount_amount_is_zero_with_invalid_promo()
    {
        $this->markTestSkipped('Code updated/refactored');

        try {
            $this->offer_factory->initialize('NOTEXISTEDPROMO', $this->delivery_cost);
        } catch (\Exception $e) {
            $this->delivery_cost->calculate();
        }

        $this->assertSame(0.0, $this->delivery_cost->getDiscountAmount());
    }
}
