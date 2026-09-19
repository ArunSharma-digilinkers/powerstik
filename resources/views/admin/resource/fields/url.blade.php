@include('admin.resource.fields._label')
<input id="f-{{ $field->name }}" name="{{ $field->name }}" type="url" value="{{ old($field->name, $value) }}" class="adm-input">
@include('admin.resource.fields._meta')
