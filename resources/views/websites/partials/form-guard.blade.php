{{-- CSRF token + honeypot field for the public contact form. --}}
@csrf
<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden">
    <label>Website <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
</div>
