<?php

namespace App\Repositories\Interfaces;

interface TextbookRepositoryInterface
{
    public function all();
    public function findByTitle($title);
    public function countByAuthor($authorId);
    public function create(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
    public function getPublished();
    public function findWithStudentNotes($textbookId, $studentId);
}
