<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TextbookResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => (float) $this->price,
            'author_name' => $this->author->name,
            'is_published' => (bool) $this->is_published,
            'content' => $this->whenLoaded('chapters'), // Chapter တွေရှိမှ ထည့်ပေးမည်
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}