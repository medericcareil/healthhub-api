<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    /**
     * Returns a success response with optionnal data (Code: 200)
     * @param mixed $data 
     * @return JsonResponse 
     */
    public function success($data = null): JsonResponse
    {
        $return = ['status_code' => Response::HTTP_OK];
        if ($data) {
            $return['data'] = $data;
        }

        return $this->json($return, Response::HTTP_OK);
    }

    /**
     * Returns a failure response with optionnal data (Code: 200)
     * @param mixed $data 
     * @return JsonResponse 
     */
    public function failure($data = null, $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $return = ['status_code' => $status];

        if ($data) {
            $return['data'] = $data;
        }

        return $this->json($return, Response::HTTP_OK);
    }
}
