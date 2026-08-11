<div class="h-screen w-full max-w-4xl mx-auto flex flex-col" x-cloak>
    <div class="space-y-4 pt-16 pb-20 px-1 sm:px-4">
        <div class="avatar w-full">
            <div class="w-24 rounded-full mx-auto">
                <img src="{{ asset('logo.png') }}" />
            </div>
        </div>
        {{-- <p class="font-semibold ">Leconfe v{{ app()->getCodeVersion() }}</p> --}}
    </div>
    <div class="space-y-4 px-1 sm:px-4">
        <div class="text-center">
            <h1 class="text-2xl font-bold">Installation Successful</h1>
            <p class="text-lg">You have successfully installed Leconfe</p>
        </div>
        @if ($showTelemetryNotice)
            <div class="rounded-lg border border-primary/30 bg-primary/5 p-4 text-sm space-y-3">
                <h2 class="text-lg font-semibold">{{ __('general.telemetry_upgrade_heading') }}</h2>
                <p>{{ __('general.telemetry_upgrade_notice') }}</p>
                <p>{{ __('general.telemetry_upgrade_data') }}</p>
                <p>{{ __('general.telemetry_status_line', ['status' => $telemetryStatus['status'] ?? __('general.telemetry_not_sent_status'), 'time' => ! empty($telemetryStatus['attempted_at']) ? ' at '.$telemetryStatus['attempted_at'] : '']) }}</p>
                <p>{{ __('general.telemetry_upgrade_settings') }}</p>
            </div>
        @endif
        <div class="text-center">
            <a href="{{ route('filament.administration.pages.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </div>
</div>
