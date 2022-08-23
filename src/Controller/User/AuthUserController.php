<?php

namespace App\Controller\User;

use App\Controller\AbstractApiController;
use App\Entity\User;
use App\Failure\User\UserInvalidCredentialFailure;
use App\Service\User\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class AuthUserController
 * @package App\Controller\User
 *
 * @Route("/api/user")
 */
class AuthUserController extends AbstractApiController
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/auth", name="user_auth", methods={"POST"})
     */
    public function auth(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->authService->validateInputs($paramsIn);
            $user = $this->authService->isValid($paramsIn);

            return $this->success(User::toArray($user));
        } catch (\Throwable $th) {
            return $this->failure(new UserInvalidCredentialFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'authentification' : $th->getMessage()));
        }
    }
}
