<?php

namespace App\Repositories\Interfaces;

interface PurchaseRepositoryInterface
{
    public function create(array $data);
    public function findPurchase($studentId, $textbookId);
    public function getStudentPurchases($studentId);
}
