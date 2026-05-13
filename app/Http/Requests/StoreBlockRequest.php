<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sub_chapter_id' => 'required|exists:sub_chapters,id',
            // သတ်မှတ်ထားသော Type ၅ မျိုးကိုသာ လက်ခံမည်
            'type' => 'required|string|in:textbox,equation,html,image_link,video_link',
            // Content သည် Type အလိုက် URL သို့မဟုတ် Text ဖြစ်နိုင်သဖြင့် string ဖြစ်ရမည်
            'content' => 'nullable|string',
            'order_index' => 'nullable|integer'
        ];
    }

    // Error Message များကို မြန်မာလို ပြချင်ပါက (Optional)
    public function messages(): array
    {
        return [
            'type.in' => 'Block အမျိုးအစား မှားယွင်းနေပါသည်။ (textbox, equation, html, image_link, video_link သာ လက်ခံပါသည်)',
        ];
    }
}