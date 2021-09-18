<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use App\Entities\Category;

class CreateCategoryRequestRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value != 0) {
            $check = Category::where('id', $value)->first();
            if (!$check) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('api.category.parent_id_not_exists');
    }
}
