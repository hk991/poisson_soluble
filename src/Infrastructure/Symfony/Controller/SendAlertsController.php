<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Controller;

use Domain\Request\Recipient\SendAlertsRequest;
use Domain\UseCase\Recipient\SendAlertsUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SendAlertsController
{
    #[Route('/alerter', name: 'send_alerts', methods: ['POST'])]
    public function sendAlerts(
        Request $request,
        SendAlertsUseCase $sendAlertsUseCase,
    ): JsonResponse {
        if ($request->isMethod('POST') && $request->headers->get('Content-Type') !== 'application/json') {
            return new JsonResponse(['error' => 'Incorrect Content-Type. Expected application/json.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        if (!\is_array($data) || !isset($data['insee'])) {
            return new JsonResponse(['error' => 'Missing or invalid JSON payload'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $sendAlertsUseCase->sendAlerts(new SendAlertsRequest($data['insee'], $request->headers->get('x-api-key')));

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => $response->getMessage()], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['error' => $response->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
    }
}
