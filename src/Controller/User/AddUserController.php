<?php

namespace App\Controller\User;

use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use App\Controller\AbstractApiController;
use App\Service\User\RegistrationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class AddUserController
 * @package App\Controller\User
 */
class AddUserController extends AbstractApiController
{
    /**
     * @var RegistrationService
     */
    private RegistrationService $registrationService;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(
        RegistrationService $registrationService,
        UserRepository $userRepository
    )
    {
        $this->registrationService = $registrationService;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response
     * 
     * @Route("/inscription", name="register", methods={"GET", "POST"})
     */
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user) {
                $form->get('email')->addError(new FormError('Email déjà utilisé'));
            } else {
                $this->registrationService->process($form);
            
                $this->addFlash('success', 'Félicitations ' . $form->get('pseudo')->getData() . ' ! Un email de confirmation vous a été envoyé, afin de valider votre compte.');
    
                return $this->redirectToRoute('success');
            }
        }

        return $this->render('front/registration/registration.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return Response
     * 
     * @Route("/confirmation-d-inscription/{token}", name="register_confirm", methods={"GET", "POST"})
     */
    public function confirmRegister(string $token): Response
    {
        $user = $this->registrationService->confirmAccount($token);

        $this->addFlash('success', 'Félicitations ' . $user->getPseudo() . ' ! Votre compte a été validé avec succès.');

        return $this->redirectToRoute('success');
    }
}
