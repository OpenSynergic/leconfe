<div class="flex ml-auto">
    <x-filament-actions::group :actions="[
        $this->scheduleAction,
        $this->openAction,
        $this->closeAction,
    ]" button="true"/>
    <x-filament-actions::modals />
</div>
