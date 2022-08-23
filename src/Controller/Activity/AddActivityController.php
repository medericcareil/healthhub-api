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
 * class AddActivityController
 * @package App\Controller\Activity
 *
 * @Route("/api/activity")
 */
class AddActivityController extends AbstractApiController
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
     * @Route("/add-activity", name="activity_add_activity", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->activityService->validateInputs($paramsIn);
            $activity = $this->activityService->persist($paramsIn);

            return $this->success(Activity::toArray($activity));
        } catch (\Throwable $th) {
            return $this->failure(new ActivityFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout du type d\'activité' : $th->getMessage()));
        }
    }
}
