# Company Profile Generator — project notes

Laravel 13 SaaS (SQLite, Blade, Tailwind v4, Alpine, Vite). See README.md for the full overview.

- Central app vs tenant websites are separated by host: tenant routes use `Route::domain('{tenant_host}')`
  with a regex excluding `config('platform.central_domains')`; central routes use the `central` middleware.
- Business logic lives in `app/Services`; controllers stay thin. Repeatable content (services, products,
  projects, team, testimonials, gallery) is driven by `app/Support/ContentTypes.php`; editor tabs by
  `app/Support/ProfileTabs.php`.
- Website themes: `resources/views/websites/templates/{layout}`; always build URLs with `$site`
  (`App\Support\Website\SiteContext`) and use brand tokens (`bg-primary`, `text-on-primary`, `font-heading`,
  `rounded-brand`, `rounded-btn`). Register layouts in `config/website-templates.php`.
- Rich text is sanitized with `App\Support\HtmlSanitizer` before saving; everything else is escaped.
- Commands: `php artisan migrate:fresh --seed`, `php artisan test`, `npm run build`, `vendor/bin/pint`.
