<?php

namespace Spatie\UptimeMonitor\Events;

use Spatie\UptimeMonitor\Models\Monitor;
use Iodev\Whois\Modules\Tld\TldInfo;
use Illuminate\Contracts\Queue\ShouldQueue;

class DomainExpirationCheckSucceeded implements ShouldQueue
{
    /** 
     * @var \Spatie\UptimeMonitor\Models\Monitor 
     */
    public $monitor;

    /** 
     * @var \Iodev\Whois\Modules\Tld\TldInfo 
     */
    public $info;

    public function __construct(Monitor $monitor, TldInfo $info)
    {
        $this->monitor = $monitor;

        $this->info = $info;
    }
}
