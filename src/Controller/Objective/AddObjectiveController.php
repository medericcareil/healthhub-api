<?php

namespace App\Controller\Objective;

use App\Controller\AbstractApiController;
use App\Entity\Objective;
use App\Failure\Objective\ObjectiveFailure;
use App\Service\Objective\ObjectiveService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class AddObjectiveController
 * @package App\Controller\Objective
 *
 * @Route("/api/objective")
 */
class AddObjectiveController extends AbstractApiController
{
    /**
     * @var ObjectiveService
     */
    private ObjectiveService $objectiveService;

    public function __construct(ObjectiveService $objectiveService)
    {
        $this->objectiveService = $objectiveService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/add-objective", name="objective_add_objective", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);
        
        try {
            $this->objectiveService->validateInputs($paramsIn);
            $objective = $this->objectiveService->persist($paramsIn);

            return $this->success(Objective::toArray($objective));
        } catch (\Throwable $th) {
            return $this->failure(new ObjectiveFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout de l\'objectif' : $th->getMessage()));
        }
    }
}
