<?php

namespace Spatie\UptimeMonitor\Commands;

use Spatie\Url\Url;
use Spatie\UptimeMonitor\Models\Monitor;

class CreateMonitor extends BaseCommand
{
    protected $signature = 'monitor:create {url}';

    protected $description = 'Create a monitor';

    public function handle()
    {
        $url = Url::fromString($this->argument('url'));

        if (! in_array($url->getScheme(), ['http', 'https'])) {
            if ($scheme = $this->choice("Which protocol needs to be used for checking `{$url}`?", [1 => 'https', 2 => 'http'], 1)) {
                $url = $url->withScheme($scheme);
            }
        }

        $expiration = $this->confirm('Do you want to monitor domain expiration?');

        if ($this->confirm('Should we look for a specific string on the response?')) {
            $lookForString = $this->ask('Which string?');
        }

        $monitor = Monitor::create([
            'url' => trim($url, '/'),
            'look_for_string' => $lookForString ?? '',
            'uptime_check_method' => isset($lookForString) ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
            'domain_expiration_check_enabled' => $expiration,
            'uptime_check_interval_in_minutes' => config('uptime-monitor.uptime_check.run_interval_in_minutes'),
        ]);

        $this->warn("{$monitor->url} will be monitored!");
    }
}
