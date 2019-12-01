<?php

namespace Spatie\UptimeMonitor\Events;

use Spatie\UptimeMonitor\Models\Monitor;
use Iodev\Whois\Modules\Tld\DomainInfo;
use Illuminate\Contracts\Queue\ShouldQueue;

class DomainExpiresSoon implements ShouldQueue
{
    /** 
    * @var \Spatie\UptimeMonitor\Models\Monitor 
    */
    public $monitor;

    /** 
     * @var \Iodev\Whois\Modules\Tld\DomainInfo
     */
    public $info;

    public function __construct(Monitor $monitor, DomainInfo $info)
    {
        $this->monitor = $monitor;

        $this->info = $info;
    }
}
