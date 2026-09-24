@php $rows = old($field->name, $value ?: []); @endphp
<fieldset x-data="{ rows: @js(array_values($rows)) }">
    <legend class="adm-label">{{ $field->label }}</legend>
    <div class="space-y-2">
        <template x-for="(row, i) in rows" :key="i">
            <div class="flex gap-2">
                <input :name="`{{ $field->name }}[${i}][label]`" x-model="row.label" placeholder="Label" class="adm-input w-2/5">
                <input :name="`{{ $field->name }}[${i}][value]`" x-model="row.value" placeholder="Value" class="adm-input flex-1">
                <button type="button" @click="rows.splice(i, 1)" class="px-2 text-zinc-400 hover:text-red-600" aria-label="Remove row">✕</button>
            </div>
        </template>
    </div>
    <button type="button" @click="rows.push({ label: '', value: '' })" class="adm-btn-secondary mt-2 !py-1 text-xs">+ Add row</button>
</fieldset>
@include('admin.resource.fields._meta')
