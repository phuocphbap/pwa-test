<?php

namespace App\Rules;

use App\Entities\ArticleImages;
use Illuminate\Contracts\Validation\Rule;

class UpdateArticlesRequestRule implements Rule
{
    protected $articleId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = explode(',', $value);
        $images = ArticleImages::where('article_id', $this->articleId)->get();
        $check = true;
        foreach ($value as $key => $val) {
            $check = $images->where('id', $val)->first();
            if (!$check) {
                return $check;
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
        return __('api.invalid_image');
    }
}
