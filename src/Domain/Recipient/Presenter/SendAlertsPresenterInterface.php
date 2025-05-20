<?php

namespace Domain\Recipient\Presenter;

use Domain\Recipient\Response\SendAlertsResponse;

interface SendAlertsPresenterInterface
{
    public function present(SendAlertsResponse $response): mixed;
}
