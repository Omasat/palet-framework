<?php

declare(strict_types=1);

namespace Palet\Framework\Database\Orm\Model\Traits;

trait SoftDeletes
{
    protected bool $forceDeleting = false;

    public function delete(): bool
    {
        if ($this->forceDeleting) {
            $this->performForceDelete();
            return true;
        }

        $this->performSoftDelete();
        return true;
    }

    public function forceDelete(): bool
    {
        $this->forceDeleting = true;
        $this->delete();
        $this->forceDeleting = false;
        return true;
    }

    public function restore(): bool
    {
        $this->setAttribute('deleted_at', null);
        // Mock save
        if (method_exists($this, 'fireModelEvent')) {
            $this->fireModelEvent('restored');
        }
        return true;
    }

    protected function performSoftDelete(): void
    {
        $this->setAttribute('deleted_at', date('Y-m-d H:i:s'));
        
        if (method_exists($this, 'fireModelEvent')) {
            $this->fireModelEvent('deleted');
        }
    }

    protected function performForceDelete(): void
    {
        // Actually delete from database...
        
        if (method_exists($this, 'fireModelEvent')) {
            $this->fireModelEvent('forceDeleted');
        }
    }
    
    public function trashed(): bool
    {
        return $this->getAttribute('deleted_at') !== null;
    }
}
