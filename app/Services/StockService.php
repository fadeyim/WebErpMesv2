<?php

namespace App\Services;

use App\Models\Products\StockMove;

class StockService
{
    /**
     * Create a stock movement record.
     *
     * @param array $data Key-value pairs for StockMove attributes.
     * @return \App\Models\Products\StockMove
     */
    public function createStockMove(array $data)
    {
        return StockMove::create($data);
    }
}
