<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Question.
 */
class AnswerQuestion extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'answer_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_id',
        'order',
        'question',
        'answer',
        'state',
    ];

    public function topic()
    {
        return $this->belongsTo(Question::class);
    }
}
