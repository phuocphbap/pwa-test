<?php

namespace App\Http\Controllers\Api;

use App\Repositories\UserRepository;
use Illuminate\Support\Str;
use Socialite;

class SocialAuthController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * UsersController constructor.
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a redirect method.
     *
     * @return void
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Return a callback method from provider api.
     *
     * @return token
     */
    public function callback($provider)
    {
        $socialInfo = Socialite::driver($provider)->stateless()->user();
        $user = $this->createUserSocial($socialInfo, $provider);
        auth()->login($user);
        $token = auth()->user()->createToken(auth()->user()->id)->accessToken;

        return response()->json([
            'status' => true,
            'token' => $token,
            'user' => auth()->user(),
        ]);
    }

    public function createUserSocial($socialInfo, $provider)
    {
        // Check if use is not exists, then create new
        $user = $this->repository->getUserBySocialId($provider, $socialInfo->id);

        if (!$user) {
            //check email has exist account
            if ($socialInfo->email) {
                $user = $this->repository->getUserByEmail($socialInfo->email);
                if ($user) {
                    $user->update(['state' => 1]);
                } else {
                    $user = $this->repository->registerUser([
                        'email' => $socialInfo->email,
                        'user_name' => isset($socialInfo->name) ? $socialInfo->name : $socialInfo->nickname,
                        'password' => Str::random(12),
                        'avatar' => $socialInfo->avatar,
                        'is_email_verified' => 1,
                        'state' => 1,
                    ]);
                }
            } else {
                $user = $this->repository->registerUser([
                    'user_name' => isset($socialInfo->name) ? $socialInfo->name : $socialInfo->nickname,
                    'password' => Str::random(12),
                    'avatar' => $socialInfo->avatar,
                    'state' => 1,
                ]);
            }

            // Create user social
            $this->repository->createSocial([
                'user_id' => $user->id,
                'provider' => $provider,
                'social_id' => $socialInfo->id,
            ]);
        }

        return $user;
    }
}
