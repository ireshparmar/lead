<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save" method="POST">
        @csrf  <!-- Ensure CSRF token is present -->
        {{ $this->form }}




    <div class="flex">
        <div class="p-1">
            <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
        </div>
        <div class="p-1">
            <x-filament-panels::form.actions
            :actions="$this->downloadSampleFile()"
        />
        </div>
        @if(session('file'))
        <div class="p-1">
    <x-filament-panels::form.actions
    :actions="$this->downloadErrorFileAction()"
/>
    </div>
    @endif
</div>
    </x-filament-panels::form>
</x-filament-panels::page>
