<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email,'.$this->route('user')?->id],
        ];

        if ($this->user()?->isAdmin()) {
            $rules['role'] = ['required', 'string', 'in:'.implode(',', array_column(Role::cases(), 'value'))];
        }

        return $rules;
    }
}
