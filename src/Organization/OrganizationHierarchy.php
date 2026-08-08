<?php

declare(strict_types=1);

namespace Palet\Framework\Organization;

use Palet\Framework\Organization\Models\NodeInterface;

class OrganizationHierarchy
{
    /**
     * Recursively find all descendants of a given node.
     *
     * @param NodeInterface $node
     * @return array<NodeInterface>
     */
    public function getDescendants(NodeInterface $node): array
    {
        $descendants = [];
        foreach ($node->getChildren() as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->getDescendants($child));
        }
        return $descendants;
    }

    /**
     * Find the path from the root to the given node.
     *
     * @param NodeInterface $node
     * @return array<NodeInterface>
     */
    public function getPath(NodeInterface $node): array
    {
        $path = [];
        $current = $node;
        while ($current !== null) {
            array_unshift($path, $current); // Prepend so root is first
            $current = $current->getParent();
        }
        return $path;
    }
}
