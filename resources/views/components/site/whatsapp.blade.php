@php($href = App\Support\Site::whatsappUrl() ?? url(config('site.callback_url')))
<a href="{{ $href }}" @if (str_starts_with($href, 'https://wa.me')) target="_blank" rel="noopener" @endif data-track="whatsapp_float"
   class="fixed right-4 bottom-4 z-[60] grid size-14 place-items-center rounded-full bg-whatsapp text-white shadow-[0_8px_26px_rgba(0,0,0,.28)] transition-colors hover:bg-whatsapp-dark sm:right-[26px] sm:bottom-[26px] sm:size-[60px]">
    <span class="sr-only">Chat with us on WhatsApp</span>
    <x-site.whatsapp-icon class="size-[30px]" />
</a>
