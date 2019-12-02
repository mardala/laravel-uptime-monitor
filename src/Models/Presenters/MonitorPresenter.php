<?php

namespace Spatie\UptimeMonitor\Models\Presenters;

use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Enums\CertificateStatus;
use Spatie\UptimeMonitor\Models\Enums\DomainExpirationStatus;

trait MonitorPresenter
{
    public function getUptimeStatusAsEmojiAttribute(): string
    {
        if ($this->uptime_status === UptimeStatus::UP) {
            return '✅';
        }

        if ($this->uptime_status === UptimeStatus::DOWN) {
            return '❌';
        }

        return '';
    }

    public function getCertificateStatusAsEmojiAttribute(): string
    {
        if ($this->certificate_status === CertificateStatus::VALID) {
            return '✅';
        }

        if ($this->certificate_status === CertificateStatus::INVALID) {
            return '❌';
        }

        return '';
    }

    public function getExpirationStatusAsEmojiAttribute(): string
    {
        if ($this->domain_expiration_status === DomainExpirationStatus::SAFE) {
            return '✅';
        }

        if ($this->domain_expiration_status === DomainExpirationStatus::EXPIRING) {
            return '⚠️';
        }

        if ($this->domain_expiration_status === DomainExpirationStatus::EXPIRED) {
            return '❌';
        }

        return '';
    }

    public function formattedLastUpdatedStatusChangeDate(string $format = ''): string
    {
        return $this->formatDate('uptime_status_last_change_date', $format);
    }

    public function formattedCertificateExpirationDate(string $format = ''): string
    {
        return $this->formatDate('certificate_expiration_date', $format);
    }

    public function formattedDomainExpirationDate(string $format = ''): string
    {
        return $this->formatDate('domain_expiration_date', $format);
    }

    public function getChunkedLastFailureReasonAttribute(): string
    {
        if ($this->uptime_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->uptime_check_failure_reason, 30, "\n");
    }

    public function getChunkedLastCertificateCheckFailureReasonAttribute(): string
    {
        if ($this->certificate_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->certificate_check_failure_reason, 60, "\n");
    }

    public function getChunkedLastExpirationCheckFailureReasonAttribute(): string
    {
        if ( empty($this->domain_expiration_check_failure_reason) ) {
            return '';
        }

        return chunk_split($this->domain_expiration_check_failure_reason, 60, "\n");
    }

    protected function formatDate(string $attributeName, string $format = ''): string
    {
        if (! $this->$attributeName) {
            return '';
        }

        if ($format === 'forHumans') {
            return $this->$attributeName->diffForHumans();
        }

        if ($format === '') {
            $format = config('uptime-monitor.notifications.date_format');
        }

        return $this->$attributeName->format($format);
    }
}
