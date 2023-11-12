<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTasksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            //TODO move some params-values to env or businees part
            'filters.status' => ['string', Rule::enum(Status::class)],
            'filters.priority' => 'integer|min:1|max:5',
            'filters.search' => 'string|min:4|max:150',
            'sort.created_at' => ['string', Rule::in(['asc', 'desc'])],
            'sort.completed_at' => ['string', Rule::in(['asc', 'desc'])],
            'sort.priority' => ['string', Rule::in(['asc', 'desc'])],
        ];
    }
}
