<?php

namespace App\Controller\Activity;

use App\Entity\ActivityType;
use App\Controller\AbstractApiController;
use App\Failure\Activity\ActivityTypeFailure;
use App\Service\Activity\ActivityTypeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class AddActivityTypeController
 * @package App\Controller\Activity
 *
 * @Route("/api/activity")
 */
class AddActivityTypeController extends AbstractApiController
{
    /**
     * @var ActivityTypeService
     */
    private ActivityTypeService $activityTypeService;

    public function __construct(ActivityTypeService $activityTypeService)
    {
        $this->activityTypeService = $activityTypeService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/add-activity-type", name="activity_add_activity_type", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->activityTypeService->validateInputs($paramsIn);
            $this->activityTypeService->isRoleAdmin($paramsIn['user_id']);
            $activityType = $this->activityTypeService->persist($paramsIn);

            return $this->success(ActivityType::toArray($activityType));
        } catch (\Throwable $th) {
            return $this->failure(new ActivityTypeFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de l\'ajout du type d\'activité' : $th->getMessage()));
        }
    }
}
