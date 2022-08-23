<?php

namespace App\Service\Mailer;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\LogicException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

/**
 * class MailerService
 * @package App\Service\Mailer
 */
class MailerService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * Send email
     * @param string $subject 
     * @param string $from 
     * @param string $to 
     * @param string $template 
     * @param array $parameters 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws LogicException 
     * @throws LoaderError 
     * @throws SyntaxError 
     * @throws RuntimeError 
     * @throws TransportExceptionInterface 
     */
    public function send(
        string $subject, 
        string $from,
        string $to,
        string $template,
        array $parameters
    ): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html(
                $this->twig->render($template, $parameters),
                // 'text/html'
            );
        
        $this->mailer->send($email);
    }
}
