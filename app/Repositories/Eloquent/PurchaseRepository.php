<?php
namespace App\Repositories\Eloquent;

use App\Models\Purchase;
use App\Repositories\Interfaces\PurchaseRepositoryInterface;

class PurchaseRepository implements PurchaseRepositoryInterface {
    public function create(array $data) {
        return Purchase::create($data);
    }

    public function findPurchase($studentId, $textbookId) {
        return Purchase::where('student_id', $studentId)
                       ->where('textbook_id', $textbookId)
                       ->first();
    }

    public function getStudentPurchases($studentId) {
        return Purchase::with('textbook')->where('student_id', $studentId)->get();
    }
}