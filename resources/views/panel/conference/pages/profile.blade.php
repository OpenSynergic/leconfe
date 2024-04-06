<x-filament-panels::page>
    <div class="flex flex-col gap-y-6" x-data="{ activeTab: 'information' }">
        <x-filament::tabs class="!ml-0">
            <x-filament::tabs.item alpine-active="activeTab === 'information'"
                x-on:click="activeTab = 'information'">
                Information
            </x-filament::tabs.item>
            @if(app()->getCurrentConference())
            <x-filament::tabs.item alpine-active="activeTab === 'roles'"
                x-on:click="activeTab = 'roles'">
                Roles
            </x-filament::tabs.item>
            @endif
            <x-filament::tabs.item alpine-active="activeTab === 'notifications'"
                x-on:click="activeTab = 'notifications'">
                Notifications
            </x-filament::tabs.item>

        </x-filament::tabs>

        <div x-show="activeTab === 'information'">
            <form wire:submit="submitInformationForm" class="space-y-4">
                {{ $this->informationForm }}

                <x-filament::button type="submit">
                    Save
                </x-filament::button>                
            </form>
        </div>
        @if(app()->getCurrentConference())
        <div x-show="activeTab === 'roles'">
            <form wire:submit="submitRolesForm" class="space-y-4">
                {{ $this->rolesForm }}

                <x-filament::button type="submit">
                    Save
                </x-filament::button>                
            </form>
        </div>
        @endif
        <div x-show="activeTab === 'notifications'">
            <form wire:submit="submitNotificationsForm" class="space-y-4">
                {{ $this->notificationForm }}

                <x-filament::button type="submit">
                    Save
                </x-filament::button>                
            </form>
        </div>
    </div>
</x-filament-panels::page>
