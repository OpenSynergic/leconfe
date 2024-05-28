<?php

namespace App\Actions\MailTemplates;

use App\Mail\Templates\TemplateMailable;
use App\Models\Conference;
use App\Models\MailTemplate;
use Illuminate\Filesystem\Filesystem;
use Lorisleiva\Actions\Concerns\AsAction;
use ReflectionClass;

class MailTemplatePopulateDefaultData
{
    use AsAction;

    public function handle(Conference $conference)
    {
        $directory = app_path('Mail/Templates');
        $namespace = 'App\\Mail\\Templates';

        $filesystem = app(Filesystem::class);
        if ((! $filesystem->exists($directory)) && (! str($directory)->contains('*'))) {
            return;
        }

        $namespace = str($namespace);

        foreach ($filesystem->allFiles($directory) as $file) {
            $variableNamespace = $namespace->contains('*') ? str_ireplace(
                ['\\'.$namespace->before('*'), $namespace->after('*')],
                ['', ''],
                str($file->getPath())
                    ->after(base_path())
                    ->replace(['/'], ['\\']),
            ) : null;

            if (is_string($variableNamespace)) {
                $variableNamespace = (string) str($variableNamespace)->before('\\');
            }

            $class = (string) $namespace
                ->append('\\', $file->getRelativePathname())
                ->replace('*', $variableNamespace)
                ->replace(['/', '.php'], ['\\', '']);

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            if (! is_subclass_of($class, TemplateMailable::class)) {
                continue;
            }

            // Populate default data
            $data = [
                'subject' => $class::getDefaultSubject(),
                'html_template' => $class::getDefaultHtmlTemplate(),
                'text_template' => $class::getDefaultTextTemplate(),
                'description' => $class::getDefaultDescription(),
            ];

            MailTemplate::firstOrCreate(
                [
                    'conference_id' => $conference->id,
                    'mailable' => $class
                ],
                $data
            );
        }
    }
}
