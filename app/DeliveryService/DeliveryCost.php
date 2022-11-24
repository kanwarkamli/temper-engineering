<?php

namespace App\DeliveryService;

use App\Offer\Offer;

class DeliveryCost extends Offer
{
    protected float $base_cost;
    protected int $number_of_package;
    protected float $distance;
    protected float $weight = 0;
    protected int|float $discount_amount;

    public function __construct()
    {
        //
    }

    /**
     * @param float $base_cost
     * @return $this
     */
    public function setBaseCost(float $base_cost): DeliveryCost
    {
        $this->base_cost = $base_cost;
        return $this;
    }

    /**
     * @return float
     */
    public function getBaseCost(): float
    {
        return $this->base_cost;
    }

    /**
     * @param float $weight
     * @return $this
     */
    public function setWeight(float $weight): DeliveryCost
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param int $number_of_package
     * @return $this
     */
    public function setNumberOfPackage(int $number_of_package): DeliveryCost
    {
        $this->number_of_package = $number_of_package;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPackage(): int
    {
        return $this->number_of_package;
    }

    /**
     * @param float $distance
     * @return $this
     */
    public function setDistance(float $distance): DeliveryCost
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount(float $amount): DeliveryCost
    {
        $this->discount_amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discount_amount;
    }

    /**
     * Calculate the delivery cost
     * Base delivery cost + (Package total weight * 10) + (Distance to destination * 5)
     *
     * @return float
     */
    public function calculate(): float
    {
        $total_cost = $this->base_cost + ($this->weight * config('delivery.weight_factor')) + ($this->distance * config('delivery.distance_factor'));

        if ($this->isEligible()) {
            $this->setDiscountAmount($this->getDiscountRate() * $total_cost);
            return $total_cost - $this->getDiscountAmount();
        }

        $this->setDiscountAmount(0);
        return $total_cost;
    }

    /**
     * @param string|null $offer_code
     * @return void
     */
    public function initiateOffer(string $offer_code = null)
    {
        $this->setOffer($offer_code);
    }
}
