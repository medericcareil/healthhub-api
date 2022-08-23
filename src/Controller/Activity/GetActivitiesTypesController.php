<?php

namespace App\Controller\Activity;

use App\Controller\AbstractApiController;
use App\Entity\ActivityType;
use App\Failure\Activity\ActivityTypeFailure;
use App\Service\Activity\ActivityTypeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class GetActivitiesTypesController
 * @package App\Controller\Activity
 *
 * @Route("/api/activity")
 */
class GetActivitiesTypesController extends AbstractApiController
{
    /**
     * @var ActivityService
     */
    private ActivityTypeService $activityTypeService;

    public function __construct(ActivityTypeService $activityTypeService)
    {
        $this->activityTypeService = $activityTypeService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/get-activities-types", name="activity_get_activities_types", methods={"POST"})
     */
    public function getActivities(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->activityTypeService->validateInput($paramsIn);
            $this->activityTypeService->isUser($paramsIn['user_id']);

            return $this->success(ActivityType::toArrays($this->activityTypeService->fetchAll()));
        } catch (\Throwable $th) {
            return $this->failure(new ActivityTypeFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la récupération des types activités' : $th->getMessage()));
        }
    }
}
