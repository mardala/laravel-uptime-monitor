<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\MonitorRepository;

class EnableMonitor extends BaseCommand
{
    protected $signature = 'monitor:enable {input : ID or URL}';

    protected $description = 'Enable a monitor';

    public function handle()
    {
        foreach (explode(',', $this->argument('input')) as $input) {
            $this->enableMonitor(trim($input));
        }
    }

    protected function enableMonitor($input)
    {
        if(is_numeric($input)){
            if (! $monitor = MonitorRepository::findById($input)) {
                $this->error("There is no monitor configured with the id `{$input}`.");
                return;
            }
        }else{
            if (! $monitor = MonitorRepository::findByUrl($input)) {
                $this->error("There is no monitor configured for url `{$input}`.");
                return;
            }
        }

        $monitor->enable();

        $this->info("The checks for `{$monitor->id}` : `{$monitor->url}` are now enabled.");
    }
}
