@include('admin.resource.fields._label')
<textarea id="f-{{ $field->name }}" name="{{ $field->name }}" rows="3" class="adm-input">{{ old($field->name, $value) }}</textarea>
@include('admin.resource.fields._meta')
