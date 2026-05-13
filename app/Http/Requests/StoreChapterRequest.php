<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest {
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'textbook_id' => 'required|exists:textbooks,id',
            'title' => 'required|string|max:255',
            'order_index' => 'nullable|integer'
        ];
    }
}