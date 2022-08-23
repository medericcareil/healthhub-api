<?php

namespace App\Controller\Objective;

use App\Entity\Objective;
use App\Controller\AbstractApiController;
use App\Failure\Objective\ObjectiveFailure;
use App\Service\Objective\ObjectiveService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class GetObjectivesController
 * @package App\Controller\Objective
 *
 * @Route("/api/objective")
 */
class GetObjectivesController extends AbstractApiController
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
     * @Route("/get-objectives", name="objective_get_objectives", methods={"POST"})
     */
    public function getObjectives(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->objectiveService->validateInput($paramsIn);
            $this->objectiveService->isUser($paramsIn['user_id']);

            return $this->success(Objective::toArrays($this->objectiveService->fetchAll($paramsIn['user_id'])));
        } catch (\Throwable $th) {
            return $this->failure(new ObjectiveFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la récupération des objectifs' : $th->getMessage()));
        }
    }
}
