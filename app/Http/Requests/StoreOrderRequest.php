<?php

namespace App\Http\Requests;

use App\Models\Food;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('food_id') || $validator->errors()->has('quantity')) {
                    return;
                }

                $food = Food::query()
                    ->select(['id', 'stock', 'is_available'])
                    ->find($this->integer('food_id'));

                if (! $food) {
                    return;
                }

                if (! $food->is_available || $food->stock < 1) {
                    $validator->errors()->add('quantity', 'Món ăn hiện không khả dụng.');

                    return;
                }

                if ($this->integer('quantity') > $food->stock) {
                    $validator->errors()->add('quantity', "Bạn chỉ có thể đặt tối đa {$food->stock} sản phẩm.");
                }
            },
        ];
    }
}
