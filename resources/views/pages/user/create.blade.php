<x-layouts.auth title="Tambah User" back-button="{{ route('user') }}">
    <x-form method="POST" enctype="multipart/form-data" action="{{ route('user.store') }}">

        {{-- form akun --}}
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mt-2">Form Akun</h4>
                    </div>

                    <div class="card-body">

                        {{-- Input username --}}
                        <div class="form-group row mb-3">
                            <label for="username" class="col-md-3 col-sm-12 col-form-label">
                                Username <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input required type="text" id="username" name="username" placeholder="Masukan username..."
                                    value="{{ old('username') }}"
                                    class="form-control @error('username') is-invalid @enderror" />

                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Input password --}}
                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-3 col-sm-12 col-form-label">
                                password <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                    <input required type="password" id="password" name="password"
                                        placeholder="Masukan password..." value="{{ old('password') }}"
                                        class="form-control @error('password') is-invalid @enderror" />

                                    <div class="input-group-append" data-password="false" style="cursor: pointer">
                                        <div class="input-group-text">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Switch aktif --}}
                        <div class="form-group row justify-content-end">
                            <div class="col-md-9 col-sm-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="active" class="custom-control-input form-control-lg" id="active"
                                        @if (old('active')) checked @endif />

                                    <label class="custom-control-label" for="active">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- end form akun --}}

        {{-- form profil --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mt-2">
                            Form Profil
                        </h4>
                    </div>

                    <div class="card-body">

                        {{-- Upload avatar --}}
                        <div class="form-group row justify-content-end mb-3">
                            <div class="col-md-9 col-sm-12">
                                <img id="avatar-view" alt="avatar" class="img-fluid avatar-lg rounded-circle img-thumbnail"
                                    src="{{ asset('assets/images/avatars/avatar_default.webp') }}"
                                    data-src="{{ asset('assets/images/avatars/avatar_default.webp') }}" />

                                <label for="avatar" class="ml-2">
                                    <span type="button" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-upload"></i>
                                        <span>Unggah</span>
                                    </span>
                                </label>

                                <div>
                                    <input type="file" id="avatar" name="avatar" accept="image/*" value="{{ old('avatar') }}"
                                        class="@error('avatar') is-invalid @enderror" style="display: none;" />

                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- input nama lengkap --}}
                        <div class="form-group row mb-3">
                            <label for="nama_lengkap" class="col-md-3 col-sm-12 col-form-label">
                                Nama Lengkap <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input required type="text" id="nama_lengkap" name="nama_lengkap"
                                    placeholder="Masukan nama lengkap..." value="{{ old('nama_lengkap') }}"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror" />

                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Input divisi (bagian) --}}
                        <div class="form-group row mb-3">
                            <label for="divisi_id" class="col-md-3 col-sm-12 col-form-label">
                                Bagian <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <select required name="divisi_id" id="divisi_id" data-toggle="select2"
                                    class="form-control select2 @error('divisi_id') is-invalid @enderror">
                                    <option disabled value="{{ null }}"
                                        @if (!old('divisi_id')) selected @endif>
                                        -- Pilih Bagian --
                                    </option>

                                    @foreach ($divisions as $divisi)
                                        <option value="{{ $divisi->id }}" @if (old('divisi_id') == $divisi->id) selected @endif>
                                            {{ $divisi->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('divisi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Input seksi --}}
                        <div class="form-group row mb-3">
                            <label for="seksi" class="col-md-3 col-sm-12 col-form-label">
                                Seksi <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input required type="text" id="seksi" name="seksi" placeholder="Masukan seksi..."
                                    value="{{ old('seksi') }}" class="form-control @error('seksi') is-invalid @enderror" />

                                @error('seksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-sm mr-2">
                            <i class="mdi mdi-content-save"></i>
                            <span>Simpan</span>
                        </button>

                        <button type="reset" class="btn btn-dark btn-sm">
                            <i class="mdi mdi-close"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end form profil --}}

    </x-form>

    <x-slot name="script">
        <script src="{{ asset('assets/js/pages/user.js') }}"></script>
    </x-slot>
</x-layouts.auth>
