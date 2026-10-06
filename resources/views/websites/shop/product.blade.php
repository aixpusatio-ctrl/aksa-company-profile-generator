@extends('websites.shop.layout')

@php
    $images = $product->images->map(fn ($i) => $i->url('image'))->filter()->values();
    $hasVariants = $variantsJson->isNotEmpty();
    $tracksStock = $product->track_stock && $product->stock_status !== 'backorder';
    $lowThreshold = (int) ($product->low_stock_threshold ?? $shop->low_stock_threshold ?? 5);
    $original = $hasVariants ? null : $product->originalPrice();
    $ctaCart = $product->ctaEnabled('add_to_cart', $shop);
    $ctaBuy = $product->ctaEnabled('buy_now', $shop);
    $ctaWa = $product->ctaEnabled('whatsapp', $shop) && $company->whatsappUrl();
    $ctaContact = $product->ctaEnabled('contact', $shop);
    $canPurchase = $ctaCart || $ctaBuy;
    $reviewsEnabled = (bool) $shop->option('reviews_enabled');
    $reviewErrors = $errors->getBag('review');
    $totalReviews = (int) $ratingBreakdown->sum();
    $specs = collect($product->specifications ?? [])->filter(fn ($s) => filled($s['label'] ?? null) && filled($s['value'] ?? null));

    $crumbs = [];
    if ($product->category?->parent) {
        $crumbs[$product->category->parent->name] = $site->shop('category/'.$product->category->parent->slug);
    }
    if ($product->category) {
        $crumbs[$product->category->name] = $site->shop('category/'.$product->category->slug);
    }
    $crumbs[$product->name] = null;

    $pageConfig = [
        'images' => $images->all(),
        'variants' => $variantsJson->all(),
        'options' => $product->options->map(fn ($o) => ['id' => $o->id, 'name' => $o->name, 'values' => $o->values->map(fn ($v) => ['id' => $v->id, 'value' => $v->value])->values()])->values()->all(),
        'price' => $product->formattedPrice(),
        'original' => $original ? \App\Support\Shop\Money::format($original) : null,
        'sku' => $product->sku,
        'stock' => $tracksStock ? $product->availableStock() : null,
        'inStock' => $product->isInStock(),
        'showStock' => (bool) $shop->option('show_stock'),
        'lowStock' => $lowThreshold,
    ];
@endphp

