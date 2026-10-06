<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Shop\Cart;
use App\Models\Shop\Customer;
use App\Models\Shop\Product;
use App\Models\Template;
use App\Models\User;
use App\Services\CompanyProfileService;
use App\Services\Shop\CheckoutService;
use App\Services\Shop\InventoryService;
use App\Services\Shop\OrderService;
use App\Services\Shop\ProductService;
use App\Services\Shop\ReviewService;
use App\Services\Shop\ShopService;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

/**
 * Demo online shops (php artisan migrate:fresh --seed):
 *  - "Ruma Living" (Retail Modern template): 10 categories (nested),
 *    30 products, variants, customers, orders in every status, reviews.
 *  - "Maison Arunika" (Fashion Brand template): apparel with size/colour
 *    variants — shows the same shop in a completely different design.
 */
class ShopDemoSeeder extends Seeder
{
    public function __construct(
        private readonly CompanyProfileService $companies,
        private readonly ShopService $shop,
        private readonly ProductService $products,
        private readonly InventoryService $inventory,
        private readonly CheckoutService $checkout,
        private readonly OrderService $orders,
        private readonly ReviewService $reviews,
    ) {}

    public function run(): void
    {
        $user = User::query()->where('email', 'user@example.com')->firstOrFail();

        Event::fakeFor(function () use ($user) {
            $ruma = $this->website($user, 'retail-modern', 'ruma-living');
            $this->seedHomeLiving($ruma, $user);
            $this->seedCustomersAndOrders($ruma, 24);

            $maison = $this->website($user, 'fashion-brand', 'maison-arunika');
            $this->seedFashion($maison, $user);
            $this->seedCustomersAndOrders($maison, 10);
        });
    }

    private function website(User $user, string $templateSlug, string $slug): CompanyProfile
    {
        $template = Template::query()->where('slug', $templateSlug)->firstOrFail();
        $demo = DemoContent::for($template->demoKey());

        $company = $this->companies->create($user, ['name' => $demo['company']['name'], 'slug' => $slug], $template);
        $company->update(collect($demo['company'])->except('slug')->all() + ['wizard_step' => 11]);

        foreach (['services', 'testimonials', 'gallery', 'team'] as $relation) {
            foreach ($demo[$relation] as $item) {
                $company->{$relation}()->create($item);
            }
        }
        foreach ($demo['pages'] as $page) {
            $company->pages()->create($page);
        }

        $company->update(['status' => CompanyProfile::STATUS_PUBLISHED, 'published_at' => now()->subDays(45)]);
        $this->shop->enable($company);
        $company->shopSetting->update([
            'description' => $demo['company']['description'],
            'options' => array_merge($company->shopSetting->options ?? [], [
                'shop_title' => $templateSlug === 'fashion-brand' ? 'The Collection' : 'Belanja Kebutuhan Rumah',
                'shop_subtitle' => $demo['company']['tagline'],
                'banner_image' => 'https://picsum.photos/seed/'.$slug.'-shop-banner/1600/900',
            ]),
        ]);
        $company->taxes()->create(['name' => 'PPN', 'rate' => 11, 'inclusive' => true, 'is_active' => true]);
        $company->coupons()->createMany([
            ['code' => 'WELCOME10', 'description' => 'Diskon 10% untuk pelanggan baru', 'type' => 'percentage', 'value' => 10, 'max_discount' => 150000, 'usage_limit_per_customer' => 1, 'status' => 'active'],
            ['code' => 'GRATISONGKIR', 'description' => 'Gratis ongkir min. belanja Rp 300.000', 'type' => 'free_shipping', 'value' => 0, 'min_purchase' => 300000, 'status' => 'active'],
            ['code' => 'HEMAT50K', 'description' => 'Potongan Rp 50.000', 'type' => 'fixed', 'value' => 50000, 'min_purchase' => 500000, 'usage_limit' => 100, 'ends_at' => now()->addMonths(2), 'status' => 'active'],
        ]);

        return $company;
    }

