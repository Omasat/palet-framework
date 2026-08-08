<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Factories;

interface FactoryInterface
{
    /**
     * Define the model's default state.
     */
    public function definition(): array;

    /**
     * Create a new factory builder for the model.
     */
    public static function new(): FactoryBuilderInterface;
}
