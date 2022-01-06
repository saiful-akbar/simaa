@extends('templates.main')

@section('title', 'Tambah Akun Belanja')

@section('btn-kembali')
    <a href="{{ route('jenis-belanja') }}" class="btn btn-rounded btn-light btn-sm">
        <i class="mdi mdi-chevron-double-left"></i>
        <span>Kembali</span>
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form class="form-horizontal" action="{{ route('jenis-belanja.store') }}" method="post" autocomplete="off">
                @method('POST')
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mt-2">Form Tambah Akun Belanja</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">

                                {{-- input kategori belanja --}}
                                <div class="form-group row mb-3">
                                    <label for="kategori_belanja" class="col-md-3 col-sm-12 col-form-label">
                                        Kategori Belanja <small class="text-danger">*</small>
                                    </label>

                                    <div class="col-md-9 col-sm-12">
                                        <input type="text" id="kategori_belanja" name="kategori_belanja"
                                            placeholder="Masukan kategori belanja..."
                                            value="{{ old('kategori_belanja') }}"
                                            class="form-control @error('kategori_belanja') is-invalid @enderror" required />

                                        @error('kategori_belanja')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- input jenis_belanja (akun belanja) aktif --}}
                                <div class="form-group row justify-content-end">
                                    <div class="col-md-9 col-sm-12">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="active"
                                                class="custom-control-input form-control-lg" id="active"
                                                @if (old('active', true)) checked @endif />

                                            <label class="custom-control-label" for="active">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="col-md-9 col-sm-12">
                            <button type="submit" class="btn btn-info btn-rounded btn-sm mr-2">
                                <i class="mdi mdi-content-save"></i>
                                <span>Simpan</span>
                            </button>

                            <button type="reset" class="btn btn-rounded btn-outline-dark btn-sm">
                                <i class="mdi mdi-close"></i>
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
