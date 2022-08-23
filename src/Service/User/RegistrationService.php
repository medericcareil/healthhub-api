<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\AbstractApiService;
use App\Service\Upload\UploadService;
use App\Service\Security\TokenService;
use App\Kernel\Exception\UserException;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

/**
 * class RegistrationService
 * @package App\Service\User
 */
class RegistrationService extends AbstractApiService
{
    /**
     * @var UploadService
     */
    private UploadService $uploadService;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $encoder;

    /**
     * @var TokenService
     */
    private TokenService $tokenService;

    /**
     * @var MailerService
     */
    private MailerService $mailerService;

    /**
     * @var string
     */
    private string $mailer_from;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $router,
        UploadService $uploadService, 
        UserPasswordHasherInterface $encoder,
        TokenService $tokenService,
        MailerService $mailerService,
        string $mailer_from
    )
    {
        parent::__construct($em, $router);
        $this->uploadService = $uploadService;
        $this->encoder = $encoder;
        $this->tokenService = $tokenService;
        $this->mailerService = $mailerService;
        $this->mailer_from = $mailer_from;
    }

    /**
     * @return string 
     */
    public function getMailerFrom(): string
    {
        return $this->mailer_from;
    }

    /**
     * Persists a User in the database
     * @param User $user 
     * @return void 
     */
    private function persist(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Send email for confirm account
     * @param User $user 
     * @return void 
     * @throws RouteNotFoundException 
     * @throws MissingMandatoryParametersException 
     * @throws InvalidParameterException 
     * @throws InvalidArgumentException 
     * @throws LogicException 
     * @throws LoaderError 
     * @throws SyntaxError 
     * @throws RuntimeError 
     * @throws TransportExceptionInterface 
     */
    private function sendConfirmation(User $user): void
    {
        $url = $this->router->generate('register_confirm', ['token' => $user->getToken()], 0);

        $this->mailerService->send(
            'Step - Confirmer votre compte',
            $this->getMailerFrom(),
            $user->getEmail(),
            'front/email/confirm.html.twig',
            [
                'pseudo'    => $user->getPseudo(),
                'url'       => $url,
                'mail_from' => $this->getMailerFrom()
            ]
        );
    }

    /**
     * @param object $form 
     * @return void 
     */
    public function process(object $form): void
    {
        $data = $form->getData();

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);
        $user->setPassword($this->encoder->hashPassword($user, $data['password']));
        $user->setPseudo($data['pseudo']);
        $user->setImage($this->uploadService->processFile($form->get('image')->getData()));
        $user->setGender($data['gender']);
        $user->setBirthdate($data['birthdate']);
        $user->setToken($this->tokenService->generateToken());
        
        $this->persist($user);

        $this->sendConfirmation($user);
    }

    /**
     * Fetch User by his token
     * @param string $token 
     * @return User 
     * @throws UserException 
     */
    private function fetchByToken(string $token): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            throw new UserException('L\'utilisateur n\'existe pas'); 
        }
        return $user;
    }

    /**
     * Confirm account
     * @param string $token 
     * @return User 
     * @throws UserException 
     */
    public function confirmAccount(string $token): User
    {
        $user = $this->fetchByToken($token);
        $user->setIsValidated(true);
        $user->setToken(null);
        $this->persist($user);
        return $user;
    }
}
