<?php

namespace Spatie\UptimeMonitor\Commands\MonitorLists;

use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\MonitorRepository;
use Spatie\UptimeMonitor\Helpers\ConsoleOutput;

class Disabled
{
    public static function display()
    {
        $disabledMonitors = MonitorRepository::getDisabled();

        if (! $disabledMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Disabled monitors');
        ConsoleOutput::warn('=================');

        $rows = $disabledMonitors->map(function (Monitor $monitor) {
            $id = $monitor->id;
            $url = $monitor->url;

            return compact('id', 'url');
        });

        $titles = ['ID', 'URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
