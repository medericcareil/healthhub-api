<?php

namespace App\Controller\User;

use App\Failure\User\UserFailure;
use App\Entity\UserCharacteristic;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\User\UserCharacteristicService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class AddUserCharacteristicController
 * @package App\Controller\User
 *
 * @Route("/api/user")
 */
class AddUserCharacteristicController extends AbstractApiController
{
    /**
     * @var UserCharacteristicService
     */
    private UserCharacteristicService $userCharacteristicService;

    public function __construct(UserCharacteristicService $userCharacteristicService)
    {
        $this->userCharacteristicService = $userCharacteristicService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/add-characteristics", name="user_add_characteristic", methods={"POST"})
     */
    public function addCharacteristics(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->userCharacteristicService->validateInputs($paramsIn);
            $this->userCharacteristicService->formatInputs($paramsIn);
            $userCharacteristic = $this->userCharacteristicService->persist($paramsIn);
            
            return $this->success(UserCharacteristic::toArray($userCharacteristic));
        } catch (\Throwable $th) {
            return $this->failure(new UserFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout de caractéristiques' : $th->getMessage()));
        }
    }
}
