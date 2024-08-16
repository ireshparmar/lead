<x-filament-panels::page @class([
    'fi-resource-edit-record-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    'fi-resource-record-' . $record->getKey(),
])>
    @capture($form)
        <x-filament-panels::form id="form" :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="save">
            {{ $this->form }}

            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
        </x-filament-panels::form>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    @if (!$hasCombinedRelationManagerTabsWithContent || !count($relationManagers))
        {{ $form() }}
    @endif

    <x-filament::section>

        <div class="w-full">
            <ol class="flex items-center space-x-2">
                <!-- Active Step 1 -->
                <li class="relative flex-1 text-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full">
                        1
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center -z-10">
                        <div class="w-full h-0.5 bg-green-600"></div>
                    </div>
                    <div class="mt-2">
                        <h3 class="font-medium text-green-600">User Info</h3>
                        <p class="text-sm text-gray-500">Step details here</p>
                    </div>
                </li>

                <!-- Inactive Step 2 -->
                <li class="relative flex-1 text-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full">
                        2
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center -z-10">
                        <div class="w-full h-0.5 bg-gray-300"></div>
                    </div>
                    <div class="mt-2">
                        <h3 class="font-medium text-gray-500">Company Info</h3>
                        <p class="text-sm text-gray-500">Step details here</p>
                    </div>
                </li>

                <!-- Inactive Step 3 -->
                <li class="relative flex-1 text-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full">
                        3
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center -z-10">
                        <div class="w-full h-0.5 bg-gray-300"></div>
                    </div>
                    <div class="mt-2">
                        <h3 class="font-medium text-gray-500">Payment Info</h3>
                        <p class="text-sm text-gray-500">Step details here</p>
                    </div>
                </li>

                <!-- Inactive Step 4 -->
                <li class="relative flex-1 text-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full">
                        4
                    </div>
                    <div class="mt-2">
                        <h3 class="font-medium text-gray-500">Review</h3>
                        <p class="text-sm text-gray-500">Step details here</p>
                    </div>
                </li>
            </ol>
        </div>







    </x-filament::section>

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers :active-locale="isset($activeLocale) ? $activeLocale : null" :active-manager="$this->activeRelationManager ??
            ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))" :content-tab-label="$this->getContentTabLabel()"
            :content-tab-icon="$this->getContentTabIcon()" :content-tab-position="$this->getContentTabPosition()" :managers="$relationManagers" :owner-record="$record" :page-class="static::class">
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    {{ $form() }}
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
