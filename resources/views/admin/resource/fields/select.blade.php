@include('admin.resource.fields._label')
<select id="f-{{ $field->name }}" name="{{ $field->name }}" class="adm-input">
    @unless (in_array('required', $field->rules, true))<option value="">—</option>@endunless
    @foreach ($field->options as $key => $label)
        <option value="{{ $key }}" @selected((string) old($field->name, $value) === (string) $key)>{{ $label }}</option>
    @endforeach
</select>
@include('admin.resource.fields._meta')
