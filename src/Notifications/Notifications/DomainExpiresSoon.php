<?php

namespace Spatie\UptimeMonitor\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Spatie\UptimeMonitor\Notifications\BaseNotification;
use Spatie\UptimeMonitor\Events\DomainExpiresSoon as DomainExpiresSoonEvent;

class DomainExpiresSoon extends BaseNotification
{
    /** 
     * @var \Spatie\UptimeMonitor\Events\DomainExpiresSoon 
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

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->warning()
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title($this->getMessageText())
                    ->content("Expires {$this->getMonitor()->formattedDomainExpirationDate('forHumans')}")
                    ->fallback($this->getMessageText())
                    ->footer($this->getMonitor()->domain_registrar)
                    ->timestamp(Carbon::now());
            });
    }

    public function setEvent(DomainExpiresSoonEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    protected function getMessageText(): string
    {
        return "Domain {$this->getMonitor()->url} expires soon. The registrar is {$this->getMonitor()->domain_registrar}.";
    }
}
