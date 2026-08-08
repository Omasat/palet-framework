<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Factories;

use Palet\Framework\Contracts\Database\Factories\FactoryInterface;
use Palet\Framework\Contracts\Database\Factories\FactoryBuilderInterface;

abstract class Factory implements FactoryInterface
{
    protected FakerAdapter $faker;

    public function __construct()
    {
        $this->faker = new FakerAdapter();
    }

    public static function new(): FactoryBuilderInterface
    {
        return new FactoryBuilder(static::class);
    }
}
