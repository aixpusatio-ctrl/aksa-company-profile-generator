<select name="shop" class="form-input sm:w-48">
    <option value="">Semua toko</option>
    @foreach ($shopOptions as $id => $name)
        <option value="{{ $id }}" @selected($shop === $id)>{{ $name }}</option>
    @endforeach
</select>