    private function seedHomeLiving(CompanyProfile $company, User $user): void
    {
        $tree = [
            'Furnitur' => ['Sofa & Kursi', 'Meja', 'Penyimpanan'],
            'Dekorasi' => ['Vas & Tanaman', 'Wall Art'],
            'Dapur & Makan' => ['Peralatan Saji'],
            'Pencahayaan' => [],
            'Tekstil' => [],
        ];
        $categories = $this->categories($company, $tree);

        $catalog = [
            // name, category, price, sale, stock, brand, short description, options
            ['Sofa Linen 3 Dudukan Arka', 'Sofa & Kursi', 7490000, 6290000, 8, 'Ruma Home', 'Sofa berbingkai kayu jati dengan dudukan linen yang lembut dan mudah dirawat.', ['Warna' => 'Sand, Olive, Charcoal']],
            ['Kursi Lounge Rotan Sela', 'Sofa & Kursi', 2350000, null, 15, 'Ruma Home', 'Kursi santai anyaman rotan alami dengan bantal duduk empuk.', []],
            ['Armchair Bouclé Nara', 'Sofa & Kursi', 3150000, null, 3, 'Ruma Home', 'Armchair bertekstur bouclé dengan kaki kayu oak.', ['Warna' => 'Ivory, Terracotta']],
            ['Meja Makan Jati Solid Laras', 'Meja', 8900000, null, 4, 'Kayu Laras', 'Meja makan 6 kursi dari kayu jati solid finishing natural.', ['Ukuran' => '160 cm, 200 cm']],
            ['Meja Kopi Travertine Batu', 'Meja', 4250000, 3790000, 6, 'Ruma Home', 'Meja kopi bulat dengan top travertine dan rangka besi hitam.', []],
            ['Meja Samping Lipat Kiri', 'Meja', 690000, null, 30, 'Ruma Basics', 'Meja samping ringkas yang bisa dilipat, cocok untuk ruang kecil.', []],
            ['Rak Buku Modular Petak', 'Penyimpanan', 2190000, null, 12, 'Ruma Basics', 'Rak modular 5 tingkat yang bisa disusun sesuai kebutuhan.', ['Warna' => 'Natural, Putih']],
            ['Lemari Bufet Rattan Tirta', 'Penyimpanan', 5490000, null, 2, 'Kayu Laras', 'Bufet panel rotan dengan 4 pintu dan rak dalam yang luas.', []],
            ['Kotak Penyimpanan Anyam Set 3', 'Penyimpanan', 389000, 329000, 60, 'Ruma Basics', 'Set 3 keranjang anyaman pandan untuk merapikan rumah.', []],
            ['Vas Keramik Glasir Ombak', 'Vas & Tanaman', 289000, null, 40, 'Tanah Liat Studio', 'Vas keramik buatan tangan dengan glasir bertekstur ombak.', ['Ukuran' => 'Kecil, Sedang, Besar']],
            ['Tanaman Artifisial Monstera 120 cm', 'Vas & Tanaman', 899000, null, 18, 'Ruma Home', 'Monstera artifisial realistis dengan pot semen.', []],
            ['Pot Terracotta Bergaris', 'Vas & Tanaman', 159000, null, 0, 'Tanah Liat Studio', 'Pot terracotta berlubang drainase dengan motif garis.', []],
            ['Lukisan Kanvas Abstrak Senja', 'Wall Art', 1250000, null, 7, 'Galeri Ruma', 'Lukisan kanvas abstrak bernuansa hangat, siap gantung.', ['Ukuran' => '60x90 cm, 90x120 cm']],
            ['Cermin Bulat Bingkai Rotan', 'Wall Art', 749000, 599000, 14, 'Ruma Home', 'Cermin dinding bulat diameter 70 cm dengan bingkai rotan.', []],
            ['Set Piring Stoneware 12 pcs', 'Peralatan Saji', 1150000, null, 20, 'Tanah Liat Studio', 'Piring makan, piring kecil dan mangkuk stoneware untuk 4 orang.', ['Warna' => 'Moss, Salt, Clay']],
            ['Talenan Kayu Akasia Besar', 'Peralatan Saji', 245000, null, 50, 'Kayu Laras', 'Talenan sekaligus papan saji dari kayu akasia.', []],
            ['Gelas Kaca Bergelombang Set 4', 'Peralatan Saji', 219000, 179000, 35, 'Ruma Basics', 'Set 4 gelas kaca bergelombang 350 ml.', []],
            ['Teko Teh Keramik Matte', 'Dapur & Makan', 329000, null, 4, 'Tanah Liat Studio', 'Teko teh 900 ml dengan saringan stainless.', []],
            ['Lampu Gantung Rotan Kubah', 'Pencahayaan', 1350000, null, 9, 'Cahaya Rumah', 'Lampu gantung anyaman rotan yang memberi cahaya hangat.', ['Ukuran' => '40 cm, 60 cm']],
            ['Lampu Meja Keramik Bulan', 'Pencahayaan', 690000, 549000, 16, 'Cahaya Rumah', 'Lampu meja dengan kaki keramik dan kap linen.', []],
            ['Lampu Lantai Tripod Kayu', 'Pencahayaan', 1490000, null, 5, 'Cahaya Rumah', 'Lampu lantai tripod kayu oak dengan kap kain.', []],
            ['Lampu Dinding Kuningan Arah', 'Pencahayaan', 890000, null, 11, 'Cahaya Rumah', 'Lampu dinding kuningan dengan lengan yang bisa diputar.', []],
            ['Sarung Bantal Linen Polos', 'Tekstil', 149000, null, 80, 'Ruma Basics', 'Sarung bantal 45x45 cm dari linen premium, resleting tersembunyi.', ['Warna' => 'Sand, Olive, Rust, Navy']],
            ['Selimut Rajut Katun', 'Tekstil', 459000, 389000, 25, 'Tenun Ruma', 'Selimut rajut katun 130x170 cm yang lembut.', []],
            ['Karpet Jute Anyam 160x230', 'Tekstil', 1690000, null, 6, 'Tenun Ruma', 'Karpet serat jute anyam tangan untuk ruang keluarga.', []],
            ['Taplak Meja Batik Cap', 'Tekstil', 279000, null, 30, 'Tenun Ruma', 'Taplak meja katun dengan motif batik cap tradisional.', []],
            ['Gorden Linen Blackout', 'Tekstil', 529000, null, 2, 'Tenun Ruma', 'Gorden linen dengan lapisan blackout, per panel.', ['Ukuran' => '140x220 cm, 140x260 cm']],
            ['Lilin Aromaterapi Kayu Manis', 'Dekorasi', 129000, null, 100, 'Ruma Scents', 'Lilin soy wax beraroma kayu manis & vanila, 40 jam.', []],
            ['Diffuser Rotan Melati', 'Dekorasi', 189000, 159000, 45, 'Ruma Scents', 'Reed diffuser aroma melati 100 ml dengan stik rotan.', []],
            ['Jam Dinding Kayu Minimalis', 'Dekorasi', 349000, null, 0, 'Ruma Basics', 'Jam dinding kayu tanpa angka, mesin senyap.', []],
        ];

        $this->products($company, $user, $categories, $catalog, 'ruma');
    }

