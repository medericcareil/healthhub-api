<?php

namespace App\Controller\Activity;

use App\Controller\AbstractApiController;
use App\Entity\Activity;
use App\Failure\Activity\ActivityFailure;
use App\Service\Activity\ActivityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class GetActivitiesController
 * @package App\Controller\Activity
 *
 * @Route("/api/activity")
 */
class GetActivitiesController extends AbstractApiController
{
    /**
     * @var ActivityService
     */
    private ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/get-activities", name="activity_get_activities", methods={"POST"})
     */
    public function getActivities(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->activityService->validateInput($paramsIn);
            $this->activityService->isUser($paramsIn['user_id']);

            return $this->success(Activity::toArrays($this->activityService->fetchAll($paramsIn['user_id'])));
        } catch (\Throwable $th) {
            return $this->failure(new ActivityFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la récupération des activités' : $th->getMessage()));
        }
    }
}
