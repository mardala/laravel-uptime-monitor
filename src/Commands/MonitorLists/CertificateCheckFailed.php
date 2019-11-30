<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class CertificateCheckFailed
{
    public static function display()
    {
        $monitorsWithFailingCertificateChecks = MonitorRepository::getWithFailingCertificateCheck();

        if (! $monitorsWithFailingCertificateChecks->count()) {
            return;
        }

        ConsoleOutput::warn('Certificate check failed');
        ConsoleOutput::warn('========================');

        $rows = $monitorsWithFailingCertificateChecks->map(function (Monitor $monitor) {
            $id = $monitor->id;
            $url = $monitor->url;

            $reason = $monitor->chunkedLastCertificateCheckFailureReason;

            return compact('id', 'url', 'reason');
        });

        $titles = ['ID', 'URL', 'Problem description'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
