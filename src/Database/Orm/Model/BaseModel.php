<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model;

use Palet\Framework\Contracts\Database\Orm\Model\ModelInterface;
use Palet\Framework\Contracts\Database\Orm\Model\FillableInterface;
use Palet\Framework\Contracts\Database\Orm\Model\CastableInterface;
use Palet\Framework\Contracts\Support\ArrayableInterface;
use Palet\Framework\Contracts\Support\JsonableInterface;
use Palet\Framework\Database\Orm\EntityManager;

abstract class BaseModel implements ModelInterface, FillableInterface, CastableInterface, ArrayableInterface, JsonableInterface
{
    use AttributeBag, HasRelations;

    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = ['*'];
    protected array $hidden = [];

    // Optional ORM connection point
    protected static ?EntityManager $entityManager = null;

    public function __construct(array $attributes = [])
    {
        $this->bootAttributeBag();
        $this->fill($attributes);
    }

    public static function setEntityManager(EntityManager $entityManager): void
    {
        self::$entityManager = $entityManager;
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }

        return $this;
    }

    public function forceFill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->fillable)) {
            return true;
        }

        if ($this->isGuarded($key)) {
            return false;
        }

        return empty($this->fillable) && !str_starts_with($key, '_');
    }

    protected function isGuarded(string $key): bool
    {
        if (empty($this->guarded)) {
            return false;
        }

        if ($this->guarded === ['*']) {
            return true;
        }

        return in_array($key, $this->guarded);
    }

    public function save(): bool
    {
        if (self::$entityManager) {
            self::$entityManager->persist($this);
            return true;
        }
        
        // Mock save logic for testing without EM
        $this->syncOriginal();
        return true;
    }

    public function delete(): bool
    {
        if (self::$entityManager) {
            self::$entityManager->remove($this);
            return true;
        }
        
        return true;
    }

    public function getKey(): mixed
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function toArray(): array
    {
        $array = [];

        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, $this->hidden)) {
                $array[$key] = $this->hasCast($key) ? $this->castAttribute($key, $value) : $value;
            }
        }
        
        foreach ($this->getRelations() as $key => $value) {
            if ($value instanceof ArrayableInterface) {
                $array[$key] = $value->toArray();
            } elseif ($value instanceof \Palet\Framework\Contracts\Database\Orm\Model\ModelInterface) {
                // If it's a single model and implements Arrayable (which BaseModel does)
                $array[$key] = method_exists($value, 'toArray') ? $value->toArray() : $value;
            } elseif ($value === null) {
                $array[$key] = null;
            }
        }

        return $array;
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
