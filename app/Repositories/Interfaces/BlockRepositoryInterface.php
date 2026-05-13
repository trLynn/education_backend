<?php

namespace App\Repositories\Interfaces;

interface BlockRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function updateOrder(array $orders);
}
