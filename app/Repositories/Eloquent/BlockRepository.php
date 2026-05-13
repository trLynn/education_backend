<?php

namespace App\Repositories\Eloquent;

use App\Models\Block;
use App\Repositories\Interfaces\BlockRepositoryInterface;

class BlockRepository implements BlockRepositoryInterface
{
    public function create(array $data)
    {
        return Block::create($data);
    }

    public function update($id, array $data)
    {
        $record = Block::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return Block::destroy($id);
    }

    public function updateOrder(array $orders)
    {
        foreach ($orders as $order) {
            Block::where('id', $order['id'])->update(['order_index' => $order['order_index']]);
        }
    }
}
