<?php

declare(strict_types=1);

namespace Tests\Session;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Session\Store;
use Palet\Framework\Session\Drivers\ArraySessionDriver;

class StoreTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        $this->store = new Store('test_session', new ArraySessionDriver(10));
        $this->store->start();
    }

    public function test_put_and_get()
    {
        $this->store->put('name', 'Tayfun');
        $this->assertEquals('Tayfun', $this->store->get('name'));
    }

    public function test_has_and_forget()
    {
        $this->store->put('age', 25);
        $this->assertTrue($this->store->has('age'));
        
        $this->store->forget('age');
        $this->assertFalse($this->store->has('age'));
        $this->assertNull($this->store->get('age'));
    }

    public function test_flash_data_ages_properly()
    {
        $this->store->flash('status', 'success');
        $this->assertEquals('success', $this->store->get('status'));
        
        // Simulating the end of request
        $this->store->save();
        
        // Simulating the next request
        $this->store->start();
        $this->assertEquals('success', $this->store->get('status'));
        
        // Simulating the end of the next request (should age flash data to be deleted)
        $this->store->save();
        
        // Simulating the third request
        $this->store->start();
        $this->assertNull($this->store->get('status')); // Should be gone now
    }

    public function test_regenerate_session_id()
    {
        $oldId = $this->store->getId();
        $this->store->regenerate();
        $newId = $this->store->getId();
        
        $this->assertNotEquals($oldId, $newId);
    }
}
