<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\MonitorRepository;

class DisableMonitor extends BaseCommand
{
    protected $signature = 'monitor:disable {input : ID or Full URL}';

    protected $description = 'Disable a monitor';

    public function handle()
    {
        foreach (explode(',', $this->argument('input')) as $input) {
            $this->disableMonitor(trim($input));
        }
    }

    protected function disableMonitor($input)
    {
        if( is_numeric($input) ){
            if (! $monitor = MonitorRepository::findById($input)) {
                $this->error("There is no monitor configured with id `{$input}`.");

                return;
            }
        }else{
            if (! $monitor = MonitorRepository::findByUrl($input)) {
                $this->error("There is no monitor configured for url `{$input}`.");

                return;
            }
        }

        $monitor->disable();

        $this->info("The checks for `{$monitor->id}` : `{$monitor->url}` are now disabled.");
    }
}
