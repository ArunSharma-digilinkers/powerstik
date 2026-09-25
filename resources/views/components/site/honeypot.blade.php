@props(['bag' => 'default'])
{{-- Bots fill this in; people never see it. A filled one files the lead as spam
     (App\Support\LeadIntake), so nothing is silently thrown away. --}}
<div aria-hidden="true" class="absolute left-[-9999px] h-px w-px overflow-hidden">
    <label for="website-{{ $bag }}">Leave this field empty</label>
    <input id="website-{{ $bag }}" type="text" name="website" tabindex="-1" autocomplete="off" value="">
</div>
