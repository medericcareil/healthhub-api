<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Failure\User\UserFailure;
use App\Service\User\UserService;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * class UpdateUserController
 * @package App\Controller\User
 *
 * @Route("/api/user")
 */
class UpdateUserController extends AbstractApiController
{
    /**
     * @var UserService
     */
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/update-user", name="user_update", methods={"POST"})
     */
    public function update(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->userService->validateInputs($paramsIn);
            $user = $this->userService->update($paramsIn);

            return $this->success(User::toArray($user));
        } catch (\Throwable $th) {
            return $this->failure(new UserFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la mise à jour de l\'utilisateur' : $th->getMessage()));
        }
    }
}
