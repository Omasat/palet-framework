<?php

declare(strict_types=1);

namespace Palet\Framework\Organization\Models;

interface NodeInterface
{
    public function getId(): string|int;
    public function getName(): string;
    public function getType(): string;
    public function getParent(): ?NodeInterface;
    public function setParent(?NodeInterface $parent): void;
    public function addChild(NodeInterface $child): void;
    public function removeChild(NodeInterface $child): void;
    
    /**
     * @return array<NodeInterface>
     */
    public function getChildren(): array;
}