    private function seedFashion(CompanyProfile $company, User $user): void
    {
        $categories = $this->categories($company, [
            'Women' => ['Dresses', 'Tops', 'Outerwear'],
            'Accessories' => ['Bags', 'Scarves'],
        ]);

        $catalog = [
            ['Linen Wrap Dress Sora', 'Dresses', 1890000, null, 0, 'Maison Arunika', 'Wrap dress linen dengan potongan midi dan tali pinggang.', ['Ukuran' => 'S, M, L, XL', 'Warna' => 'Ivory, Black']],
            ['Silk Slip Dress Malam', 'Dresses', 2450000, 1990000, 0, 'Maison Arunika', 'Slip dress sutra dengan kerah V yang jatuh lembut.', ['Ukuran' => 'S, M, L', 'Warna' => 'Champagne, Midnight']],
            ['Pleated Shirt Dress Kirana', 'Dresses', 1750000, null, 0, 'Maison Arunika', 'Kemeja dress dengan lipit halus dan kancing mutiara.', ['Ukuran' => 'S, M, L']],
            ['Tenun Wrap Top Senja', 'Tops', 990000, null, 0, 'Maison Arunika', 'Atasan wrap dengan aksen tenun ikat Sumba.', ['Ukuran' => 'S, M, L']],
            ['Cotton Poplin Shirt Ayu', 'Tops', 790000, 650000, 0, 'Maison Arunika', 'Kemeja katun poplin oversize yang serbaguna.', ['Ukuran' => 'S, M, L, XL', 'Warna' => 'White, Sky']],
            ['Knit Vest Lembayung', 'Tops', 890000, null, 0, 'Maison Arunika', 'Rompi rajut dengan pola kabel halus.', ['Ukuran' => 'S, M, L']],
            ['Wool Blend Coat Purnama', 'Outerwear', 3290000, null, 0, 'Maison Arunika', 'Mantel wool blend dengan potongan longgar.', ['Ukuran' => 'S, M, L', 'Warna' => 'Camel, Charcoal']],
            ['Linen Blazer Santun', 'Outerwear', 1990000, null, 0, 'Maison Arunika', 'Blazer linen tanpa lapisan untuk iklim tropis.', ['Ukuran' => 'S, M, L']],
            ['Leather Tote Rumah', 'Bags', 2790000, null, 12, 'Maison Arunika', 'Tote kulit sapi nabati dengan saku dalam.', ['Warna' => 'Tan, Black']],
            ['Woven Clutch Pandan', 'Bags', 690000, 590000, 20, 'Maison Arunika', 'Clutch anyaman pandan dengan tali rantai emas.', []],
            ['Silk Scarf Batik Parang', 'Scarves', 750000, null, 25, 'Maison Arunika', 'Syal sutra 90x90 cm bermotif parang modern.', []],
            ['Tenun Shawl Flores', 'Scarves', 1150000, null, 8, 'Maison Arunika', 'Selendang tenun ikat Flores buatan tangan.', []],
        ];

        $this->products($company, $user, $categories, $catalog, 'maison', variantStock: 6);
    }

