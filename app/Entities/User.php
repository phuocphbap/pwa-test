<?php

namespace App\Entities;

use Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class User.
 */
class User extends Authenticatable implements Transformable
{
    use TransformableTrait;
    use HasApiTokens;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'user_name',
        'password',
        'avatar',
        'first_name',
        'last_name',
        'gender',
        'detail',
        'address_id',
        'address',
        'phone',
        'phone_verify',
        'is_two_fa',
        'phone_verify_token',
        'is_phone_verified',
        'is_email_verified',
        'is_kyc_profiled',
        'identity_status',
        'birth_date',
        'referral_code',
        'input_refferal_code',
        'ranking',
        'stripe_customer_id',
        'reason_leave',
        'email_verified_at',
        'state',
        'switch_notices_progress',
        'latitude',
        'longitude',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    const USER_INACTIVE = 0;
    const USER_ACTIVE = 1;
    const USER_BLACKLIST = 2;
    const USER_LEAVE_GROUP = 3;

    const IDENTITY_PENDING_STATUS = 'PENDING';
    const IDENTITY_PROCESS_STATUS = 'PROCESS';
    const IDENTITY_ACCEPT_STATUS = 'ACCEPTED';
    const IDENTITY_REJECT_STATUS = 'REJECTED';
    const KYC_PROFILE_UPDATED = 1;

    //call mutator to auto hash password;
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }

    public function skills()
    {
        return $this->belongstoMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    public function roles()
    {
        return $this->belongstoMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function agreements()
    {
        return $this->hasMany(RequestConsulting::class, 'service_id')->where('progress', RequestConsulting::PROGRESS_DONE);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class);
    }

    public function identityCard()
    {
        return $this->hasOne(IdentityCard::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->wallet()->create();
        });
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'address_id');
    }

    public function withDraws()
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function bonus()
    {
        return $this->hasMany(Bonus::class);
    }

    public function reduntPoint()
    {
        return $this->wallet()->with(['walletExpires' => function ($query) {
            $query->where('expire_date', '>=', \Carbon\Carbon::now())->orderBy('id', 'desc')->first();
        }]);
    }
}
