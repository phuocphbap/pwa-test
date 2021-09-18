<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BankAccount.
 */
class BankAccount extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'account_number',
        'account_owner',
        'bank_name',
        'branch_name',
        'state',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryBankAccount::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
