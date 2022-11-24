<?php

namespace App\Offer;

abstract class Offer
{
    private float|null $min_distance;
    private float|null $min_weight;
    private float|null $max_weight;
    private float|null $max_distance;
    private float|null $discount_rate;
    private bool $offer_code_null = false;

    /**
     * @return bool
     */
    public function isEligible(): bool
    {
        if ($this->offer_code_null) {
            return false;
        }

        return ($this->weight >= $this->getMinWeight() && $this->weight <= $this->getMaxWeight())
            && ($this->distance >= $this->getMinDistance() && $this->distance <= $this->getMaxDistance());
    }

    /**
     * @param $offer_code
     * @return bool
     */
    public function setOffer($offer_code): bool
    {
        if (is_null($offer_code)) {
            $this->offer_code_null = true;
            return false;
        }

        $offer_code = strtolower($offer_code);

        $this->setMinDistance(config("offer.{$offer_code}.min_distance"));
        $this->setMaxDistance(config("offer.{$offer_code}.max_distance"));
        $this->setMinWeight(config("offer.{$offer_code}.min_weight"));
        $this->setMaxWeight(config("offer.{$offer_code}.max_weight"));
        $this->setDiscountRate(config("offer.{$offer_code}.discount"));

        return true;
    }

    public function setMinDistance(float|null $distance): static
    {
        $this->min_distance = $distance;
        return $this;
    }

    public function getMinDistance(): ?float
    {
        return $this->min_distance;
    }

    public function setMaxDistance(float|null $distance): static
    {
        $this->max_distance = $distance;
        return $this;
    }

    public function getMaxDistance(): ?float
    {
        return $this->max_distance;
    }

    public function setMinWeight(float|null $weight): static
    {
        $this->min_weight = $weight;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinWeight(): ?float
    {
        return $this->min_weight;
    }

    /**
     * @param float|null $weight
     * @return $this
     */
    public function setMaxWeight(float|null $weight): static
    {
        $this->max_weight = $weight;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxWeight(): ?float
    {
        return $this->max_weight;
    }

    /**
     * @param float|null $discount
     * @return $this
     */
    public function setDiscountRate(float|null $discount): static
    {
        $this->discount_rate = $discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountRate(): float
    {
        return $this->isEligible()
            ? $this->discount_rate
            : 0;
    }
}
