{{--
    Reusable address form fields.
    Use $prefix (default '') to namespace IDs in multiple modals on the same page.
--}}
@php $prefix = $prefix ?? ''; @endphp

<input type="hidden" name="_form_context" value="{{ $prefix === 'edit_' ? 'edit' : 'add' }}">

<div class="mb-4">
    <label class="input-label" for="{{ $prefix }}label">Label Alamat</label>
    <input type="text" id="{{ $prefix }}label" name="label"
           placeholder="Contoh: Rumah, Kantor..." class="input-field"
           value="{{ old('label') }}" maxlength="50">
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}recipient_name">Nama Penerima <span class="text-red-500">*</span></label>
        <input type="text" id="{{ $prefix }}recipient_name" name="recipient_name"
               required placeholder="Nama lengkap penerima" class="input-field"
               value="{{ old('recipient_name') }}">
    </div>
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}phone">Nomor Telepon <span class="text-red-500">*</span></label>
        <input type="text" id="{{ $prefix }}phone" name="phone"
               required placeholder="08xx-xxxx-xxxx" class="input-field"
               value="{{ old('phone') }}">
    </div>
</div>

<div class="mb-4">
    <label class="input-label" for="{{ $prefix }}street">Alamat Lengkap (Jalan / No. Rumah) <span class="text-red-500">*</span></label>
    <textarea id="{{ $prefix }}street" name="street"
              required rows="3" placeholder="Contoh: Jl. Mawar No. 12, Blok B"
              class="input-field resize-y">{{ old('street') }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}rt_rw">RT/RW</label>
        <input type="text" id="{{ $prefix }}rt_rw" name="rt_rw"
               placeholder="001/002" class="input-field"
               value="{{ old('rt_rw') }}" maxlength="10">
    </div>
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}kelurahan">Kelurahan</label>
        <input type="text" id="{{ $prefix }}kelurahan" name="kelurahan"
               placeholder="Kelurahan" class="input-field"
               value="{{ old('kelurahan') }}">
    </div>
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}kecamatan">Kecamatan</label>
        <input type="text" id="{{ $prefix }}kecamatan" name="kecamatan"
               placeholder="Kecamatan" class="input-field"
               value="{{ old('kecamatan') }}">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}city">Kota / Kabupaten <span class="text-red-500">*</span></label>
        <input type="text" id="{{ $prefix }}city" name="city"
               required placeholder="Nama kota" class="input-field"
               value="{{ old('city') }}">
    </div>
    <div class="mb-4">
        <label class="input-label" for="{{ $prefix }}postal_code">Kode Pos</label>
        <input type="text" id="{{ $prefix }}postal_code" name="postal_code"
               placeholder="12345" class="input-field" maxlength="10"
               value="{{ old('postal_code') }}">
    </div>
</div>

<div class="flex items-center gap-2.5 py-2">
    <input type="checkbox" id="{{ $prefix }}is_default" name="is_default" value="1"
           {{ old('is_default') ? 'checked' : '' }}
           class="w-5 h-5 rounded border-cream-border accent-primary cursor-pointer">
    <label for="{{ $prefix }}is_default" class="text-sm font-semibold text-brown-mid cursor-pointer">Jadikan sebagai alamat utama</label>
</div>

