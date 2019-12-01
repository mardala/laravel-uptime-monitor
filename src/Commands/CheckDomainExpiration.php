<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Models\Enums\DomainExpirationStatus;

class CheckDomainExpiration extends BaseCommand
{
    protected $signature = 'monitor:check-expiration
                           {--url= : Only check these urls} 
                           {--id= : Only check these ids}';

    protected $description = 'Check the domain expiration of all sites';

    public function handle()
    {
        $monitors = MonitorRepository::getForExpirationCheck();

        if ($id = $this->option('id')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($id) {
                return in_array((int) $monitor->id, explode(',', $id));
            });
        }

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the domain expiration of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking expiration of {$monitor->id} : {$monitor->url}");

            $monitor->checkExpiration();

            if ($monitor->domain_expiration_status !== DomainExpirationStatus::SAFE || $monitor->domain_expiration_status !== DomainExpirationStatus::EXPIRING) {
            	if($monitor->domain_expiration_status === DomainExpirationStatus::FAILED){
            		$this->error("Could not get domain registration expiration date for {$monitor->id}:{$monitor->url} because: {$monitor->domain_expiration_check_failure_reason}");
            	}

            	if($monitor->domain_expiration_status === DomainExpirationStatus::EXPIRED){
            		$this->error("Domain {$monitor->id}:{$monitor->url} is expired! {$monitor->certificate_check_failure_reason}");
            	}
            }

            if($monitor->domain_expiration_status === DomainExpirationStatus::EXPIRING){
                $this->info("Domain {$monitor->id}:{$monitor->url} will be expiring soon.");
            }
        });

        $this->info('All done!');
    }
}
