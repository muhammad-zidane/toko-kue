{{--
    Reusable address form fields.
    Use $prefix (default '') to namespace IDs in multiple modals on the same page.
--}}
@php $prefix = $prefix ?? ''; @endphp

<input type="hidden" name="_form_context" value="{{ $prefix === 'edit_' ? 'edit' : 'add' }}">

<div class="form-group">
    <label class="form-label" for="{{ $prefix }}label">Label Alamat</label>
    <input type="text" id="{{ $prefix }}label" name="label"
           placeholder="Contoh: Rumah, Kantor..." class="form-input"
           value="{{ old('label') }}" maxlength="50">
</div>

<div class="form-row-2">
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}recipient_name">Nama Penerima <span style="color:#EF4444;">*</span></label>
        <input type="text" id="{{ $prefix }}recipient_name" name="recipient_name"
               required placeholder="Nama lengkap penerima" class="form-input"
               value="{{ old('recipient_name') }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}phone">Nomor Telepon <span style="color:#EF4444;">*</span></label>
        <input type="text" id="{{ $prefix }}phone" name="phone"
               required placeholder="08xx-xxxx-xxxx" class="form-input"
               value="{{ old('phone') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="{{ $prefix }}street">Alamat Lengkap (Jalan / No. Rumah) <span style="color:#EF4444;">*</span></label>
    <textarea id="{{ $prefix }}street" name="street"
              required rows="3" placeholder="Contoh: Jl. Mawar No. 12, Blok B"
              class="form-input" style="resize:vertical;">{{ old('street') }}</textarea>
</div>

<div class="form-row-3">
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}rt_rw">RT/RW</label>
        <input type="text" id="{{ $prefix }}rt_rw" name="rt_rw"
               placeholder="001/002" class="form-input"
               value="{{ old('rt_rw') }}" maxlength="10">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}kelurahan">Kelurahan</label>
        <input type="text" id="{{ $prefix }}kelurahan" name="kelurahan"
               placeholder="Kelurahan" class="form-input"
               value="{{ old('kelurahan') }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}kecamatan">Kecamatan</label>
        <input type="text" id="{{ $prefix }}kecamatan" name="kecamatan"
               placeholder="Kecamatan" class="form-input"
               value="{{ old('kecamatan') }}">
    </div>
</div>

<div class="form-row-2">
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}city">Kota / Kabupaten <span style="color:#EF4444;">*</span></label>
        <input type="text" id="{{ $prefix }}city" name="city"
               required placeholder="Nama kota" class="form-input"
               value="{{ old('city') }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}postal_code">Kode Pos</label>
        <input type="text" id="{{ $prefix }}postal_code" name="postal_code"
               placeholder="12345" class="form-input" maxlength="10"
               value="{{ old('postal_code') }}">
    </div>
</div>

<div class="checkbox-group">
    <input type="checkbox" id="{{ $prefix }}is_default" name="is_default" value="1"
           {{ old('is_default') ? 'checked' : '' }}>
    <label for="{{ $prefix }}is_default">Jadikan sebagai alamat utama</label>
</div>
