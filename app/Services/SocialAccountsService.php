<?php
namespace App\Services;

use App\User;
use Laravel\Socialite\Two\User as ProviderUser;

class SocialAccountsService
{
    /**
     * Find or create user instance by provider user instance and provider name.
     * 
     * @param ProviderUser $providerUser
     * @param string $provider
     * 
     * @return User
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): User
    {
        
        $socialAccount = \App\SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUser->getId())
            ->first();
        if ($socialAccount) {
            return $socialAccount->user;
        } else {
            $user = null;
            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            }
            if (! $user) {
                $user = User::create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                ]);
            }
            $user->socialAccount()->create([
                'user_id' => $user->id,
                'provider_user_id' => $providerUser->getId(),
                'provider' => $provider,
            ]);
            return $user;
        }
    }
}