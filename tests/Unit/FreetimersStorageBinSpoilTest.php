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
    }

}