    /** @return array<string, int> name => id */
    private function categories(CompanyProfile $company, array $tree): array
    {
        $map = [];
        $order = 1;
        foreach ($tree as $root => $children) {
            $parent = $company->productCategories()->create([
                'name' => $root, 'slug' => Str::slug($root), 'status' => 'active', 'sort_order' => $order++,
                'description' => "Koleksi {$root} pilihan dari {$company->name}.",
                'image' => 'https://picsum.photos/seed/'.$company->slug.'-cat-'.Str::slug($root).'/800/800',
            ]);
            $map[$root] = $parent->id;
            foreach ($children as $i => $child) {
                $map[$child] = $company->productCategories()->create([
                    'name' => $child, 'slug' => Str::slug($child), 'parent_id' => $parent->id, 'status' => 'active', 'sort_order' => $i + 1,
                    'image' => 'https://picsum.photos/seed/'.$company->slug.'-cat-'.Str::slug($child).'/800/800',
                ])->id;
            }
        }

        return $map;
    }

    private function products(CompanyProfile $company, User $user, array $categories, array $catalog, string $prefix, int $variantStock = 10): void
    {
        $tags = $company->productTags()->pluck('id', 'slug');

        foreach ($catalog as $i => [$name, $category, $price, $sale, $stock, $brand, $short, $options]) {
            $optionRows = collect($options)->map(fn ($values, $option) => ['name' => $option, 'values' => $values])->values()->all();

            $product = $this->products->save($company, [
                'name' => $name,
                'category_id' => $categories[$category] ?? null,
                'sku' => strtoupper($prefix).'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'brand' => $brand,
                'short_description' => $short,
                'description' => '<p>'.$short.'</p><p>Dibuat dengan material pilihan dan pengerjaan yang teliti, sehingga nyaman dipakai setiap hari dan awet bertahun-tahun.</p><ul><li>Garansi 30 hari tukar barang</li><li>Dikemas aman dengan bubble wrap</li><li>Bisa ambil di toko</li></ul>',
                'specifications' => [['label' => 'Merek', 'value' => $brand], ['label' => 'Asal', 'value' => 'Indonesia'], ['label' => 'Perawatan', 'value' => 'Lap dengan kain lembab']],
                'price' => $price,
                'sale_price' => $sale,
                'compare_price' => null,
                'track_stock' => true,
                'stock' => $optionRows ? 0 : $stock,
                'stock_status' => 'in_stock',
                'weight' => random_int(3, 40) * 100,
                'status' => 'published',
                'featured' => $i % 5 === 0,
                'published_at' => now()->subDays(60 - $i),
                'tags' => array_values(array_filter([
                    $i % 5 === 0 ? $tags['featured'] ?? null : null,
                    $sale ? $tags['sale'] ?? null : null,
                    $i > count($catalog) - 6 ? $tags['new'] ?? null : null,
                    $i % 7 === 1 ? $tags['best-seller'] ?? null : null,
                ])),
                'options' => $optionRows,
                'variants' => [],
            ], $user);

            foreach (range(1, 3) as $n) {
                $product->images()->create(['image' => "https://picsum.photos/seed/{$prefix}-product-{$i}-{$n}/900/1100", 'alt' => $name, 'sort_order' => $n]);
            }

            foreach ($product->variants as $v => $variant) {
                $this->inventory->adjust($product, $variant, $variant->id % 4 === 0 ? 2 : $variantStock + ($v % 3) * 4, 'Stok awal', $user->id);
            }

            if (! $product->variants->count() && $stock === 0) {
                $product->update(['stock_status' => 'out_of_stock']);
            }
        }
    }

