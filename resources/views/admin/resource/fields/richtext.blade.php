@include('admin.resource.fields._label')
<input id="f-{{ $field->name }}" type="hidden" name="{{ $field->name }}" value="{{ old($field->name, $value) }}">
<trix-editor input="f-{{ $field->name }}" class="trix-content adm-input min-h-40"></trix-editor>
@include('admin.resource.fields._meta')
