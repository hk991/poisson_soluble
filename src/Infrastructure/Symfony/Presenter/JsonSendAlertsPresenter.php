<?php

namespace Infrastructure\Symfony\Presenter;

use Domain\Recipient\Presenter\SendAlertsPresenterInterface;
use Domain\Recipient\Response\SendAlertsResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonSendAlertsPresenter implements SendAlertsPresenterInterface
{
    public function present(SendAlertsResponse $response): JsonResponse
    {
        if ($response->isSuccess()) {
            return new JsonResponse(['message' => $response->getMessage()], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => $response->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
    }
}
