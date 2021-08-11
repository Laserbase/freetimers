<?php
declare(strict_types = 1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;
use \app\Models\StarageBin;

class FreetimersStorageBinSpoilTest extends TestCase
{
    public function test_starage_bin_spoil()
    {   
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->spoil();

        $this->assertIsObject($bin);
        $status = $bin->getStatus();
        $this->assertTrue($status['status'] === 'spoiled');
        $this->assertTrue($status['level'] === 10);
    }
    public function test_starage_bin_add_to_spoiled_bin()
    {
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->spoil();

        $this->expectException(\app\Models\StorageException::class);
        $bin->add("bolts", 10, 10.99, date("2021-01-01"));
    }
    public function test_starage_bin_remove_from_spoiled_bin()
    {
        $bin = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bin->spoil();

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bin->remove("bolts", 1, 10.99, date("2021-02-02"));
    }
    public function test_starage_bin_move_from_spoiled_bin()
    {
        $bolts1 = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bolts1->spoil();

        $bolts2 = new \App\Models\StorageBin("bolts", 1, 1.00, date("2021-01-01"));
        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["overflow"] === 0);
        $this->assertTrue($status2["level"] === 1);

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bolts1->move($bolts2, 0);
    }
    public function test_starage_bin_move_to_spoiled_bin()
    {
        $bolts1 = new \App\Models\StorageBin("bolts", 10, 10.00, date("2021-01-01"));
        $bolts2 = new \App\Models\StorageBin("bolts", 10, 1.00, date("2021-01-01"));
        $bolts2->spoil();

        $status2 = $bolts2->getStatus();
        $this->assertTrue($status2["status"] === 'spoiled');
        $this->assertTrue($status2["overflow"] === 0);
        $this->assertTrue($status2["level"] === 10);

        $this->expectException(\app\Models\StorageException::class);
        $overflow = $bolts1->move($bolts2, 1);
    }

}