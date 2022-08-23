<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\AbstractApiService;
use App\Kernel\Exception\InputException;
use Doctrine\ORM\EntityManagerInterface;
use App\Kernel\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * class AuthService
 * @package App\Service\User
 */
class AuthService extends AbstractApiService
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        UserPasswordHasherInterface $encoder
    )
    {
        parent::__construct($em, $router);
        $this->encoder = $encoder;
    }    

    /**
     * Validate inputs
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInputs(?array $params): void
    {
        if (empty($params)) {
            throw new InputException('Requête malformée');
        }
        if (!isset($params['email'])) {
            throw new InputException('Clé \'email\' requise');
        }
        if (!isset($params['password'])) {
            throw new InputException('Clé \'password\' requise');
        }
    }

    /**
     * Check if the user exists and return it
     * @param array $params 
     * @return User 
     * @throws AuthenticationException 
     */
    public function isValid(array $params): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $params['email']]);
        if (!$user) {
            throw new AuthenticationException('L\'email et/ou le mot de passe ne correspondent pas'); 
        }
        if (!$this->encoder->isPasswordValid($user, $params['password'])) {
            throw new AuthenticationException('L\'email et/ou le mot de passe ne correspondent pas');
        }
        if (!$user->getIsValidated()) {
            throw new AuthenticationException('Vous devez valider votre compte');
        }
        
        return $user;
    }
}
