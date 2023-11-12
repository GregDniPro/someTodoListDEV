<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|min:4|max:250',
            'priority' => 'integer|min:1|max:5',
            'status' => ['string', Rule::enum(Status::class)],
            'description' => 'string|max:2000',
        ];
    }
}

