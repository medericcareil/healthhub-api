<?php

namespace App\Controller\User;

use App\Service\User\UserService;
use App\Repository\UserRepository;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Failure\User\UserInvalidCredentialFailure;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class DeleteUserController
 * @package App\Controller\User
 * 
 * @Route("/api/user")
 */
class DeleteUserController extends AbstractApiController
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(
        UserService $userService,
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     * 
     * @Route("/delete-user", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);
    
        try {
            $this->userService->validateInput($paramsIn);
            $user = $this->userService->isUser($paramsIn['user_id']);

            $this->userRepository->remove($user);

            return $this->success(['message' => '' . $user->getPseudo() . ', votre compte a été supprimé avec succès']);
        } catch (\Throwable $th) {
            return $this->failure(new UserInvalidCredentialFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la suppression de l\'utilisateur' : $th->getMessage()));
        }
    }
}
