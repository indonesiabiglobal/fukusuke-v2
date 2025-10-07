# Choices.js with Livewire Integration Fix

## Problem
When using Choices.js dropdowns with Livewire, the dropdown loses its initialization after Livewire re-renders the DOM. This happens when properties like `divisionId` change, triggering a Livewire update.

## Solution
The solution involves three key components:

### 1. Wire:ignore Directive
Add `wire:ignore` to the select element's wrapper to prevent Livewire from re-rendering that specific element:
```blade
<div class="mb-1" wire:ignore>
    <select id="machineSelect" class="form-control" wire:model.defer="machineId" data-choices>
        <!-- options -->
    </select>
</div>
```

### 2. Initialization Function
Create a function to properly initialize/reinitialize Choices.js:
```javascript
function initMachineSelect() {
    const machineSelect = document.getElementById('machineSelect');
    
    if (machineSelect) {
        // Destroy existing instance if it exists
        if (machineSelect.choicesInstance) {
            machineSelect.choicesInstance.destroy();
        }
        
        // Initialize new Choices instance
        const choices = new Choices(machineSelect, {
            searchEnabled: true,
            removeItemButton: true,
            shouldSort: false,
            searchFields: ['label']
        });
        
        // Store instance for later destruction
        machineSelect.choicesInstance = choices;
        
        // Sync changes back to Livewire
        machineSelect.addEventListener('change', function(e) {
            @this.set('machineId', e.target.value);
        });
    }
}
```

### 3. Livewire Hooks
Use Livewire hooks to reinitialize after DOM updates:
```javascript
// Initialize on component load
document.addEventListener('livewire:init', () => {
    initMachineSelect();
});

// Reinitialize after Livewire updates the DOM
Livewire.hook('morph', ({ component, cleanup }) => {
    cleanup(() => {
        setTimeout(() => {
            initMachineSelect();
        }, 100);
    });
});
```

## Key Points
- **wire:ignore**: Prevents Livewire from touching the select element
- **Destroy before reinit**: Always destroy existing Choices instance before creating a new one
- **Event listener**: Use `@this.set()` to sync changes back to Livewire
- **morph hook**: Reinitialize after DOM morphing (when properties change)
- **Timeout**: Small delay ensures DOM is fully updated before reinitializing

## Files Modified
- `resources/views/livewire/jam-kerja/check-list.blade.php`

## References
- Livewire Documentation: https://livewire.laravel.com/docs/javascript
- Choices.js Documentation: https://github.com/Choices-js/Choices
