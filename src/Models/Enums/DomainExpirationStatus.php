<?php

namespace Spatie\UptimeMonitor\Models\Enums;

class DomainExpirationStatus
{
    const NOT_YET_CHECKED = 'not yet checked';
    const FAILED = 'failed check';
    const SAFE = 'safe';
    const EXPIRING = 'expiring soon';
    const EXPIRED = 'domain expired';
}
