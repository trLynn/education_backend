<?php
namespace App\Repositories\Interfaces;

interface ChapterRepositoryInterface {
    public function all();
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}