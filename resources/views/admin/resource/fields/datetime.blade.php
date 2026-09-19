@include('admin.resource.fields._label')
<input id="f-{{ $field->name }}" name="{{ $field->name }}" type="datetime-local"
       value="{{ old($field->name, $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '') }}" class="adm-input max-w-xs">
@include('admin.resource.fields._meta')
