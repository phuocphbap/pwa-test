<?php

namespace App\Rules\Admin;

use App\Traits\ApiTrait;
use App\Entities\Category;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateCategoryRequestRule implements Rule
{
    use ApiTrait;

    protected $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        if ($value == $this->id) {
            $result = response()->json(['errors' => true, 'messages' => __('api.category.prarent_id_not_same_id')]);
            throw new HttpResponseException($result);
        }
        if ($value != 0) {
            $check = Category::where('id', $value)->first();
            if (!$check) {
                $result = response()->json(['errors' => true, 'messages' => __('api.category.parent_id_not_exists')]);
                throw new HttpResponseException($result);
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
