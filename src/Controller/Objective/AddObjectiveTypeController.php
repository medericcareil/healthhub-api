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
 * class AddObjectiveTypeController
 * @package App\Controller\Objective
 *
 * @Route("/api/objective")
 */
class AddObjectiveTypeController extends AbstractApiController
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
     * @Route("/add-objective-type", name="objective_add_objective_type", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->objectiveTypeService->validateInputs($paramsIn);
            $this->objectiveTypeService->isRoleAdmin($paramsIn['user_id']);
            $objectiveType = $this->objectiveTypeService->persist($paramsIn);

            return $this->success(ObjectiveType::toArray($objectiveType));
        } catch (\Throwable $th) {
            return $this->failure(new ObjectiveTypeFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout du type d\'objectif' : $th->getMessage()));
        }
    }
}
