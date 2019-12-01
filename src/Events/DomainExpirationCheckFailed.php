<?php

namespace Spatie\UptimeMonitor\Events;

use Spatie\UptimeMonitor\Models\Monitor;
use Iodev\Whois\Modules\Tld\TldInfo;
use Illuminate\Contracts\Queue\ShouldQueue;

class DomainExpirationCheckFailed implements ShouldQueue
{
    /** 
     * @var \Spatie\UptimeMonitor\Models\Monitor 
     */
    public $monitor;

    /** 
     * @var string 
     */
    public $reason;

    /** 
     * @var \Iodev\Whois\Modules\Tld\TldInfo|null 
     */
    public $info;

    public function __construct(Monitor $monitor, string $reason, $info)
    {
        $this->monitor = $monitor;
        $this->reason = $reason;
        $this->info = $info;
    }
}
