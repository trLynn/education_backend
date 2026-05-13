<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sub_chapter_id' => $this->sub_chapter_id,
            'type' => $this->type,
            'content' => $this->content,
            'order_index' => $this->order_index,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}