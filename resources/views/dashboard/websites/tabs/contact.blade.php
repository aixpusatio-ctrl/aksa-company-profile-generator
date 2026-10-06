<div class="space-y-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-form.input name="address" label="Alamat" :value="$company->address" class="sm:col-span-2" />
        <x-form.input name="city" label="Kota" :value="$company->city" />
        <x-form.input name="province" label="Provinsi" :value="$company->province" />
        <x-form.input name="postal_code" label="Kode pos" :value="$company->postal_code" />
        <x-form.input name="country" label="Negara" :value="$company->country" />
        <x-form.input name="phone" label="Telepon" :value="$company->phone" />
        <x-form.input name="email" type="email" label="Email" :value="$company->email" />
        <x-form.input name="whatsapp" label="WhatsApp" :value="$company->whatsapp" />
        <x-form.input name="working_hours" label="Jam kerja" :value="$company->working_hours" placeholder="Senin - Jumat, 08.00 - 17.00" />
    </div>
    <div class="grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
        <x-form.input name="google_maps_url" type="url" label="Google Maps URL" :value="$company->google_maps_url" placeholder="https://maps.google.com/..." help="Link “Petunjuk arah” pada website." class="sm:col-span-2" />
        <x-form.input name="latitude" label="Latitude" :value="$company->latitude" placeholder="-6.2088" help="Opsional — untuk peta yang lebih akurat." />
        <x-form.input name="longitude" label="Longitude" :value="$company->longitude" placeholder="106.8456" />
    </div>
    <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
        <p class="flex items-center gap-2 font-semibold text-slate-900"><x-icon name="mail" class="size-4" /> Contact form</p>
        <p class="mt-1">Website Anda otomatis memiliki contact form. Pesan masuk dapat dilihat di menu <strong>Messages</strong>.</p>
    </div>
</div>
