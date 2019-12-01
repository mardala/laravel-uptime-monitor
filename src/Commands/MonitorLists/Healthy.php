<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class Healthy
{
    public static function display()
    {
        $healthyMonitor = MonitorRepository::getHealthy();

        if (! $healthyMonitor->count()) {
            return;
        }

        ConsoleOutput::info('Healthy monitors');
        ConsoleOutput::info('================');

        $rows = $healthyMonitor->map(function (Monitor $monitor) {
            $certificateFound = '';
            $certificateExpirationDate = '';
            $certificateIssuer = '';

            $expirationFound = '';
            $expirationDate = '';
            $registrar = '';
            
            $id = $monitor->id;
            $url = $monitor->url;

            $reachable = $monitor->uptimeStatusAsEmoji;

            $onlineSince = $monitor->formattedLastUpdatedStatusChangeDate('forHumans');

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate('forHumans');
                $certificateIssuer = $monitor->certificate_issuer;
            }

            if ($monitor->domain_expiration_check_enabled){
                $expirationFound = $monitor->expirationStatusAsEmoji;
                $expirationDate = $monitor->formattedDomainExpirationDate('forHumans');
                $registrar = $monitor->domain_registrar;
            }

            return compact('id', 'url', 'reachable', 'onlineSince', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer', 'expirationFound', 'expirationDate', 'registrar');
        });

        $titles = ['ID', 'URL', 'Uptime Check', 'Online', 'Cert Check', 'Cert Exp date', 'Cert Issuer', 'Exp check', 'Exp Date', 'Registrar'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
