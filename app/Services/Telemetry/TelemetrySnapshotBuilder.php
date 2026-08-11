<?php

namespace App\Services\Telemetry;

use App\Facades\Plugin;
use App\Models\Conference;
use App\Models\DOI;
use App\Models\Enums\SubmissionStatus;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\PluginSetting;
use App\Models\Presentation;
use App\Models\Proceeding;
use App\Models\Registration;
use App\Models\Review;
use App\Models\ScheduledConference;
use App\Models\Scopes\ConferenceScope;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class TelemetrySnapshotBuilder
{
    /**
     * @var array<int, SubmissionStatus>
     */
    private const CURRENT_SUBMISSION_STATUSES = [
        SubmissionStatus::Incomplete,
        SubmissionStatus::Queued,
        SubmissionStatus::OnReview,
        SubmissionStatus::OnPresentation,
        SubmissionStatus::Editing,
        SubmissionStatus::Published,
        SubmissionStatus::Declined,
        SubmissionStatus::Withdrawn,
    ];

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $scheduledConferences = ScheduledConference::withoutGlobalScope(ConferenceScope::class)
            ->with('meta')
            ->get();
        $pluginTelemetry = $this->pluginTelemetry($scheduledConferences);

        $coreWorkflow = [
            'submissions_by_status' => $this->submissionsByStatus(),
            'reviews_assigned' => Review::withoutGlobalScopes()->count(),
            'reviews_confirmed' => Review::withoutGlobalScopes()->whereNotNull('date_confirmed')->count(),
            'reviews_completed' => Review::withoutGlobalScopes()->whereNotNull('date_completed')->count(),
            'participants' => Participant::withoutGlobalScopes()->count(),
            'payments_total' => Payment::withoutGlobalScopes()->count(),
            'payments_paid' => Payment::withoutGlobalScopes()->whereNotNull('paid_at')->count(),
            'payments_unpaid' => Payment::withoutGlobalScopes()->whereNull('paid_at')->count(),
            'published_proceedings' => Proceeding::withoutGlobalScopes()
                ->where('published', true)
                ->whereNotNull('published_at')
                ->count(),
            'doi_count' => DOI::withoutGlobalScopes()->count(),
        ];

        if (Schema::hasTable('registrations')) {
            $coreWorkflow['registrations'] = Registration::withoutGlobalScopes()->count();
        }

        return [
            'snapshot_date' => now('UTC')->toDateString(),
            'schema_version' => 1,
            'environment' => [
                'leconfe_version' => app()->getInstalledVersion(),
                'php_version' => PHP_VERSION,
                'database_driver' => config('database.default')
                    ? \Illuminate\Support\Facades\DB::connection()->getDriverName()
                    : 'unknown',
                'plugins' => $pluginTelemetry['environment'],
            ],
            'inventory' => [
                'conferences' => Conference::withoutGlobalScopes()->count(),
                'scheduled_conferences' => $scheduledConferences->count(),
                'users' => User::withoutGlobalScopes()->count(),
            ],
            'core_workflow' => $coreWorkflow,
            'feature_adoption' => [
                'scheduled_conferences_using_registration' => $this->countEnabled($scheduledConferences, 'allow_registration'),
                'scheduled_conferences_using_submission_payment' => $this->countEnabled($scheduledConferences, 'submission_payment'),
                'scheduled_conferences_using_participant_payment' => $this->countEnabled($scheduledConferences, 'participant_payment'),
                'scheduled_conferences_using_invoice' => $this->countEnabled($scheduledConferences, 'invoice_enable'),
                'scheduled_conferences_using_receipt' => $this->countEnabled($scheduledConferences, 'receipt_enable'),
                'scheduled_conferences_using_review_modes' => $scheduledConferences
                    ->filter(fn (ScheduledConference $conference): bool => filled($conference->getMeta('review_mode')))
                    ->count(),
                'scheduled_conferences_using_presentations' => $this->countWithPresentations($scheduledConferences),
                'enabled_plugins' => $pluginTelemetry['adoption'],
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function submissionsByStatus(): array
    {
        $counts = [];

        foreach (self::CURRENT_SUBMISSION_STATUSES as $status) {
            $counts[$status->value] = Submission::withoutGlobalScopes()
                ->where('status', $status->value)
                ->count();
        }

        return $counts;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ScheduledConference>  $scheduledConferences
     */
    private function countEnabled($scheduledConferences, string $metaKey): int
    {
        return $scheduledConferences
            ->filter(fn (ScheduledConference $conference): bool => filter_var($conference->getMeta($metaKey), FILTER_VALIDATE_BOOLEAN))
            ->count();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ScheduledConference>  $scheduledConferences
     */
    private function countWithPresentations($scheduledConferences): int
    {
        return Presentation::withoutGlobalScopes()
            ->whereIn('scheduled_conference_id', $scheduledConferences->modelKeys())
            ->distinct()
            ->count('scheduled_conference_id');
    }

    /**
     * @return array<int, array{name: string, version?: string, scheduled_conferences?: int}>
     */
    private function pluginTelemetry($scheduledConferences): array
    {
        $scheduledConferenceIds = $scheduledConferences->modelKeys();
        $enabledSettings = PluginSetting::query()
            ->where('key', 'enabled')
            ->whereIn('scheduled_conference_id', [0, ...$scheduledConferenceIds])
            ->get()
            ->groupBy('plugin');
        $environment = [];
        $adoption = [];

        foreach (Plugin::getPluginsForTelemetry() as $plugin) {
            $name = $plugin['name'];
            $version = $plugin['version'];
            $scheduledConferenceCount = $this->enabledScheduledConferenceCount(
                $plugin,
                $scheduledConferences,
                $enabledSettings,
            );
            $pluginSettings = $enabledSettings->get($name, collect());
            $hasEnabledSpecificSetting = $pluginSettings->contains(fn (PluginSetting $setting): bool => $this->settingIsEnabled($setting));

            if ($name !== '' && $version !== '' && ($this->pluginEnabledAtSite($plugin, $pluginSettings) || $hasEnabledSpecificSetting || $scheduledConferenceCount > 0)) {
                $environment[] = ['name' => $name, 'version' => $version];
            }

            if ($name !== '' && $scheduledConferenceCount > 0) {
                $adoption[] = [
                    'name' => $name,
                    'scheduled_conferences' => $scheduledConferenceCount,
                ];
            }
        }

        return [
            'environment' => $environment,
            'adoption' => $adoption,
        ];
    }

    private function enabledScheduledConferenceCount(array $plugin, $scheduledConferences, $enabledSettings): int
    {
        $scheduledConferenceIds = $scheduledConferences->modelKeys();
        $settings = $enabledSettings->get($plugin['name'], collect());
        $siteSetting = $settings->first(fn (PluginSetting $setting): bool => (int) $setting->conference_id === 0 && (int) $setting->scheduled_conference_id === 0
        );
        $defaultEnabled = $siteSetting ? $this->settingIsEnabled($siteSetting) : $plugin['default_enabled'];

        if ($plugin['sitewide']) {
            return $defaultEnabled ? count($scheduledConferenceIds) : 0;
        }

        return $scheduledConferences
            ->filter(function (ScheduledConference $scheduledConference) use ($settings, $siteSetting, $defaultEnabled): bool {
                $scheduleSetting = $settings->first(function (PluginSetting $setting) use ($scheduledConference): bool {
                    return (int) $setting->scheduled_conference_id === $scheduledConference->getKey()
                        && (int) $setting->conference_id === $scheduledConference->conference_id;
                });
                $conferenceSetting = $settings->first(function (PluginSetting $setting) use ($scheduledConference): bool {
                    return (int) $setting->scheduled_conference_id === 0
                        && (int) $setting->conference_id === $scheduledConference->conference_id;
                });

                $setting = $scheduleSetting ?? $conferenceSetting ?? $siteSetting;

                return $setting ? $this->settingIsEnabled($setting) : $defaultEnabled;
            })
            ->count();
    }

    private function pluginEnabledAtSite(array $plugin, $settings): bool
    {
        $siteSetting = $settings->first(fn (PluginSetting $setting): bool => (int) $setting->conference_id === 0 && (int) $setting->scheduled_conference_id === 0
        );

        return $siteSetting ? $this->settingIsEnabled($siteSetting) : $plugin['default_enabled'];
    }

    private function settingIsEnabled(PluginSetting $setting): bool
    {
        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
    }
}
