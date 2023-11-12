<?php

declare(strict_types=1);

namespace App\Http\Controllers\Requests\v1\Tasks;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class
UpdateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|min:4|max:250',
            'priority' => 'integer|min:1|max:5',
            'description' => 'string|max:2000',
            'status' => ['string', Rule::enum(Status::class)],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('tasks', 'id')->where(function ($query) {
                    $query->whereNull('parent_id');
                }),
            ],
        ];
    }
}

