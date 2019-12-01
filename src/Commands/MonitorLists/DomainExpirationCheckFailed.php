<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class DomainExpirationCheckFailed
{
    public static function display()
    {
        $monitorsWithFailingExpirationChecks = MonitorRepository::getWithFailingExpirationCheck();

        if (! $monitorsWithFailingExpirationChecks->count()) {
            return;
        }

        ConsoleOutput::warn('Domain Expiration check failed');
        ConsoleOutput::warn('========================');

        $rows = $monitorsWithFailingExpirationChecks->map(function (Monitor $monitor) {
            $id = $monitor->id;
            $url = $monitor->url;

            $reason = $monitor->getChunkedLastExpirationCheckFailureReasonAttribute;

            return compact('id', 'url', 'reason');
        });

        $titles = ['ID', 'URL', 'Problem description'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
