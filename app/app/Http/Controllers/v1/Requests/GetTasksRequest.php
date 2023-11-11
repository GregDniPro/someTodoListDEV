<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetTasksRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filters.status' => [Rule::enum(Status::class)],
            'filters.priority' => 'integer|min:1|max:5', //TODO move to env or businees part
            'filters.search' => 'string|min:4|max:150', //TODO move to env or businees part todo exclude
            'sort.created_at' => ['string', Rule::in(['asc', 'desc'])],
            'sort.completed_at' => ['string', Rule::in(['asc', 'desc'])],
            'sort.priority' => ['string', Rule::in(['asc', 'desc'])],
        ];
    }
}
