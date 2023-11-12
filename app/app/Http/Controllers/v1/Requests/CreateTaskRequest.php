<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
{
    //TODO move some params-values to env or businees part
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:4|max:250',
            'priority' => 'required|integer|min:1|max:5',
            'status' => ['string', Rule::enum(Status::class)],
            'description' => 'string|max:2000',
        ];
    }
}

