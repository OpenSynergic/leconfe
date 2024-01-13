<?php

namespace App\Mail\Templates;

use App\Models\DiscussionTopic;

class NewDiscussionTopicMail extends TemplateMailable
{
    public string $topicName;

    public string $submissionTitle;

    public string $linkLogin;

    public function __construct(DiscussionTopic $discussionTopic)
    {
        $this->topicName = $discussionTopic->name;
        $this->submissionTitle = $discussionTopic->submission->getMeta('title');
        $this->linkLogin = route('livewirePageGroup.website.pages.login');
    }

    public static function getDefaultSubject(): string
    {
        return 'New discussion topic';
    }

    public static function getDefaultDescription(): string
    {
        return 'This email is sent when a new discussion topic is created.';
    }

    public static function getDefaultHtmlTemplate(): string
    {
        return <<<'HTML'
         <p> You've been added to a new discussion. Here are the details:</p>
         <table>
                <tr>
                    <td style="width:100px;">Submission</td>
                    <td>: {{ submissionTitle }}</td>
                </tr>
                <tr>
                    <td style="width:100px;">Topic</td>
                    <td>: {{ topicName }}</td>
                </tr>
            </table>
            <p>To access additional information, You can <a href="{{ linkLogin }}" target="_blank">login here</a></p>
        HTML;
    }
}
