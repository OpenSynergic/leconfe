<x-filament::page>
  <x-tabs>
    <x-slot:buttons>
      <x-tabs.button>Users</x-tabs.button>
      <x-tabs.button>Roles</x-tabs.button>
      <x-tabs.button>Site Access Options</x-tabs.button>
    </x-slot:buttons>
    
    <x-tabs.content>
      <livewire:tables.current-users />
    </x-tabs.content>
    <x-tabs.content>
      <livewire:tables.current-roles />
    </x-tabs.content>
    <x-tabs.content>
      rte
    </x-tabs.content>
  </x-tabs>

</x-filament::page>
