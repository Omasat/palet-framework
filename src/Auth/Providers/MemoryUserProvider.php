<?php

declare(strict_types=1);

namespace Palet\Framework\Auth\Providers;

use Palet\Framework\Contracts\Auth\UserProviderInterface;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;

class MemoryUserProvider implements UserProviderInterface
{
    protected array $users;

    public function __construct(array $users = [])
    {
        $this->users = $users;
    }

    public function retrieveById(mixed $identifier): ?AuthenticatableInterface
    {
        foreach ($this->users as $user) {
            if ($user->getAuthIdentifier() == $identifier) {
                return $user;
            }
        }
        return null;
    }

    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface
    {
        foreach ($this->users as $user) {
            $match = true;
            foreach ($credentials as $key => $value) {
                if ($key === 'password') {
                    continue;
                }
                
                // Assuming properties are public or accessible for this mock
                if (!isset($user->{$key}) || $user->{$key} !== $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                return $user;
            }
        }
        
        return null;
    }

    public function validateCredentials(mixed $user, array $credentials): bool
    {
        if (!$user instanceof AuthenticatableInterface) {
            return false;
        }
        
        $plain = $credentials['password'] ?? '';
        return password_verify($plain, $user->getAuthPassword());
    }
}
