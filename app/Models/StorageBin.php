<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Requirements: Create a PHP object that will act as a Bin in a warehouse.
 * Your bin should have (but not be resticted to) the following methods:
 * A method to add stock to the bin
 * A method to remove stock from the bin
 * A method to move stock from the bin to another bin
 * A method to mark stock in the bin as spoiled
 * A method to calculate the value of the stock in the bin using the following methods
 *      Last in first out
 *      First in first out
 *      Average stock price
 */
class StorageBin extends Model
{
    use HasFactory;
    public function __construct()
    {
        $this->add();
    }
    public function add()
    {
        throw new \Exception("Not Implemented");
    }
}
