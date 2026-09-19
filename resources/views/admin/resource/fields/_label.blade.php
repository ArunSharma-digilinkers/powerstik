<label for="f-{{ $field->name }}" class="adm-label">{{ $field->label }}@if (in_array('required', $field->rules, true))<span class="text-brand-500"> *</span>@endif</label>
