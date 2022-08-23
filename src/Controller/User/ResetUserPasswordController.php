<?php

namespace App\Controller\User;

use App\Form\ResetType;
use App\Form\NewPasswordType;
use Symfony\Component\Form\FormError;
use App\Controller\AbstractApiController;
use App\Repository\UserRepository;
use App\Service\User\ResetPasswordService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class ResetUserPasswordController
 * @package App\Controller\User
 */
class ResetUserPasswordController extends AbstractApiController
{
    /**
     * @var ResetPasswordService
     */
    private ResetPasswordService $resetPasswordService;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(
        ResetPasswordService $resetPasswordService,
        UserRepository $userRepository
    )
    {
        $this->resetPasswordService = $resetPasswordService;
        $this->userRepository = $userRepository;
    }

    /**
     * @return Response 
     * 
     * @Route("/mot-de-passe-oublie", name="reset", methods={"GET", "POST"})
     */
    public function reset(Request $request): Response
    {
        $form = $this->createForm(ResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['email' => $form->get('email')->getData()]);
            if (!$user) {
                $form->get('email')->addError(new FormError('Une erreur est survenue'));
            } else {
                $this->resetPasswordService->process($user);
    
                $this->addFlash('success', 'Un lien pour changer votre mot de passe a été envoyé par email.');
    
                return $this->redirectToRoute('success');
            }
        }

        return $this->render('front/reset/reset.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return Response 
     * 
     * @Route("/nouveau-mot-de-passe/{token}", name="new_password", methods={"GET", "POST"})
     */
    public function newPassword(Request $request, string $token): Response
    {
        $user = $this->resetPasswordService->checkToken($token);

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->resetPasswordService->isSamePassword($form, $user)) {
                $form->get('password')->addError(new FormError('Veuillez changer pour un nouveau mot de passe'));
            } else {
                $this->resetPasswordService->confirmNewPassword($form, $user);
    
                $this->addFlash('success', 'Votre mot de passe est à été mis à jour, vous pouvez vous connecter.');
    
                return $this->redirectToRoute('success');
            }
        }

        return $this->render('front/reset/new_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return Response 
     * 
     * @Route("/cancel-reset/{token}", name="cancel_reset", methods={"GET", "POST"})
     */
    public function cancelReset(string $token): Response
    {
        $user = $this->resetPasswordService->checkToken($token);

        $this->resetPasswordService->cancel($user);

        $this->addFlash('success', 'Merci pour votre honnêteté.');

        return $this->redirectToRoute('success');
    }
}
