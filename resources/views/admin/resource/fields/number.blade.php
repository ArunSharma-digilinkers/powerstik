@include('admin.resource.fields._label')
<input id="f-{{ $field->name }}" name="{{ $field->name }}" type="number" value="{{ old($field->name, $value) }}" class="adm-input" step="1">
@include('admin.resource.fields._meta')
