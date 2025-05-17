<?php

declare(strict_types=1);

namespace Domain\Gateway;

use Domain\Request\Recipient\SendAlertsRequest;

interface ApiKeyValidatorInterface
{
    public function validate(SendAlertsRequest $sendAlertRequest): void;
}
