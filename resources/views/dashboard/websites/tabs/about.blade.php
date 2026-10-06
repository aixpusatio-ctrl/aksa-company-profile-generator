<div class="space-y-6">
    <x-form.rich name="about" label="Tentang perusahaan" :value="$company->about" help="Profil perusahaan, apa yang dikerjakan, dan keunggulan Anda." />
    <x-form.textarea name="vision" label="Visi" :value="$company->vision" rows="2" />
    <x-form.rich name="mission" label="Misi" :value="$company->mission" help="Gunakan bullet list — setiap poin menjadi satu misi." />
    <x-form.rich name="history" label="Sejarah perusahaan" :value="$company->history" />
    <x-form.rich name="company_values" label="Nilai-nilai perusahaan" :value="$company->company_values" help="Gunakan bullet list, contoh: Integritas — bekerja jujur dan transparan." />
</div>
