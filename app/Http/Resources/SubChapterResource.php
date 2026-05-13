<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chapter_id' => $this->chapter_id,
            'title' => $this->title,
            'order_index' => $this->order_index,
            // ၎င်းအောက်ရှိ Blocks များပါလာခဲ့လျှင် ပြသပေးမည်
            'blocks' => BlockResource::collection($this->whenLoaded('blocks')), 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}