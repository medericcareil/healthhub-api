<?php

namespace App\Service\Security;

use App\Service\Security\Security;

/**
 * class TokenService
 * @package App\Service\Security
 */
class TokenService extends Security
{
    /**
     * Generate token with immutable datetime
     * @return string 
     */
    public function generateToken(): string
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $randomString = '';

        for ($k = 0; $k < rand(8, 14); $k++) { 
           $randomString .= $chars[ rand( 0, strlen($chars) - 1 ) ];
        }

        $data = [
            'datetime' => new \DateTimeImmutable(),
            'key'      => $randomString
        ];

        return base64_encode(openssl_encrypt(serialize($data), self::CIPHER, self::KEY, 0, self::IV));
    }

    /**
     * Decypte token
     * @param string $token 
     * @return array 
     */
    public function decryptToken(string $token): array
    {
        return unserialize(openssl_decrypt(base64_decode($token), self::CIPHER, self::KEY, 0, self::IV));
    }

    /**
     * Check if the token is valid and not expired
     * @param string $token 
     * @return void 
     * @throws Exception 
     */
    public function isValidDateTimeToken(string $token): void
    {
        $decryptedToken = $this->decryptToken($token);
 
        if (!is_array($decryptedToken) || !array_key_exists('datetime', $decryptedToken) || !$decryptedToken['datetime'] instanceof \DateTimeImmutable) {
            throw new \Exception('Le token n\'est pas valide'); 
        }
        $tokenExpiration = $decryptedToken['datetime']->add(new \DateInterval('PT45M'));
        if ((new \DateTimeImmutable('now')) > $tokenExpiration) {
            throw new \Exception('Le lien est expiré');
        }
    }
}
