<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\DomainExpirationCheckFailed as DomainExpirationCheckFailedFoundEvent;

class DomainExpirationCheckFailed extends BaseNotification
{
    /** 
     * @var \Spatie\UptimeMonitor\Events\DomainExpirationCheckFailed 
     */
    public $event;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->error()
            ->subject($this->getMessageText())
            ->line($this->getMessageText());

        foreach ($this->getMonitorProperties() as $name => $value) {
            $mailMessage->line($name.': '.$value);
        }

        return $mailMessage;
    }

    /**
     * @param  mixed $notifiable
     * @return Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->content($this->getMonitor()->domain_expiration_check_failure_reason)
                    ->fallback($this->getMessageText())
                    ->footer($this->getMonitor()->domain_registrar)
                    ->timestamp(Carbon::now());
            });
    }
    
    public function getMonitorProperties($properties = []): array
    {
        $extraProperties = ['Failure reason' => $this->event->monitor->domain_expiration_check_failure_reason];

        return parent::getMonitorProperties($extraProperties);
    }

    public function setEvent(DomainExpirationCheckFailedFoundEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    public function getMessageText(): string
    {
        return "Domain {$this->getMonitor()->url} is expiring soon or expired. The registrar is {$this->getMonitor()->domain_registrar}.";
    }
}