    private function seedCustomersAndOrders(CompanyProfile $company, int $count): void
    {
        $people = [
            ['Ayu Lestari', 'Bandung', 'Jawa Barat'], ['Bima Saputra', 'Jakarta Selatan', 'DKI Jakarta'], ['Citra Anggraini', 'Surabaya', 'Jawa Timur'],
            ['Dimas Prakoso', 'Yogyakarta', 'DI Yogyakarta'], ['Eka Wulandari', 'Semarang', 'Jawa Tengah'], ['Fajar Nugroho', 'Denpasar', 'Bali'],
            ['Gita Permata', 'Medan', 'Sumatera Utara'], ['Hadi Santoso', 'Makassar', 'Sulawesi Selatan'],
        ];

        $customers = collect($people)->map(function ($p, $i) use ($company) {
            $customer = Customer::query()->create([
                'company_profile_id' => $company->id,
                'name' => $p[0],
                'email' => Str::slug($p[0], '.').'@contoh.id',
                'phone' => '0812'.str_pad((string) (1000000 + $i * 7919), 8, '0', STR_PAD_LEFT),
                'password' => $i < 3 ? 'password' : null,
                'created_at' => now()->subDays(40 - $i * 3),
            ]);
            $customer->addresses()->create(['label' => 'Rumah', 'name' => $p[0], 'phone' => $customer->phone, 'address' => 'Jl. Melati No. '.($i + 7), 'city' => $p[1], 'province' => $p[2], 'postal_code' => (string) (40100 + $i * 111), 'country' => 'Indonesia', 'is_default' => true]);

            return $customer;
        });

        $products = $company->shopProducts()->with('variants')->where('status', 'published')->get()->filter(fn (Product $p) => $p->isInStock())->values();
        $shipping = $company->shippingMethods()->get();
        $payment = $company->paymentMethods()->get();
        $statuses = ['completed', 'completed', 'completed', 'shipped', 'processing', 'confirmed', 'pending', 'pending', 'cancelled', 'completed'];

        foreach (range(1, $count) as $n) {
            $customer = $customers[$n % $customers->count()];
            $address = $customer->addresses->first();
            $cart = Cart::query()->create(['company_profile_id' => $company->id, 'session_id' => 'seed-'.Str::random(20)]);

            foreach ($products->random(min(random_int(1, 3), $products->count())) as $product) {
                $variant = $product->variants->first(fn ($v) => $v->availableStock() > 1);
                if ($product->variants->isNotEmpty() && ! $variant) {
                    continue;
                }
                $cart->items()->create(['product_id' => $product->id, 'product_variant_id' => $variant?->id, 'quantity' => random_int(1, 2)]);
            }

            if (! $cart->items()->exists()) {
                $cart->delete();

                continue;
            }

            try {
                $order = $this->checkout->placeOrder($company, $cart, [
                    'name' => $customer->name, 'email' => $customer->email, 'phone' => $customer->phone,
                    'address' => $address->toSnapshot(),
                    'shipping_method_id' => $shipping[$n % $shipping->count()]->id,
                    'payment_method_id' => $payment[$n % $payment->count()]->id,
                    'coupon' => $n % 6 === 0 ? 'WELCOME10' : null,
                    'notes' => $n % 4 === 0 ? 'Mohon dikemas rapi, untuk hadiah.' : null,
                ]);
            } catch (\Throwable) {
                $cart->delete();

                continue;
            }

            $target = $statuses[$n % count($statuses)];
            $path = match ($target) {
                'confirmed' => ['confirmed'],
                'processing' => ['confirmed', 'processing'],
                'shipped' => ['confirmed', 'processing', 'packed', 'shipped'],
                'completed' => ['confirmed', 'processing', 'packed', 'shipped', 'completed'],
                'cancelled' => ['cancelled'],
                default => [],
            };

            if (in_array($target, ['confirmed', 'processing', 'shipped', 'completed'], true) && $order->payment?->method !== 'cod') {
                $this->orders->markPaid($order);
                $order->refresh();
            }
            foreach ($path as $status) {
                if ($order->status !== $status) {
                    $order = $this->orders->updateStatus($order, $status);
                }
                if ($status === 'shipped') {
                    $this->orders->setTracking($order, 'JNE', 'JNE'.random_int(100000000, 999999999));
                }
            }

            $placedAt = now()->subDays(random_int(0, 28))->subHours(random_int(0, 20));
            $order->forceFill(['created_at' => $placedAt])->save();
            $order->histories()->update(['created_at' => $placedAt]);

            if ($order->status === 'completed') {
                foreach ($order->items as $item) {
                    if ($item->product && random_int(0, 3) > 0) {
                        $review = $this->reviews->submit($company, $item->product, [
                            'rating' => [5, 5, 4, 5, 3][random_int(0, 4)],
                            'title' => ['Kualitas bagus', 'Sesuai foto', 'Pengiriman cepat', 'Sangat puas', 'Recommended'][random_int(0, 4)],
                            'body' => ['Barangnya rapi dan sesuai deskripsi, dikemas sangat aman.', 'Warnanya cantik, bahan terasa premium. Akan beli lagi.', 'Pengiriman cepat dan seller responsif. Terima kasih!', 'Kualitas jauh di atas harganya, rumah jadi lebih hangat.'][random_int(0, 3)],
                            'email' => $order->customer_email,
                            'name' => $order->customer_name,
                        ], $customer);
                        $this->reviews->moderate($review, random_int(0, 4) ? 'approved' : 'pending');
                    }
                }
            }
        }
    }
}
