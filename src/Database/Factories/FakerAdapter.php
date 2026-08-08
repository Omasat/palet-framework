<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Factories;

class FakerAdapter
{
    // A simple stub for Faker, in a real framework we would compose Faker\Generator
    // Since we don't have FakerPHP installed in our barebones stubs, we'll mock basic returns.
    
    public function name(): string
    {
        $names = ['John Doe', 'Jane Smith', 'Michael Johnson', 'Emily Davis'];
        return $names[array_rand($names)];
    }
    
    public function email(): string
    {
        return strtolower(str_replace(' ', '.', $this->name())) . '@example.com';
    }
    
    public function boolean(int $chanceOfGettingTrue = 50): bool
    {
        return mt_rand(1, 100) <= $chanceOfGettingTrue;
    }
    
    public function randomElement(array $array): mixed
    {
        return $array[array_rand($array)];
    }
    
    public function word(): string
    {
        $words = ['lorem', 'ipsum', 'dolor', 'sit', 'amet'];
        return $words[array_rand($words)];
    }
}
