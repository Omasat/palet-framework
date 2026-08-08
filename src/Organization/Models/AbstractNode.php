<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Models;

use Palet\Framework\Organization\Exceptions\CircularHierarchyException;

abstract class AbstractNode implements NodeInterface
{
    protected ?NodeInterface $parent = null;
    protected array $children = [];

    public function __construct(
        protected string|int $id,
        protected string $name
    ) {}

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParent(): ?NodeInterface
    {
        return $this->parent;
    }

    public function setParent(?NodeInterface $parent): void
    {
        if ($parent !== null && $this->createsCircularDependency($parent)) {
            throw new CircularHierarchyException("Circular dependency detected when setting parent.");
        }
        $this->parent = $parent;
    }

    public function addChild(NodeInterface $child): void
    {
        $child->setParent($this);
        $this->children[$child->getId()] = $child;
    }

    public function removeChild(NodeInterface $child): void
    {
        if (isset($this->children[$child->getId()])) {
            $child->setParent(null);
            unset($this->children[$child->getId()]);
        }
    }

    public function getChildren(): array
    {
        return array_values($this->children);
    }

    protected function createsCircularDependency(NodeInterface $newParent): bool
    {
        $current = $newParent;
        while ($current !== null) {
            if ($current->getId() === $this->getId() && $current->getType() === $this->getType()) {
                return true;
            }
            $current = $current->getParent();
        }
        return false;
    }
}
