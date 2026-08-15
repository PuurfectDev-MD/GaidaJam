<?php

namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class HackClubProvider extends AbstractProvider
{
    protected $scopes = [];
    protected $scopeSeparator = ' ';

    public function __construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);

        $configuredScopes = (string) config('services.hackclub.scopes', 'openid email name profile slack_id');

        $this->scopes = collect(preg_split('/[\s,]+/', $configuredScopes) ?: [])
            ->filter(fn ($scope) => $scope !== '')
            ->values()
            ->all();
    }

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.hackclub.base_url') . '/oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return config('services.hackclub.base_url') . '/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(config('services.hackclub.base_url') . '/api/v1/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        if (isset($user['identity'])) {
            $identity = $user['identity'];
            return (new User)->setRaw($user)->map([
                'id' => $identity['id'] ?? null,
                'name' => trim(($identity['first_name'] ?? '') . ' ' . ($identity['last_name'] ?? '')),
                'email' => $identity['primary_email'] ?? null,
                'avatar' => null,
                'slack_id' => $identity['slack_id'] ?? null,
            ]);
        }

        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? null,
            'slack_id' => $user['slack_id'] ?? null,
        ]);
    }
}
