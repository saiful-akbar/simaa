@extends('templates.main')

@section('title', 'Edit Divisi')

@section('content')
    <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-end">
            <a
                href="{{ route('divisi') }}"
                class="btn btn-sm btn-dark"
            >
                <i class="dripicons-chevron-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h4 class="header-title">Form Edit Divisi</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <form
                                class="form-horizontal"
                                action="{{ route('divisi.update', ['divisi' => $divisi->id]) }}"
                                method="post"
                                autocomplete="off"
                            >
                                @method('PATCH')
                                @csrf

                                <div class="form-group row mb-3">
                                    <label
                                        for="inputEmail3"
                                        class="col-md-3 col-sm-12 col-form-label"
                                    >
                                        Nama Divisi <small class="text-danger ml-1">*</small>
                                    </label>
                                    <div class="col-md-9 col-sm-12">
                                        <input
                                            type="text"
                                            name="nama_divisi"
                                            placeholder="Nama divisi..."
                                            value="{{ old('nama_divisi', $divisi->nama_divisi) }}"
                                            class="form-control @error('nama_divisi') is-invalid @enderror"
                                        />
                                        @error('nama_divisi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-0 justify-content-end row">
                                    <div class="col-md-9 col-sm-12">
                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm mr-2"
                                        >
                                            <i class="mdi mdi-content-save"></i>
                                            <span>Simpan</span>
                                        </button>
                                        <button
                                            type="reset"
                                            class="btn btn-sm btn-secondary"
                                        >
                                            <i class="mdi mdi-close"></i>
                                            <span>Reset</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection