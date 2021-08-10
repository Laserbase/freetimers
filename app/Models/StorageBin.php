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

class StorageException extends \Exception { }
class StorageBin extends Model
{
    use HasFactory;
    private string $product_id = "";
    private int $level = 0;
    private $control = [];
    /**
     *     private int $quantity = 0; 
     *     private decimal $cost = 0.00;
     *     private $date_purchased;
     */

    public function __construct(string $product_id, int $quantity, float $cost, string $date_purchased)
    {
        if ($this->level === 0) {
            $this->product_id = $product_id;
            $this->level = 0;
            $this->control = [];
        }

        $this->add($product_id, $quantity, $cost, $date_purchased);
    }
    public function add(string $product_id, int $quantity, float $cost, string $date_purchased)
    {
        $this->check_quantity($quantity);

        if ($this->product_id !== $product_id) {
            throw new StorageException("This storage bin is for '$this->product_id', unable to add '{$product_id}'");
        }

        $this->level += $quantity;
        $control[] = ["quantity" => $quantity, "cost" => $cost, "date" => $date_purchased];
    }
    private function check_quantity(int $quantity) 
    {
        if ($quantity < 1) {
            throw new StorageException("Unable to add quantity '{$quantity}' as it is less than 1 (one)");
        }

        return $this;
    }
}
