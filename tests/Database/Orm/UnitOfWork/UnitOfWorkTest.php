<?php

declare(strict_types=1);

namespace Tests\Database\Orm\UnitOfWork;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\UnitOfWork\UnitOfWork;
use Palet\Framework\Contracts\Database\Orm\UnitOfWork\FlushManagerInterface;
use stdClass;

class MockFlushManager implements FlushManagerInterface
{
    public array $flushedNew = [];
    public array $flushedDirty = [];
    public array $flushedRemoved = [];

    public function flush(array $newObjects, array $dirtyObjects, array $removedObjects): void
    {
        $this->flushedNew = $newObjects;
        $this->flushedDirty = $dirtyObjects;
        $this->flushedRemoved = $removedObjects;
    }
}

class UnitOfWorkTest extends TestCase
{
    public function test_registers_objects_in_correct_pools()
    {
        $flushManager = new MockFlushManager();
        $uow = new UnitOfWork($flushManager);
        
        $newObj = new stdClass();
        $dirtyObj = new stdClass();
        $removedObj = new stdClass();
        
        $uow->registerNew($newObj);
        $uow->registerDirty($dirtyObj);
        $uow->registerRemoved($removedObj);
        
        $uow->commit();
        
        $this->assertCount(1, $flushManager->flushedNew);
        $this->assertSame($newObj, $flushManager->flushedNew[0]);
        
        $this->assertCount(1, $flushManager->flushedDirty);
        $this->assertSame($dirtyObj, $flushManager->flushedDirty[0]);
        
        $this->assertCount(1, $flushManager->flushedRemoved);
        $this->assertSame($removedObj, $flushManager->flushedRemoved[0]);
    }

    public function test_registering_new_then_removed_cancels_insert()
    {
        $flushManager = new MockFlushManager();
        $uow = new UnitOfWork($flushManager);
        
        $obj = new stdClass();
        
        $uow->registerNew($obj);
        $uow->registerRemoved($obj); // Should detach from new, skip remove
        
        $uow->commit();
        
        $this->assertEmpty($flushManager->flushedNew);
        $this->assertEmpty($flushManager->flushedRemoved);
    }
}
