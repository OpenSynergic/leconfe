<?php

namespace App\Mail\Templates;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable as BaseTemplateMailable;
use Illuminate\Mail\Mailables\Address;

abstract class TemplateMailable extends BaseTemplateMailable implements Interfaces\HasDefaultMailVariable, ShouldQueue
{
    use Queueable, SerializesModels;

    protected static $templateModelClass = MailTemplate::class;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    public static function getDefaultTextTemplate(): string
    {
        return preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags(static::getDefaultHtmlTemplate()))));
    }

    public static function getVariables(): array
    {
        return array_merge(static::getConferenceViewData(), parent::getVariables());
    }

    public function buildViewData()
    {
        return array_merge(static::getConferenceViewData(), parent::buildViewData());
    }

    public static function getConferenceViewData()
    {
        $conference = app()->getCurrentConference();

        if (!$conference) return [];


        return [
            'conferenceName' => $conference->name,
            'conferenceLink' => $conference->getHomeUrl(),
            'conferenceLogoUrl' => $conference->getFirstMedia('logo')?->getAvailableUrl(['thumb', 'thumb-xl']),
            'conferenceLogoAltText' => $conference->name,
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), app()->getCurrentConference()->name ?? config('mail.from.name')),
        );
    }
}
