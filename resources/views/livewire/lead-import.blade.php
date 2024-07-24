<div>
    <form wire:submit.prevent="import">
        <div>
            <label for="file">Choose Excel File:</label>
            <input type="file" wire:model="file"  class="form-control @error('file') is-invalid @enderror">
            @error('file') <span class="error">{{ $message }}</span> @enderror
        </div>

    </form>
</div>
