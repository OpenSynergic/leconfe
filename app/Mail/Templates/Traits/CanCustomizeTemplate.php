<?php

namespace App\Mail\Templates\Traits;

use Illuminate\Contracts\Mail\Mailable;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;

trait CanCustomizeTemplate
{
    public ?string $customizedContent = null;

    public ?string $customizedSubject = null;

    abstract protected function resolveTemplateModel(): MailTemplateInterface;

    public function contentUsing(?string $customizedContent): static
    {
        $this->customizedContent = $customizedContent;
        return $this;
    }

    public function subjectUsing(?string $customizedSubject): static
    {
        $this->customizedSubject = $customizedSubject;
        return $this;
    }

    public function getMailTemplate(): MailTemplateInterface
    {
        if ($this->customizedContent) {
            return new class($this->customizedContent, $this->customizedSubject) implements MailTemplateInterface
            {
                public function __construct(public ?string $content, public ?string $subject)
                {
                }

                public function getSubject(): string
                {
                    return $this->subject;
                }

                public function getHtmlTemplate(): string
                {
                    return $this->content;
                }

                public static function findForMailable(Mailable $mailable)
                {
                    return null;
                }

                public function getTextTemplate(): ?string
                {
                    return null;
                }
            };
        }

        return $this->resolveTemplateModel();
    }
}
