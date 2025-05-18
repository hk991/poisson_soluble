<?php

declare(strict_types=1);

namespace Domain\Recipient\Gateway;

use Domain\Recipient\Request\SendAlertsRequest;

interface ApiKeyValidatorInterface
{
    public function validate(SendAlertsRequest $sendAlertRequest): void;
}
