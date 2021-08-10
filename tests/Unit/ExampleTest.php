<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \app\Models;

class FreetimersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_starage_bin_exists()
    {
        $bin = new \App\Models\StorageBin();
        $this->assertIsObject($bin);
    }
}
