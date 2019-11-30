<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\MonitorRepository;

class DisableMonitor extends BaseCommand
{
    protected $signature = 'monitor:disable {url}';

    protected $description = 'Disable a monitor';

    public function handle()
    {
        foreach (explode(',', $this->argument('url')) as $url) {
            if( is_numeric($url) ){
                $this->disableMonitorById(trim($url));
            }else{
                $this->disableMonitor(trim($url));
            }
        }
    }

    protected function disableMonitorById($id)
    {
        if (! $monitor = MonitorRepository::findById($id)) {
            $this->error("There is no monitor configured with id `{$id}`.");

            return;
        }

        $url = $monitor->url;
        $monitor->disable();

        $this->info("The checks for url `{$url}` are now disabled.");
    }

    protected function disableMonitor(string $url)
    {
        if (! $monitor = MonitorRepository::findByUrl($url)) {
            $this->error("There is no monitor configured for url `{$url}`.");

            return;
        }

        $monitor->disable();

        $this->info("The checks for url `{$url}` are now disabled.");
    }
}
