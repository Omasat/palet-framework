<?php

declare(strict_types=1);

namespace Palet\Framework\Auth\Providers;

use Palet\Framework\Contracts\Auth\UserProviderInterface;
use Palet\Framework\Contracts\Auth\PasswordHasherInterface;
use Palet\Framework\Contracts\Auth\AuthenticatableInterface;
use Palet\Framework\Contracts\Database\DatabaseManagerInterface;
use PDO;

class DatabaseUserProvider implements UserProviderInterface
{
    protected DatabaseManagerInterface $db;
    protected PasswordHasherInterface $hasher;
    protected string $model;
    protected string $table;

    public function __construct(DatabaseManagerInterface $db, PasswordHasherInterface $hasher, string $model, string $table = 'users')
    {
        $this->db = $db;
        $this->hasher = $hasher;
        $this->model = $model;
        $this->table = $table;
    }

    public function retrieveById(mixed $identifier): mixed
    {
        $pdo = $this->db->connection()->getPdo();
        $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $identifier]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $model = $this->createModel();
            $model->forceFill($user);
            return $model;
        }

        return null;
    }

    public function retrieveByCredentials(array $credentials): mixed
    {
        if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        $conditions = [];
        $bindings = [];
        foreach ($credentials as $key => $value) {
            if ($key !== 'password') {
                $conditions[] = "{$key} = :{$key}";
                $bindings[$key] = $value;
            }
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $conditions) . " LIMIT 1";
        $pdo = $this->db->connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $model = $this->createModel();
            $model->forceFill($user);
            return $model;
        }

        return null;
    }

    public function validateCredentials(mixed $user, array $credentials): bool
    {
        if (!$user instanceof AuthenticatableInterface) {
            return false;
        }
        
        return $this->hasher->check(
            $credentials['password'], $user->getAuthPassword()
        );
    }

    public function createModel(): mixed
    {
        $class = '\\' . ltrim($this->model, '\\');
        return new $class;
    }
}
