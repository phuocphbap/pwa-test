<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Question.
 */
class TopicQuestion extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'topic_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    public function answers()
    {
        return $this->hasMany(AnswerQuestion::class, 'topic_id');
    }
}
