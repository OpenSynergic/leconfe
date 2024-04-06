<div class="flex flex-col">
    <a href="{{ app()->getCurrentConference()->getPanelUrl() }}" class="text-lg font-medium">{{ app()->getCurrentConference()->name }}</a>
    <div class="text-sm text-gray-700">Series : <span class="font-medium">{{ app()->getCurrentSerie()->title }}</span></div>
</div>