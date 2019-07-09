<?php

namespace BajakLautMalaka\PmiDonatur\Traits;

trait DonatorUserTrait
{
    /**
     * hasOne relation with Donator.
     *
     * @return mixed
     */
    public function donator()
    {
        return $this->hasOne('\BajakLautMalaka\PmiDonatur\Donator');
    }
}