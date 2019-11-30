<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class UptimeCheckFailed
{
    public static function display()
    {
        $failingMonitors = MonitorRepository::getWithFailingUptimeCheck();

        if (! $failingMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Uptime check failed');
        ConsoleOutput::warn('===================');

        $rows = $failingMonitors->map(function (Monitor $monitor) {
            $certificateFound = '';
            $certificateExpirationDate = '';
            $certificateIssuer = '';

            $id = $monitor->id;
            $url = $monitor->url;

            $reachable = $monitor->uptimeStatusAsEmoji;

            $offlineSince = $monitor->formattedLastUpdatedStatusChangeDate('forHumans');

            $reason = $monitor->chunkedLastFailureReason;

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate('forHumans');
                $certificateIssuer = $monitor->certificate_issuer;
            }

            return compact('id', 'url', 'reachable', 'offlineSince', 'reason', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer');
        });

        $titles = ['ID', 'URL', 'Reachable', 'Offline since', 'Reason', 'Certificate', 'Certificate expiration date', 'Certificate issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
