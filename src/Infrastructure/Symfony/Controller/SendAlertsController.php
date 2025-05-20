<?php

namespace Infrastructure\Symfony\Controller;

use Domain\Recipient\Request\SendAlertsRequest;
use Domain\Recipient\UseCase\SendAlertsUseCase;
use Domain\Recipient\Presenter\SendAlertsPresenterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SendAlertsController
{
    #[Route('/alerter', name: 'send_alerts', methods: ['POST'])]
    public function __invoke(
        Request $request,
        SendAlertsUseCase $sendAlertsUseCase,
        SendAlertsPresenterInterface $presenter,
    ): JsonResponse {
        if ($request->isMethod('POST') && $request->headers->get('Content-Type') !== 'application/json') {
            return new JsonResponse(['error' => 'Incorrect Content-Type. Expected application/json.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        if (!\is_array($data) || !isset($data['insee'])) {
            return new JsonResponse(['error' => 'Missing or invalid JSON payload'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $response = $sendAlertsUseCase->execute(
            new SendAlertsRequest($data['insee'], $request->headers->get('x-api-key'))
        );

        return $presenter->present($response);
    }
}
