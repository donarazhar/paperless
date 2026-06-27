<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class PresensiProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Get the authentication URL for the provider.
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(env('PRESENSI_URL', 'https://presensigps.masjidagungalazhar.com') . '/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     */
    protected function getTokenUrl()
    {
        return env('PRESENSI_URL', 'https://presensigps.masjidagungalazhar.com') . '/oauth/token';
    }

    /**
     * Get the raw user for the given access token.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(env('PRESENSI_URL', 'https://presensigps.masjidagungalazhar.com') . '/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['nik_karyawan'] ?? null,
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => null,
            // Kita juga menyimpan data relasi cabang & organ yang dilempar dari Master Data
            'cabang'   => $user['cabang'] ?? null,
            'organ'    => $user['organ'] ?? null,
        ]);
    }
}
