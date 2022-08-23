<?php

namespace App\Controller\Objective;

use App\Entity\ObjectiveType;
use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use App\Failure\Objective\ObjectiveTypeFailure;
use App\Service\Objective\ObjectiveTypeService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class GetObjectivesTypesController
 * @package App\Controller\Objective
 *
 * @Route("/api/objective")
 */
class GetObjectivesTypesController extends AbstractApiController
{
    /**
     * @var ObjectiveTypeService
     */
    private ObjectiveTypeService $objectiveTypeService;

    public function __construct(ObjectiveTypeService $objectiveTypeService)
    {
        $this->objectiveTypeService = $objectiveTypeService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/get-objectives-types", name="objective_get_objectives_types", methods={"POST"})
     */
    public function getObjectivesTypes(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->objectiveTypeService->validateInput($paramsIn);
            $this->objectiveTypeService->isUser($paramsIn['user_id']);

            return $this->success(ObjectiveType::toArrays($this->objectiveTypeService->fetchAll()));
        } catch (\Throwable $th) {
            return $this->failure(new ObjectiveTypeFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout de l\objectif' : $th->getMessage()));
        }
    }
}
