{{-- Partial reutilizable para campos de formulario --}}
<div class="field">
    <label for="{{ $name }}">{{ $label }}@if(($required ?? false)) <span aria-hidden="true">*</span>@endif</label>
    @if(($type ?? 'text') === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" class="input" placeholder="{{ $placeholder ?? '' }}">{{ old($name, $value ?? '') }}</textarea>
    @elseif(($type ?? 'text') === 'select')
        <select id="{{ $name }}" name="{{ $name }}" class="input" @if($required ?? false) required @endif>
            {{ $slot ?? '' }}
        </select>
    @else
        <input id="{{ $name }}" type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ old($name, $value ?? '') }}" class="input" placeholder="{{ $placeholder ?? '' }}" @if($required ?? false) required @endif>
    @endif
    @error($name)
        <div class="field-help error">{{ $message }}</div>
    @enderror
</div>