@section('shop')
    @foreach ($schema as $block)
        <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endforeach

    <div x-data="productPage(@js($pageConfig))">
        <div class="{{ $ds->container() }} pt-5 sm:pt-8">
            @include('websites.shop.partials.breadcrumb', ['crumbs' => $crumbs])
        </div>

        <section class="{{ $ds->container() }} grid gap-8 py-6 sm:py-8 lg:grid-cols-2 lg:gap-14">
            {{-- Gallery --}}
            <div class="min-w-0">
                <div class="relative overflow-hidden {{ $ds->img() }} bg-surface-alt">
                    <button type="button" class="block aspect-square w-full cursor-zoom-in" @click="images.length && (lightbox = true)" aria-label="Perbesar gambar">
                        @if ($images->isNotEmpty())
                            <img src="{{ $images->first() }}" :src="images[active]" alt="{{ $product->name }}" class="size-full object-cover {{ $product->isInStock() ? '' : 'opacity-70' }}">
                        @else
                            <x-site.img :src="null" :alt="$product->name" icon="cube" class="size-full" />
                        @endif
                    </button>
                    @include('websites.shop.partials.badges', ['badgeMax' => 3, 'badgeClass' => 'pointer-events-none absolute top-3 left-3 z-10 flex flex-col items-start gap-1'])
                    @include('websites.shop.partials.wishlist-button', ['wishWrap' => 'absolute top-3 right-3 z-10', 'wishClass' => 'inline-flex size-11 items-center justify-center rounded-full bg-white/90 text-neutral-800 shadow backdrop-blur transition hover:scale-110 hover:text-rose-500'])
                    <template x-if="images.length > 1">
                        <div class="pointer-events-none absolute inset-x-3 top-1/2 flex -translate-y-1/2 justify-between">
                            <button type="button" class="pointer-events-auto inline-flex size-9 items-center justify-center rounded-full bg-white/85 text-neutral-900 shadow backdrop-blur" @click="active = (active - 1 + images.length) % images.length" aria-label="Gambar sebelumnya"><x-icon name="chevron-left" class="size-4" /></button>
                            <button type="button" class="pointer-events-auto inline-flex size-9 items-center justify-center rounded-full bg-white/85 text-neutral-900 shadow backdrop-blur" @click="active = (active + 1) % images.length" aria-label="Gambar berikutnya"><x-icon name="chevron-right" class="size-4" /></button>
                        </div>
                    </template>
                </div>
                @if ($images->count() > 1)
                    <div class="shop-scroll mt-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($images as $i => $img)
                            <button type="button" @click="active = images.indexOf(@js($img))" class="size-16 shrink-0 overflow-hidden rounded-brand border-2 transition sm:size-20"
                                    :class="images[active] === @js($img) ? 'border-primary' : 'border-transparent opacity-70 hover:opacity-100'" aria-label="Gambar {{ $i + 1 }}">
                                <img src="{{ $img }}" alt="" loading="lazy" class="size-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Summary & buy box --}}
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted">
                    @if ($product->brand)
                        <a href="{{ $site->shop('products').'?'.http_build_query(['brand' => [$product->brand]]) }}" class="font-semibold tracking-wide text-primary uppercase">{{ $product->brand }}</a>
                    @endif
                    @if ($product->category)
                        <a href="{{ $site->shop('category/'.$product->category->slug) }}" class="hover:text-ink">{{ $product->category->name }}</a>
                    @endif
                </div>
                <h1 class="heading mt-2 text-3xl text-ink sm:text-4xl">{{ $product->name }}</h1>

                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                    @if ($product->rating_count > 0)
                        <a href="#reviews" class="flex items-center gap-1.5 text-muted hover:text-ink">
                            <x-site.stars :rating="(int) round((float) $product->rating_avg)" class="text-ink" />
                            <span class="font-semibold text-ink">{{ number_format((float) $product->rating_avg, 1, ',', '.') }}</span>
                            <span>({{ $product->rating_count }} ulasan)</span>
                        </a>
                    @endif
                    @if ($product->sold_count > 0)
                        <span class="text-muted">{{ number_format($product->sold_count, 0, ',', '.') }} terjual</span>
                    @endif
                </div>

                {{-- Price --}}
                <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="font-heading text-3xl font-bold" :class="original ? 'text-rose-600' : 'text-ink'" x-text="price">{{ $product->formattedPrice() }}</span>
                    <span class="text-base text-muted line-through" x-show="original" x-text="original" @if (! $original) x-cloak @endif>{{ $original ? \App\Support\Shop\Money::format($original) : '' }}</span>
                    @if ($product->discountPercent() && ! $hasVariants)
                        <span class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">Hemat {{ $product->discountPercent() }}%</span>
                    @endif
                </div>
                @if ($product->isOnSale() && $product->sale_ends_at)
                    <p class="mt-1 text-xs font-medium text-rose-600">Promo berakhir {{ $product->sale_ends_at->translatedFormat('d M Y') }}</p>
                @endif

                @if ($product->short_description)
                    <p class="mt-5 leading-relaxed text-muted">{{ $product->short_description }}</p>
                @endif

                <form id="buy-form" method="POST" action="{{ $site->shop('cart') }}" class="mt-6 space-y-5" @submit.prevent="submit($event)">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    {{-- Variant selector --}}
                    @if ($hasVariants)
                        <input type="hidden" name="variant_id" :value="variant ? variant.id : ''">
                        @foreach ($product->options as $option)
                            <fieldset x-cloak>
                                <legend class="mb-2 text-sm font-semibold text-ink">
                                    {{ $option->name }}:
                                    <span class="font-normal text-muted" x-text="(options.find(o => o.id === {{ $option->id }}).values.find(v => v.id === selected[{{ $option->id }}]) || {}).value || 'Pilih'"></span>
                                </legend>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($option->values as $value)
                                        <button type="button" @click="choose({{ $option->id }}, {{ $value->id }})"
                                                :aria-pressed="(selected[{{ $option->id }}] === {{ $value->id }}).toString()"
                                                :class="{
                                                    'border-primary bg-primary text-on-primary': selected[{{ $option->id }}] === {{ $value->id }},
                                                    'border-line text-ink hover:border-ink': selected[{{ $option->id }}] !== {{ $value->id }},
                                                    'opacity-40 line-through': !valueAvailable({{ $option->id }}, {{ $value->id }})
                                                }"
                                                class="min-w-11 rounded-btn border px-3.5 py-2 text-sm font-medium transition">{{ $value->value }}</button>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                        <noscript>
                            <label class="shop-label" for="variant-select">Varian</label>
                            <select id="variant-select" name="variant_id" class="shop-input" required>
                                @foreach ($variantsJson as $v)
                                    <option value="{{ $v['id'] }}" @disabled($v['available'] <= 0)>{{ $v['label'] }} — {{ $v['price_formatted'] }}{{ $v['available'] <= 0 ? ' (habis)' : '' }}</option>
                                @endforeach
                            </select>
                        </noscript>
                    @endif

                    {{-- Stock & SKU --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <span class="inline-flex items-center gap-1.5 font-medium" :class="canBuy ? 'text-emerald-600' : 'text-rose-600'">
                            <span class="size-2 rounded-full" :class="canBuy ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                            <span x-text="stockLabel">{{ $product->availabilityLabel() }}</span>
                        </span>
                        <span class="text-muted" x-show="sku">SKU: <span x-text="sku">{{ $product->sku }}</span></span>
                    </div>

                    @if ($canPurchase)
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="inline-flex h-12 items-center rounded-btn border border-line bg-card">
                                <button type="button" @click="dec()" class="inline-flex h-full w-11 items-center justify-center text-ink hover:text-primary" aria-label="Kurangi jumlah"><x-shop.icon name="minus" class="size-4" /></button>
                                <label for="qty" class="sr-only">Jumlah</label>
                                <input id="qty" type="number" name="quantity" min="1" :max="maxQty" value="1" x-model.number="qty" inputmode="numeric"
                                       class="shop-qty h-full w-12 border-0 bg-transparent text-center text-base font-semibold text-ink focus:outline-none">
                                <button type="button" @click="inc()" class="inline-flex h-full w-11 items-center justify-center text-ink hover:text-primary" aria-label="Tambah jumlah"><x-icon name="plus" class="size-4" /></button>
                            </div>
                            <div class="grid min-w-0 flex-1 gap-3 {{ $ctaCart && $ctaBuy ? 'grid-cols-2' : 'grid-cols-1' }}">
                                @if ($ctaCart)
                                    <button type="submit" class="{{ $ds->btn($ctaBuy ? 'secondary' : 'primary', 'h-12 w-full !px-3') }}" :disabled="!canBuy || $store.shop.busy" :class="!canBuy && 'opacity-50 cursor-not-allowed'" @disabled(! $product->isInStock())>
                                        <x-shop.icon name="bag" class="size-4" /> Keranjang
                                    </button>
                                @endif
                                @if ($ctaBuy)
                                    <button type="submit" name="buy_now" value="1" class="{{ $ds->btn('primary', 'h-12 w-full !px-3') }}" :disabled="!canBuy || $store.shop.busy" :class="!canBuy && 'opacity-50 cursor-not-allowed'" @disabled(! $product->isInStock())>
                                        Beli Sekarang
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($ctaWa || $ctaContact)
                        <div class="grid gap-3 {{ $ctaWa && $ctaContact ? 'sm:grid-cols-2' : '' }}">
                            @if ($ctaWa)
                                @php($waText = 'Halo, saya tertarik dengan produk '.$product->name."\n".$site->shop('product/'.$product->slug))
                                <a href="{{ $company->whatsappUrl() }}?text={{ rawurlencode($waText) }}" :href="whatsappHref(@js($company->whatsappUrl()), @js($product->name))"
                                   target="_blank" rel="noopener noreferrer" class="ds-btn h-12 w-full bg-[#25D366] text-white hover:brightness-95">
                                    <x-icon name="whatsapp" class="size-5" /> Pesan via WhatsApp
                                </a>
                            @endif
                            @if ($ctaContact)
                                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('secondary', 'h-12 w-full') }}"><x-icon name="chat" class="size-4" /> Hubungi Penjual</a>
                            @endif
                        </div>
                    @endif
                </form>

                {{-- Perks --}}
                <ul class="mt-7 space-y-2.5 border-t border-line pt-6 text-sm text-muted">
                    <li class="flex items-center gap-2.5"><x-icon name="truck" class="size-5 text-primary" /> Pengiriman ke seluruh Indonesia, ongkir dihitung saat checkout</li>
                    <li class="flex items-center gap-2.5"><x-icon name="shield" class="size-5 text-primary" /> Transaksi aman — harga selalu dihitung ulang di server</li>
                    @if ($product->tags->isNotEmpty())
                        <li class="flex flex-wrap items-center gap-2"><x-icon name="tag" class="size-5 text-primary" />
                            @foreach ($product->tags as $tag)
                                <a href="{{ $site->shop('products') }}?tag={{ urlencode($tag->slug) }}" class="rounded-full border border-line px-2.5 py-0.5 text-xs hover:border-primary hover:text-primary">{{ $tag->name }}</a>
                            @endforeach
                        </li>
                    @endif
                </ul>
            </div>
        </section>

        {{-- Description / specifications / reviews --}}
        <section class="{{ $ds->container() }} pb-6" x-data="{ tab: 'description' }">
            <div class="shop-scroll flex gap-6 overflow-x-auto border-b border-line text-sm font-semibold" role="tablist">
                <button type="button" role="tab" @click="tab = 'description'" :class="tab === 'description' ? 'border-primary text-ink' : 'border-transparent text-muted hover:text-ink'" class="shrink-0 border-b-2 py-3">Deskripsi</button>
                @if ($specs->isNotEmpty())
                    <button type="button" role="tab" @click="tab = 'specs'" :class="tab === 'specs' ? 'border-primary text-ink' : 'border-transparent text-muted hover:text-ink'" class="shrink-0 border-b-2 py-3">Spesifikasi</button>
                @endif
                @if ($reviewsEnabled)
                    <button type="button" role="tab" @click="tab = 'reviews'" :class="tab === 'reviews' ? 'border-primary text-ink' : 'border-transparent text-muted hover:text-ink'" class="shrink-0 border-b-2 py-3">Ulasan ({{ $product->rating_count }})</button>
                @endif
            </div>

            <div class="py-8" x-show="tab === 'description'">
                @if (filled($product->description))
                    <div class="site-prose max-w-3xl text-ink/90">{!! $product->description !!}</div>
                @else
                    <p class="text-muted">{{ $product->short_description ?: 'Belum ada deskripsi untuk produk ini.' }}</p>
                @endif
            </div>

            @if ($specs->isNotEmpty())
                <div class="py-8" x-show="tab === 'specs'" x-cloak>
                    <dl class="max-w-2xl divide-y divide-line overflow-hidden rounded-brand border border-line">
                        @if ($product->sku)
                            <div class="grid grid-cols-[9rem_1fr] gap-4 px-4 py-3 text-sm sm:grid-cols-[12rem_1fr]"><dt class="text-muted">SKU</dt><dd class="text-ink" x-text="sku">{{ $product->sku }}</dd></div>
                        @endif
                        @foreach ($specs as $spec)
                            <div class="grid grid-cols-[9rem_1fr] gap-4 px-4 py-3 text-sm odd:bg-surface-alt sm:grid-cols-[12rem_1fr]"><dt class="text-muted">{{ $spec['label'] }}</dt><dd class="break-words text-ink">{{ $spec['value'] }}</dd></div>
                        @endforeach
                        @if ($product->weight)
                            <div class="grid grid-cols-[9rem_1fr] gap-4 px-4 py-3 text-sm sm:grid-cols-[12rem_1fr]"><dt class="text-muted">Berat</dt><dd class="text-ink">{{ number_format($product->weight, 0, ',', '.') }} gram</dd></div>
                        @endif
                    </dl>
                </div>
            @endif

            @if ($reviewsEnabled)
                <div id="reviews" class="py-8" x-show="tab === 'reviews'" x-cloak x-init="if (location.hash === '#reviews' || {{ $reviewErrors->any() ? 'true' : 'false' }}) tab = 'reviews'">
                    <div class="grid gap-10 lg:grid-cols-[18rem_1fr]">
                        {{-- Summary --}}
                        <div>
                            <div class="flex items-end gap-3">
                                <span class="font-heading text-5xl font-bold text-ink">{{ number_format((float) $product->rating_avg, 1, ',', '.') }}</span>
                                <div class="pb-1.5">
                                    <x-site.stars :rating="(int) round((float) $product->rating_avg)" class="text-ink" />
                                    <p class="mt-0.5 text-xs text-muted">{{ $product->rating_count }} ulasan</p>
                                </div>
                            </div>
                            <div class="mt-5 space-y-1.5">
                                @for ($r = 5; $r >= 1; $r--)
                                    @php($cnt = (int) ($ratingBreakdown[$r] ?? 0))
                                    <div class="flex items-center gap-2 text-xs text-muted">
                                        <span class="w-3 text-right">{{ $r }}</span><x-icon name="star" stroke="0" class="size-3.5 text-amber-400" />
                                        <span class="h-2 flex-1 overflow-hidden rounded-full bg-surface-alt"><span class="block h-full rounded-full bg-amber-400" style="width: {{ $totalReviews ? round($cnt / $totalReviews * 100) : 0 }}%"></span></span>
                                        <span class="w-6 text-right">{{ $cnt }}</span>
                                    </div>
                                @endfor
                            </div>

                            {{-- Review form --}}
                            <form method="POST" action="{{ $site->shop('product/'.$product->slug.'/reviews') }}" enctype="multipart/form-data" class="mt-8 space-y-3 rounded-brand border border-line bg-card p-4" x-data="{ rating: {{ (int) old('rating', 5) }} }">
                                @csrf
                                <h3 class="font-heading font-bold text-ink">Tulis ulasan</h3>
                                @if ($reviewErrors->any())
                                    <div class="rounded-md bg-rose-50 px-3 py-2 text-xs text-rose-700">{{ $reviewErrors->first() }}</div>
                                @endif
                                <div>
                                    <span class="shop-label">Rating</span>
                                    <div class="flex gap-1" role="radiogroup" aria-label="Rating">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $s }}" class="peer sr-only" x-model.number="rating" @checked((int) old('rating', 5) === $s)>
                                                <x-icon name="star" stroke="0" class="size-7 transition peer-focus-visible:ring-2 peer-focus-visible:ring-primary" ::class="rating >= {{ $s }} ? 'text-amber-400' : 'text-ink/20'" />
                                                <span class="sr-only">{{ $s }} bintang</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                @unless ($customer)
                                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                                        <div><label for="rv-name" class="shop-label">Nama</label><input id="rv-name" name="name" value="{{ old('name') }}" required maxlength="120" class="shop-input"></div>
                                        <div><label for="rv-email" class="shop-label">Email</label><input id="rv-email" type="email" name="email" value="{{ old('email') }}" required maxlength="150" class="shop-input"></div>
                                    </div>
                                @endunless
                                <div><label for="rv-title" class="shop-label">Judul <span class="font-normal text-muted">(opsional)</span></label><input id="rv-title" name="title" value="{{ old('title') }}" maxlength="120" class="shop-input"></div>
                                <div><label for="rv-body" class="shop-label">Ulasan</label><textarea id="rv-body" name="body" rows="3" maxlength="2000" class="shop-input">{{ old('body') }}</textarea></div>
                                <div><label for="rv-image" class="shop-label">Foto <span class="font-normal text-muted">(opsional, maks 2 MB)</span></label><input id="rv-image" type="file" name="image" accept="image/*" class="block w-full text-xs text-muted file:mr-3 file:rounded-btn file:border-0 file:bg-surface-alt file:px-3 file:py-2 file:text-xs file:font-semibold file:text-ink"></div>
                                {{-- Honeypot: must stay empty --}}
                                <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website_url" tabindex="-1" autocomplete="off" value=""></label></div>
                                <button type="submit" class="{{ $ds->btn('primary', 'w-full') }}">Kirim Ulasan</button>
                                @if ($shop->option('reviews_moderation'))
                                    <p class="text-center text-[11px] text-muted">Ulasan tampil setelah dimoderasi penjual.</p>
                                @endif
                            </form>
                        </div>

                        {{-- List --}}
                        <div>
                            @forelse ($reviews as $review)
                                <article class="border-b border-line py-5 first:pt-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex size-9 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">{{ mb_strtoupper(mb_substr($review->name, 0, 1)) }}</span>
                                            <div>
                                                <p class="text-sm font-semibold text-ink">{{ $review->name }}
                                                    @if ($review->verified_purchase)
                                                        <span class="ml-1 inline-flex items-center gap-0.5 text-[11px] font-medium text-emerald-600"><x-icon name="check-circle" class="size-3.5" /> Pembeli terverifikasi</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-muted">{{ $review->created_at->translatedFormat('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <x-site.stars :rating="$review->rating" class="text-ink" />
                                    </div>
                                    @if ($review->title)
                                        <h4 class="mt-3 font-semibold text-ink">{{ $review->title }}</h4>
                                    @endif
                                    @if ($review->body)
                                        <p class="mt-1.5 text-sm leading-relaxed whitespace-pre-line text-muted">{{ $review->body }}</p>
                                    @endif
                                    @if ($review->image)
                                        <a href="{{ $review->url('image') }}" target="_blank" rel="noopener" class="mt-3 inline-block size-20 overflow-hidden rounded-brand border border-line"><img src="{{ $review->url('image') }}" alt="Foto ulasan" loading="lazy" class="size-full object-cover"></a>
                                    @endif
                                </article>
                            @empty
                                <p class="text-sm text-muted">Belum ada ulasan. Jadilah yang pertama memberi ulasan!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </section>

        {{-- Lightbox --}}
        <div x-cloak x-show="lightbox" x-transition.opacity class="fixed inset-0 z-[90] flex items-center justify-center bg-black/90 p-4" @click.self="lightbox = false" @keydown.escape.window="lightbox = false" role="dialog" aria-modal="true">
            <button type="button" class="absolute top-4 right-4 inline-flex size-10 items-center justify-center rounded-full bg-white/10 text-white" @click="lightbox = false" aria-label="Tutup"><x-icon name="x" class="size-6" /></button>
            <img :src="images[active]" alt="{{ $product->name }}" class="max-h-[85vh] max-w-full rounded-brand object-contain">
        </div>

        {{-- Sticky mobile CTA --}}
        @if ($canPurchase)
            <div class="shop-sticky-cta shop-no-print fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 px-4 py-3 backdrop-blur lg:hidden">
                <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs text-muted">{{ $product->name }}</p>
                        <p class="truncate font-heading text-base font-bold text-ink" x-text="price">{{ $product->formattedPrice() }}</p>
                    </div>
                    <button type="submit" form="buy-form" @if ($ctaBuy && ! $ctaCart) name="buy_now" value="1" @endif class="{{ $ds->btn('primary', '!px-5') }}" :disabled="!canBuy || $store.shop.busy" :class="!canBuy && 'opacity-50'" @disabled(! $product->isInStock())>
                        <x-shop.icon name="bag" class="size-4" /> {{ $ctaCart ? '+ Keranjang' : 'Beli Sekarang' }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Related & recently viewed --}}
    @if ($related->isNotEmpty())
        <section class="border-t border-line py-12 sm:py-14">
            <div class="{{ $ds->container() }}">
                @include('websites.shop.partials.section-heading', ['eyebrow' => 'Rekomendasi', 'heading' => 'Produk Terkait', 'more' => $product->category ? $site->shop('category/'.$product->category->slug) : null, 'n' => 1])
                @include('websites.shop.partials.grid', ['products' => $related])
            </div>
        </section>
    @endif
    @if ($recent->isNotEmpty())
        <section class="bg-surface-alt py-12 sm:py-14">
            <div class="{{ $ds->container() }}">
                @include('websites.shop.partials.section-heading', ['eyebrow' => 'Riwayat', 'heading' => 'Terakhir Dilihat', 'more' => null, 'n' => 2])
                @include('websites.shop.partials.grid', ['products' => $recent])
            </div>
        </section>
    @endif
@endsection
