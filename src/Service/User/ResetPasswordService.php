<?php

namespace App\Service\User;

use App\Entity\User;
use App\Kernel\Exception\UserException;
use App\Service\AbstractApiService;
use App\Service\Mailer\MailerService;
use App\Service\Security\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * class ResetPasswordService
 * @package App\Service\User
 */
class ResetPasswordService extends AbstractApiService
{
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
        UserPasswordHasherInterface $encoder,
        TokenService $tokenService,
        MailerService $mailerService,
        string $mailer_from
    )
    {
        parent::__construct($em, $router);
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
     * Check if the token is valid and not expired
     * @param string $token 
     * @return User 
     * @throws UserException 
     */
    public function checkToken(string $token): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            throw new UserException('Aucun lien ne correspond');
        }
        $this->tokenService->isValidDateTimeToken($token);

        return $user;
    }

    /**
     * Check if new password is a same
     * @param object $form 
     * @param User $user 
     * @return bool 
     */
    public function isSamePassword(object $form, User $user): bool
    {
        return $this->encoder->isPasswordValid($user, $form->get('password')->getData());
    }

    /**
     * @param object $form 
     * @param User $user 
     * @return void 
     */
    public function confirmNewPassword(object $form, User $user): void
    {
        $user->setPassword($this->encoder->hashPassword($user, $form->get('password')->getData()));
        $user->setToken(null);
        $this->persist($user);
    }

    /**
     * Send link to reset password
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
    private function sendLink(User $user): void
    {
        $url = $this->router->generate('new_password', ['token' => $user->getToken()], 0);
        $cancel_url = $this->router->generate('cancel_reset', ['token' => $user->getToken()], 0);

        $this->mailerService->send(
            'Step - Mot de passe oublié',
            $this->getMailerFrom(),
            $user->getEmail(),
            'front/email/reset.html.twig',
            [
                'pseudo'     => $user->getPseudo(),
                'url'        => $url,
                'cancel_url' => $cancel_url,
                'mail_from'  => $this->getMailerFrom()
            ]
        );
    }

    /**
     * @param User $user 
     * @return void 
     */
    public function process(User $user): void
    {
        $user->setToken($this->tokenService->generateToken());

        $this->persist($user);

        $this->sendLink($user);
    }

    /**
     * Cancel reset password
     * @param User $user 
     * @return void 
     */
    public function cancel(User $user): void
    {
        $user->setToken(null);
        $this->persist($user);
    }
}
