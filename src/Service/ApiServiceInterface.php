<?php

namespace App\Service;

/**
 * interface ApiServiceInterface
 * @package App\Service
 */
interface ApiServiceInterface
{
    /**
     * Validate input
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInput(?array $params): void;

    /**
     * Validate inputs
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInputs(?array $params): void;

    /**
     * Check if User has role "ROLE_ADMIN"
     * @param string $userId 
     * @return void 
     */
    public function isRoleAdmin(string $userId): void;
}
