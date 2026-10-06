# ProfilKu — Company Profile Generator (Laravel SaaS)

Platform SaaS untuk membuat **website company profile** profesional tanpa coding — seperti
“Canva / website builder khusus company profile”. User mendaftar, memilih template, mengisi data
perusahaan melalui wizard, lalu website langsung online di **subdomain** (`perusahaan.platform.test`)
dan opsional di **custom domain** (`www.perusahaan.com`).

> Stack: **Laravel 13 · PHP 8.3+ · SQLite · Blade · Tailwind CSS v4 · Alpine.js · Vite**

---

## Daftar Isi

1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Environment](#environment)
6. [Database](#database)
7. [Seeder](#seeder)
8. [Dummy Accounts](#dummy-accounts)
9. [Development](#development)
10. [Production](#production)
11. [Subdomain Configuration](#subdomain-configuration)
12. [Custom Domain Architecture](#custom-domain-architecture)
13. [Template System](#template-system)
14. [Online Shop](#online-shop)
15. [Folder Structure](#folder-structure)
16. [Testing](#testing)
17. [Deployment](#deployment)

---

## Project Overview

Aplikasi memiliki tiga “zona”:

| Zona | Host | Isi |
|---|---|---|
| **Central app** | `localhost`, `platform.test`, `companyprofile.com` | Landing page, template gallery, auth, user dashboard, admin panel |
| **Tenant website (subdomain)** | `{slug}.platform.test` | Website company profile hasil generate |
| **Tenant website (custom domain)** | `www.perusahaan.com` | Website yang sama, melalui domain milik customer |

Website **tidak** di-generate sebagai HTML statis. Setiap request dirender oleh Blade theme
milik template dengan data terbaru (company + pages + menus + sections + services + products +
projects + team + ...), sehingga setiap perubahan langsung tampil.

### User flow

```
Landing Page → Register → Login → Dashboard → Create Company Profile → Choose Template
→ Fill Company Information → Customize → Add Pages → Create Menu/Submenu → Preview → Publish
→ company-name.platform.com → (optional) Custom Domain → www.company.com
```

### Admin flow

```
Admin Login → Dashboard → Users · Templates · Company Profiles · Domains · Media · Settings → Manage Entire Platform
```

---

## Features

**Frontend / Landing** — navbar (Features, Templates, Pricing, FAQ, Login, Register), hero dengan
live preview template, features, templates, how it works, example websites, pricing, FAQ, CTA, footer.

**Auth & Roles** — register, login, logout, forgot/reset password (Laravel standar, password hashing),
role `admin` & `user`, middleware `role:admin`, user suspended otomatis logout, rate limiting login.

**User Dashboard** — statistik (total/published/draft website, template dipakai, kunjungan),
kartu “My Company Profiles” (Edit / Preview / Manage), pesan terbaru, notifikasi, pencarian.

**Creation Wizard (11 step)** — Choose Template → Company Information → About → Services →
Products → Team → Projects → Contact → SEO → Preview → Publish. Bisa kembali ke step sebelumnya,
**autosave** untuk field teks.

**Konten perusahaan** — company info (logo, favicon, tahun berdiri, kontak, WhatsApp, social
media FB/IG/LinkedIn/YouTube/TikTok/X), about (rich text: about, visi, misi, sejarah, nilai),
services, products, projects/portfolio, team, testimonials, gallery — semuanya CRUD + **drag & drop reorder**.

**Custom Pages** — halaman tanpa batas (title, slug, rich content, featured image, SEO, status).

**Navigation / Sub Menu Builder** — menu & submenu (2 level) dengan drag & drop; tipe:
Anchor Section, Internal Page, External URL, Menu Group.

**Section Builder** — enable/disable/reorder section + judul/subjudul custom.

**Brand Customization** — primary/secondary color, font judul & isi, button style, border radius,
logo, favicon, hero image.

**SEO** — SEO title/description/keywords, OG title/description/image, favicon, canonical URL,
Open Graph, Twitter Card, JSON-LD Organization, `sitemap.xml`, `robots.txt` per website.

**Domain** — subdomain otomatis (bisa diganti), custom domain dengan instruksi DNS
(CNAME/A + TXT), status *Pending → Verifying → Active / Failed*, domain utama, dummy DNS verification.

**Media Library** — upload (multi), delete, search, filter (images/logos/gallery/documents),
picker di setiap field gambar; validasi tipe file, MIME, ukuran, dimensi.

**Contact form & Analytics** — pesan pengunjung tersimpan di database + notifikasi;
page view analytics tanpa cookie.

**Admin Panel** — Dashboard statistik, Users (CRUD, suspend, reset password, lihat website),
Company Profiles, Templates (CRUD, duplicate, publish/unpublish, featured), Template Categories,
Pages, Domains (verify/activate), Subscriptions, Media, Settings (nama aplikasi, logo, favicon,
default template, default SEO, storage, konfigurasi sistem), System Logs.

**50 template** (10 layout crafted + 40 template composer berbasis komponen, 11 kategori) — lihat [Template System](#template-system).

---

## Requirements

- PHP **8.3+** dengan ekstensi `pdo_sqlite`, `mbstring`, `fileinfo`, `gd` (opsional untuk dimensi gambar)
- Composer 2
- Node.js **20+** & npm
- SQLite 3

---

## Installation

```bash
git clone <repo> company-profile-generator
cd company-profile-generator

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link

npm install
npm run build
```

Jalankan:

```bash
composer run dev     # server + vite + queue + logs
# atau
php artisan serve    # http://localhost:8000
```

Buka:

- Landing page: <http://localhost:8000>
- Login: <http://localhost:8000/login>
- Website demo (subdomain): <http://example.localhost:8000>

> Browser modern (Chrome, Firefox, Edge) me-resolve `*.localhost` ke `127.0.0.1`, jadi subdomain
> langsung bekerja tanpa mengubah file hosts.

---

## Environment

Variabel penting di `.env`:

| Key | Default | Keterangan |
|---|---|---|
| `APP_URL` | `http://localhost:8000` | URL aplikasi central |
| `PLATFORM_DOMAIN` | host dari `APP_URL` | Domain induk untuk subdomain tenant (`{slug}.PLATFORM_DOMAIN`) |
| `CENTRAL_DOMAINS` | `localhost,127.0.0.1` | Host tambahan yang melayani aplikasi central |
| `PLATFORM_SCHEME` / `PLATFORM_PORT` | dari `APP_URL` | Untuk membangun URL tenant |
| `PLATFORM_CNAME_TARGET` | `cname.{PLATFORM_DOMAIN}` | Target CNAME untuk custom domain |
| `PLATFORM_SERVER_IP` | `203.0.113.10` | Target A record untuk apex domain |
| `DOMAIN_VERIFIER` | `fake` | `fake` (dev/demo, tanpa DNS) atau `dns` (production) |
| `SHOW_DEMO_CREDENTIALS` | `true` | Tampilkan akun demo di halaman login (tidak pernah di production) |
| `MEDIA_DISK` | `public` | Disk penyimpanan upload (`public`, `s3`, ...) |
| `QUEUE_CONNECTION` | `sync` | Gunakan `database`/`redis` + worker di production |
| `TRUSTED_PROXIES` | — | Daftar IP proxy/load balancer (comma separated) |

Konfigurasi lengkap: `config/platform.php` (domain, plans, media) dan
`config/website-templates.php` (layouts, sections, fonts).

---

## Database

SQLite (`database/database.sqlite`). Migrasi utama: `database/migrations/2026_10_06_100000_create_platform_tables.php`.

```
users                 role, phone, avatar, suspended_at, last_login_at
template_categories   name, slug, sort_order
templates             category, name, slug, layout, thumbnail, preview_url, status, is_featured, settings(json)
company_profiles      user, template, identitas, kontak, alamat, koordinat, social_links(json),
                      about/vision/mission/history/company_values, branding(json), SEO, status, published_at
company_sections      key, title, subtitle, is_enabled, sort_order
company_services      title, description, icon, image, sort_order
company_products      name, description, image, price, category, sort_order
company_projects      title, description, image, client, location, year, category, url, sort_order
company_team          name, position, photo, bio, linkedin, email, sort_order
company_testimonials  customer_name, company, photo, testimonial, rating, sort_order
company_gallery       image, title, description, category, sort_order
company_pages         title, slug, content, featured_image, seo_title, seo_description, status
menus                 company_profile_id, parent_id (nested), company_page_id, title, slug, type, url, sort_order, status
domains               company_profile_id, domain, type, verification_token, status, is_primary, verified_at
media                 user, company_profile, collection, disk, path, mime, size, width, height
contact_messages      company_profile, name, email, phone, subject, message, read_at
settings              key, value
activity_logs         user, subject (morph), action, description, properties, ip, user_agent
subscriptions         user, plan, status, price, trial_ends_at, starts_at, ends_at
page_views            company_profile, path, referrer, visitor_hash, viewed_on
notifications         (Laravel database notifications)
```

Semua relasi memakai foreign key dengan `cascadeOnDelete` (konten milik company) atau
`nullOnDelete` (template, kategori, halaman yang direferensikan menu).

---

## Seeder

```bash
php artisan migrate:fresh --seed
```

Membuat:

- 1 Admin + 1 Demo User (+3 user showcase)
- 10 template category
- 50 template (11 kategori) — semua published, masing-masing dengan data demo perusahaan
- Demo company **PT Example Indonesia** (`example`) lengkap: services, products, projects, team,
  testimonials, gallery, 3 custom pages, menu + submenu, 2 custom domain (active & pending),
  pesan kontak dan data analytics
- Website draft (Technology) untuk demo wizard
- 3 website showcase (Construction, Creative Agency, Executive) untuk “Example Websites”
- 2 online shop demo (milik `user@example.com`): **Ruma Living** (`ruma-living`, template Retail Modern —
  10 kategori bertingkat, 30 produk, varian, tag, kupon `WELCOME10`/`GRATISONGKIR`/`HEMAT50K`, PPN 11%)
  dan **Maison Arunika** (`maison-arunika`, template Fashion Brand — produk dengan varian ukuran/warna),
  masing-masing dengan pelanggan (password `password` untuk 3 pelanggan pertama), pesanan di berbagai
  status, dan ulasan

Konten demo per industri ada di `app/Support/DemoContent.php` (dipakai juga untuk preview template).

---

## Dummy Accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| User | `user@example.com` | `password` |

Kredensial hanya ditampilkan di halaman login bila `SHOW_DEMO_CREDENTIALS=true` **dan**
`APP_ENV` bukan `production`.

---

## Development

```bash
composer run dev        # php artisan serve + queue + pail + vite (hot reload)
npm run dev             # hanya Vite
php artisan test        # test suite
vendor/bin/pint         # code style
```

Asset Vite:

- `resources/css/app.css` + `resources/js/app.js` — aplikasi SaaS (landing, dashboard, admin)
- `resources/css/site.css` + `resources/js/site.js` — website tenant (dipisah agar ringan)

### Mencoba alur custom domain secara lokal

1. Login sebagai `user@example.com` → website **PT Example Indonesia** → **Domain**.
2. Tambahkan domain, misal `www.perusahaan-saya.test` → instruksi DNS tampil.
3. Klik **Verify** → (fake verifier) status menjadi **Active**. Domain yang mengandung kata
   `fail` akan menjadi **Failed** untuk mendemokan alur gagal.
4. Tambahkan `127.0.0.1 www.perusahaan-saya.test` ke `/etc/hosts`, lalu buka
   `http://www.perusahaan-saya.test:8000`.

---

## Production

1. `APP_ENV=production`, `APP_DEBUG=false`, `SHOW_DEMO_CREDENTIALS=false`.
2. `PLATFORM_DOMAIN=companyprofile.com`, `APP_URL=https://companyprofile.com`, `PLATFORM_SCHEME=https`.
3. `DOMAIN_VERIFIER=dns`, `PLATFORM_CNAME_TARGET=cname.companyprofile.com`, `PLATFORM_SERVER_IP=<IP server>`.
4. `QUEUE_CONNECTION=database` (atau redis) dan jalankan worker: `php artisan queue:work`.
5. `MEDIA_DISK=s3` bila memakai object storage.
6. Optimasi:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Subdomain Configuration

Tenant diidentifikasi berdasarkan **hostname**:

```
Request Host → ResolveCompanyDomain → (custom domain? → subdomain?) → Company Profile
            → ResolveTenant (published & owner aktif) → WebsiteRendererService → Template Blade
```

- Route tenant didaftarkan dengan `Route::domain('{tenant_host}')` dan pola regex yang
  **mengecualikan** central domains, sehingga `/` di `perusahaan.platform.test` merender website
  tenant, sedangkan `/` di `platform.test` merender landing page. Kompatibel dengan `route:cache`.
- Route central dibungkus middleware `central` (404 bila diakses dari host tenant).
- Subdomain dicadangkan (www, admin, api, mail, ...) tidak bisa dipakai tenant
  (`config/platform.php → reserved_subdomains`).

DNS & web server:

```
*.companyprofile.com    A     <IP server>
companyprofile.com      A     <IP server>
cname.companyprofile.com A    <IP server>
```

Nginx (satu server block untuk semua host):

```nginx
server {
    listen 80;
    server_name companyprofile.com *.companyprofile.com _;   # "_" menerima custom domain
    root /var/www/company-profile-generator/public;
    index index.php;

    # robots.txt & sitemap.xml dibuat dinamis per website
    location = /robots.txt { try_files /__none__ /index.php?$query_string; }

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

> Catatan: hapus `public/robots.txt` (bawaan skeleton Laravel) atau gunakan rule di atas, agar
> `robots.txt` dinamis per website tidak tertimpa file statis.

Local dengan Laravel Herd/Valet: `PLATFORM_DOMAIN=platform.test`, `APP_URL=http://platform.test`,
lalu `valet link platform` — wildcard `*.platform.test` otomatis aktif.

---

## Custom Domain Architecture

```
User → Settings → Domain → Tambah "www.example.com"
   ↓
DomainService::add()            normalisasi, validasi format, tolak domain platform/duplikat,
                                token verifikasi, type: subdomain (CNAME) / apex (A)
   ↓
Instruksi DNS                   CNAME www → cname.companyprofile.com   (atau A @ → IP server)
                                TXT  _cpg-verify.www → cpg-xxxxxxxx
   ↓
Verify → status "verifying" → VerifyDomainJob (queue)
   ↓
DomainVerifier (interface)      FakeDomainVerifier  (dev/demo — tanpa DNS sungguhan)
                                DnsDomainVerifier   (production — dns_get_record TXT + CNAME/A)
   ↓
Active  → event DomainVerified → activity log + notifikasi user
Failed  → failure_reason ditampilkan
```

`ResolveCompanyDomain` mencoba domain persis, lalu varian dengan/tanpa `www.`, lalu subdomain
platform. Hasil lookup di-cache 5 menit dan di-invalidate saat domain/slug berubah.
`www.company.com` dan `company.platform.com` menampilkan company profile yang sama; canonical URL
memakai domain utama yang aktif. Untuk HTTPS custom domain di production, gunakan reverse proxy
dengan on-demand TLS (mis. Caddy `on_demand_tls`, Cloudflare for SaaS, atau Traefik + Let’s Encrypt).

---

## Template System

- **Layout** = Blade theme di `resources/views/websites/templates/{layout}/`
  (`layout`, `home`, `page`, dan `sections/{hero,about,services,products,projects,team,testimonials,gallery,cta,contact}`).
  Setiap layout punya header/navigasi, hero, tipografi, susunan section, gaya kartu, portfolio,
  CTA dan footer sendiri.
- **Template** (tabel `templates`, dikelola admin) = referensi ke sebuah layout + default branding
  (warna, font, button style, radius) + default urutan section. Admin dapat membuat varian baru
  tanpa coding (contoh: *Corporate Emerald*, *Healthcare Clinic*, *Education Campus*).
- **Company branding** menimpa default template. Nilai brand diterjemahkan menjadi CSS variables
  (`App\Support\Website\Brand`) yang dipakai token Tailwind `bg-primary`, `text-on-primary`,
  `font-heading`, `rounded-brand`, `rounded-btn`, ... di `resources/css/site.css`.

| Layout | Karakter |
|---|---|
| Corporate | Top-bar kontak, hero split, kartu bergaris, filter portofolio |
| Modern Business | Navbar transparan, hero gradien, carousel testimoni |
| Technology | Dark mode, grid & glow, bento layout, label monospace |
| Construction | Industrial, uppercase tebal, aksen kuning, masonry proyek |
| Manufacturing | Katalog produk bertab, proses produksi bernomor, sertifikasi |
| Consulting | Serif elegan, whitespace, layanan editorial, slider kutipan |
| Creative Agency | Tipografi raksasa, marquee, grid asimetris, menu off-canvas |
| Professional Services | Booking card, area praktik bertab, sidebar info |
| Minimal | Hitam-putih, satu kolom, tipografi sebagai elemen utama |
| Executive | Navy & emas, serif Cormorant, baris proyek sinematik |

Komponen render:

- `WebsiteRendererService` — merakit data (sections aktif yang punya konten, menu tree,
  halaman, SEO) dan memilih view.
- `SiteContext` (`$site`) — semua URL di template dibuat lewat objek ini, sehingga theme yang sama
  dipakai untuk website live, preview draft pemilik, dan preview template dengan data demo.
- `MenuLink` — item navigasi yang sudah di-resolve (anchor/page/url/group, max 2 level).

### Menambah layout baru

1. Salin `resources/views/websites/templates/corporate` ke folder baru, mis. `healthcare`.
2. Daftarkan di `config/website-templates.php → layouts` (nama, kategori, deskripsi, defaults).
3. Admin → Templates → Create Template → pilih layout `healthcare`.
4. `npm run build`.

---

## Online Shop

Setiap company profile bisa mengaktifkan toko online native (Dashboard → website → **Toko Online** →
toggle ON). Toko memakai navbar, footer, warna, font dan design system template yang sama, sehingga
terasa sebagai bagian website — bukan aplikasi terpisah. Saat aktif, menu website otomatis mendapat
**Shop** (dengan sub-menu kategori) dan **Keranjang**.

| URL (tenant host) | Isi |
|---|---|
| `/shop` | Homepage toko (section builder: hero, featured, kategori, best seller, new, sale, brand, testimoni, newsletter) |
| `/shop/products`, `/shop/category/{slug}` | Katalog + pencarian, filter (kategori, harga, stok, rating, brand, atribut) & sorting |
| `/shop/product/{slug}` | Detail produk: galeri, varian, qty, add to cart / buy now / WhatsApp / contact, spesifikasi, ulasan, related, recently viewed, JSON-LD |
| `/shop/cart`, `/shop/checkout` | Keranjang (page/drawer sesuai template) & checkout multi-step, kupon, ongkir, pajak |
| `/shop/order/{number}?token=…`, `/shop/order/track` | Halaman pesanan (butuh token rahasia atau login pemilik) & lacak pesanan |
| `/account/*` | Akun pelanggan: profil, pesanan, wishlist, alamat, ulasan (guard `customer`, terpisah dari user/admin) |

**Seller dashboard** (`/dashboard/shop`, `/dashboard/websites/{company}/shop/*`): overview + grafik,
produk (gambar, varian, tag, CTA, related, SEO), kategori & tag, pesanan (status, pembayaran, resi,
invoice), pelanggan, kupon, inventori (stok tersedia/dipesan, penyesuaian, log), ulasan (moderasi),
pengiriman, pembayaran, diskon & pajak, pengaturan. **Admin** (`/admin/shop/*`): semua toko, produk,
pesanan dan pelanggan.

Arsitektur (`app/Services/Shop`):

- `CartService` — keranjang guest (token di session) / customer, digabung saat login. Total **selalu
  dihitung ulang dari database**; harga dari browser diabaikan.
- `CheckoutService` — membuat order dengan snapshot item/alamat, reservasi stok atomik
  (`UPDATE … WHERE stock - reserved_stock >= qty`), redeem kupon atomik, WhatsApp checkout (pesan
  pre-filled, dikirim sendiri oleh pembeli).
- `OrderService` — transisi status (pending → confirmed → processing → packed → shipped → completed,
  cancelled/refunded) beserta efek stok & kupon, timeline, resi.
- `InventoryService`, `CouponService`, `TaxService`, `ReviewService` (verified purchase), `CatalogService`.
- `Shipping/ShippingProviderInterface` + `ManualShippingProvider` (pickup, flat, free, custom per kota) dan
  `Payment/PaymentProviderInterface` + `ManualPaymentProvider` (transfer bank, COD). Provider baru
  (RajaOngkir, Midtrans, …) cukup mengimplementasikan interface dan didaftarkan di `AppServiceProvider`.
  Data kartu kredit tidak pernah disimpan.
- Batas tenant: semua tabel toko memiliki `company_profile_id`; middleware `shop` memastikan toko aktif
  dan sesi customer milik toko yang sama.
- Kartu produk: 8 gaya (`classic`, `minimal`, `luxury`, `bento`, `horizontal`, `image`, `compact`, `modern`)
  di `resources/views/components/shop/product-card`, dipilih lewat token `product_card` design system
  template.

---

## Folder Structure

```
app/
├── Events/                 CompanyProfilePublished, DomainVerified, ContactMessageReceived
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          Dashboard, Users, CompanyProfiles, Templates, Categories, Pages,
│   │   │                   Domains, Subscriptions, Media, Settings, ActivityLog
│   │   ├── Auth/           Register, Login, Forgot/Reset password
│   │   ├── Dashboard/      Websites, Wizard, ProfileEditor, Content, Pages, Menus, Sections,
│   │   │                   Domains, Messages, Media, Settings, Preview, Notifications
│   │   ├── Site/           Public tenant website
│   │   └── Api/            Template list & subdomain availability
│   ├── Middleware/         ResolveCompanyDomain, ResolveTenant, EnsureCentralDomain,
│   │                       EnsureUserHasRole, EnsureUserIsActive
│   └── Requests/
├── Jobs/                   VerifyDomainJob
├── Listeners/              activity log + notifications
├── Models/                 + Concerns (BelongsToCompany, HasMediaUrls)
├── Notifications/
├── Policies/               CompanyProfilePolicy, DomainPolicy, MediaPolicy
├── Services/               CompanyProfileService, TemplateService, DomainService (+ Domains/*Verifier),
│                           WebsiteRendererService, MediaService, MenuService, SeoService,
│                           AnalyticsService, SettingService
└── Support/                ContentTypes, ProfileTabs, DemoContent, HtmlSanitizer, Icons,
                            Activity, CurrentTenant, MediaUrl, Website/{SiteContext,MenuLink,Brand}

resources/
├── views/
│   ├── admin/              admin panel
│   ├── auth/               login, register, forgot/reset password
│   ├── components/         layouts (app, admin, guest, marketing), form, site, ui
│   ├── dashboard/          user dashboard, wizard, editor
│   ├── landing/            landing page
│   ├── templates/          template gallery & preview
│   └── websites/           partials + templates/{layout} (10 themes)
├── css/                    app.css, site.css
└── js/                     app.js, site.js

routes/  web.php (tenant + central), api.php
database/ migrations/, seeders/, factories/
config/  platform.php, website-templates.php
```

---

## Testing

```bash
php artisan test
```

Test suite (`tests/Feature`, `tests/Unit`) mencakup: Registration, Login, Authorization,
Admin Access, User Isolation, Create Company Profile, Template Selection, Company Data,
Content CRUD, Menu Builder, Custom Pages, Domain Resolver, Custom Domain, Publishing,
Public Website (SEO, sitemap, contact form), Templates, Admin panel dan Seeder.
Test memakai SQLite in-memory (lihat `phpunit.xml`).

---

## Deployment

Checklist singkat (VPS + Nginx + PHP-FPM):

1. Server: PHP 8.3 FPM, Nginx, Node 20 (untuk build), Supervisor.
2. DNS: wildcard `*.companyprofile.com` & `cname.companyprofile.com` → IP server.
3. Clone repo, set `.env` production (lihat [Production](#production)).
4. `composer install --no-dev -o`, `npm ci && npm run build`, `php artisan migrate --force`,
   `php artisan storage:link`, cache config/route/view.
5. Nginx server block wildcard (lihat [Subdomain Configuration](#subdomain-configuration)).
6. SSL: wildcard certificate untuk `*.companyprofile.com` (DNS challenge) + on-demand TLS
   untuk custom domain.
7. Supervisor: `php artisan queue:work --tries=3` untuk verifikasi domain.
8. Scheduler (opsional): `* * * * * php artisan schedule:run`.
9. Backup file SQLite secara berkala (atau migrasi ke MySQL/PostgreSQL — cukup ubah `DB_CONNECTION`).

---

## License

MIT
