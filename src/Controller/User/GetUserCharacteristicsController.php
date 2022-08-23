<?php

namespace App\Controller\User;

use App\Failure\User\UserFailure;
use App\Controller\AbstractApiController;
use App\Entity\UserCharacteristic;
use Symfony\Component\HttpFoundation\Request;
use App\Service\User\UserCharacteristicService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * class GetUserCharacteristicsController
 * @package App\Controller\User
 *
 * @Route("/api/user")
 */
class GetUserCharacteristicsController extends AbstractApiController
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
     * @Route("/get-characteristics", name="user_get_characteristic", methods={"POST"})
     */
    public function getCharacteristics(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->userCharacteristicService->validateInput($paramsIn);

            return $this->success(UserCharacteristic::toArrays($this->userCharacteristicService->fetchAll($paramsIn)));
        } catch (\Throwable $th) {
            return $this->failure(new UserFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la récupération des caractéristiques' : $th->getMessage()));
        }
    }
}
