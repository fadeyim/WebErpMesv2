<?php

namespace App\Services;

use App\Models\Products\StockMove;

class StockService
{
    /**
     * Create a stock movement.
     *
     * This helper centralizes the creation of a StockMove record. The data
     * array should contain the fillable attributes on the StockMove model.
     *
     * @param array $data Key/value pairs to create the stock movement with.
     * @return \App\Models\Products\StockMove
     */
    public function createStockMove(array $data)
    {
        return StockMove::create($data);
    }
}
