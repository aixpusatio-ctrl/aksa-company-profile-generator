{{--
    Default contact form markup. Templates may pass classes:
    @include('websites.partials.contact-form', [
        'inputClass' => '...', 'labelClass' => '...', 'buttonClass' => '...', 'buttonLabel' => 'Kirim'
    ])
--}}
@php
    $inputClass ??= 'w-full rounded-brand border border-black/10 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/15';
    $labelClass ??= 'mb-1.5 block text-sm font-medium';
    $buttonClass ??= 'inline-flex items-center justify-center gap-2 rounded-btn bg-primary px-6 py-3 text-sm font-semibold text-on-primary transition hover:opacity-90';
    $buttonLabel ??= 'Kirim Pesan';
@endphp
@include('websites.partials.contact-status')
<form method="POST" action="{{ $site->contactAction() }}" class="space-y-4">
    @include('websites.partials.form-guard')
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="{{ $labelClass }}" for="cf-name">Nama</label>
            <input id="cf-name" name="name" type="text" required maxlength="120" value="{{ old('name') }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="cf-email">Email</label>
            <input id="cf-email" name="email" type="email" required maxlength="150" value="{{ old('email') }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="cf-phone">Telepon</label>
            <input id="cf-phone" name="phone" type="tel" maxlength="40" value="{{ old('phone') }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="cf-subject">Subjek</label>
            <input id="cf-subject" name="subject" type="text" maxlength="150" value="{{ old('subject') }}" class="{{ $inputClass }}">
        </div>
    </div>
    <div>
        <label class="{{ $labelClass }}" for="cf-message">Pesan</label>
        <textarea id="cf-message" name="message" rows="5" required maxlength="3000" class="{{ $inputClass }}">{{ old('message') }}</textarea>
    </div>
    <button type="submit" class="{{ $buttonClass }}" @disabled(! $site->canSubmitForms())>
        {{ $buttonLabel }}
        <x-icon name="arrow-right" class="size-4" />
    </button>
</form>
