<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Uid\UuidV4;
use App\Service\ApiServiceInterface;
use App\Kernel\Exception\UserException;
use App\Kernel\Exception\InputException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * abstract class AbstractApiService
 * @package App\Service
 */
abstract class AbstractApiService implements ApiServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    protected UrlGeneratorInterface $router;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router
    )
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * Validate input
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInput(?array $params): void
    {
        if (empty($params)) {
            throw new InputException('Requête malformée');
        }
        if (!isset($params['user_id'])) {
            throw new InputException('Clé \'user_id\' requise');
        }
        if (!UuidV4::isValid($params['user_id'])) {
            throw new InputException('Uid non valide');
        }
    }

    /**
     * Validate inputs
     * @param null|array $params 
     * @return void 
     * @throws InputException 
     */
    public function validateInputs(?array $params): void {}

    /**
     * Check if User exist
     * @param string $userId 
     * @return User 
     * @throws UserException 
     */
    public function isUser(string $userId): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => UuidV4::fromString($userId)]);
        if (!$user) {
            throw new UserException('L\'utilisateur n\'existe pas'); 
        }
        return $user;
    }

    /**
     * Check if User has role "ROLE_ADMIN"
     * @param string $userId 
     * @return void 
     * @throws UserException 
     */
    public function isRoleAdmin(string $userId): void 
    {
        $user = $this->isUser($userId);
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new UserException('L\'utilisateur n\'a pas les droits d\'accès'); 
        }
    }
}
