<?php

namespace App\Controller\Statistic;

use App\Controller\AbstractApiController;
use App\Entity\Activity;
use App\Failure\Statistic\StatisticFailure;
use App\Service\Statistic\StatisticService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class GetStatisticController
 * @package App\Controller\Statistic
 * 
 * @Route("/api/statistic")
 */
class GetStatisticController extends AbstractApiController
{
    /**
     * @var StatisticService
     */
    private StatisticService $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }

    /**
     * @return JsonResponse
     *
     * @Route("/get-stats-between", name="statistic_get_statistics_between", methods={"POST"})
     */
    public function getStatisticsBetween(Request $request): JsonResponse
    {
        $paramsIn = json_decode($request->getContent(), true);

        try {
            $this->statisticService->validateInputs($paramsIn);

            return $this->success(Activity::toArrays($this->statisticService->fetchBetween($paramsIn)));
        } catch (\Throwable $th) {
            return $this->failure(new StatisticFailure($th->getMessage() === '' ? 'Une erreur est survenue lors de la récupération des statistiques' : $th->getMessage()));
        }
    }
}
