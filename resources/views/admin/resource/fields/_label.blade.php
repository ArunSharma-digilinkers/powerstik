<label for="f-{{ $field->name }}" class="adm-label">{{ $field->label }}@if (in_array('required', $field->rules, true))<span class="text-red-600"> *</span>@endif</label>
