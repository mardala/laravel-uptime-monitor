<?php

namespace Spatie\UptimeMonitor\Models\Traits;

use Exception;
use Carbon\Carbon;
use Spatie\UptimeMonitor\Models\Monitor;
use Spatie\UptimeMonitor\Events\DomainExpirationCheckFailed;
use Spatie\UptimeMonitor\Events\DomainExpiresSoon;
use Spatie\UptimeMonitor\Events\DomainExpired;
use Spatie\UptimeMonitor\Events\DomainExpirationCheckSucceeded;
use Spatie\UptimeMonitor\Models\Enums\DomainExpirationStatus;
// 
use Iodev\Whois\Whois;
use Iodev\Whois\Modules\Tld\TldInfo;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

trait SupportsDomainExpirationCheck
{
    public function checkExpiration()
    {
        try {
            $whois = Whois::create();
            $domain = $this->url->getHost();
            $info = $whois->loadDomainInfo($domain);

            $this->setExpiration($info);

        } catch (Exception $e) {
            $this->setExpirationException($e);
        }
    }

    public function setExpiration(TldInfo $info)
    {
        $exp = $info->getExpirationDate();

        if( $this->isExpired($exp) ){
            $this->domain_expiration_status = DomainExpirationStatus::EXPIRED;
        }elseif( $this->isExpiring($exp) ){
            $this->domain_expiration_status = DomainExpirationStatus::EXPIRING;
        }else{
            $this->domain_expiration_status = DomainExpirationStatus::SAFE;
        }

        $carbon = Carbon::createFromTimestamp($exp);    
        $expiration = $carbon->toDateTimeString();

        $this->domain_expiration_date = $expiration;
        $this->domain_registrar = $info->getRegistrar();

        $this->save();
        $this->fireEventsForUpdatedMonitorWithDomainExpiration($this, $info);
    }

    public function setExpirationException(Exception $exception)
    {
        $this->domain_expiration_status = DomainExpirationStatus::FAILED;
        $this->domain_expiration_date = null;
        $this->domain_registrar = '';
        $this->domain_expiration_check_failure_reason = $exception->getMessage();
        $this->save();

        event(new DomainExpirationCheckFailed($this, $exception->getMessage(), null));
    }

    protected function fireEventsForUpdatedMonitorWithDomainExpiration(Monitor $monitor, TldInfo $info)
    {
        $exp = $info->getExpirationDate();

        if ($this->domain_expiration_status === DomainExpirationStatus::SAFE) {
            event(new DomainExpirationCheckSucceeded($this, $info));

            if ( $this->isExpiring($exp) ) {
                event(new DomainExpiresSoon($monitor, $info));
            }

            return;
        }

        if($this->domain_expiration_status === DomainExpirationStatus::EXPIRED){
            $reason = 'Domain expired!';
            event(new DomainExpirationCheckFailed($this, $reason, $info));
        }
    }

    protected function isExpiring(int $expiration)
    {
        $expires = Carbon::createFromTimestamp($expiration);
        $now = Carbon::now();
        $diff = $expires->diffInDays($now);

        return $diff <= config('uptime-monitor.expiration_check.fire_expiring_soon_event_if_domain_expires_within_days');
    }

    protected function isExpired(int $expiration)
    {
        $carbon = Carbon::createFromTimestamp($expiration);
        return $carbon->isPast();
    }
}
