<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\UptimeMonitor\Models\Monitor;

class DeleteMonitor extends BaseCommand
{
    protected $signature = 'monitor:delete {input : ID or URL}';

    protected $description = 'Delete a monitor';

    public function handle()
    {
        $input = trim($this->argument('input'));

        if( is_numeric($input) ){
            $monitor = Monitor::where('id', $input)->first();
        }else{
            $monitor = Monitor::where('url', $input)->first();
        }

        if (! $monitor) {
            $this->error("Monitor {$input} is not configured");

            return;
        }

        if ($this->confirm("Are you sure you want stop monitoring {$monitor->id} : {$monitor->url}?")) {
            $monitor->delete();

            $this->warn("{$monitor->id} : {$monitor->url} will not be monitored anymore");
        }
    }
}
