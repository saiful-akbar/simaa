<x-layouts.auth title="Tambah Realisasi" back-button="{{ route('belanja') }}">
    <x-slot name="style">
        <link href="{{ asset('assets/css/vendor/summernote-bs4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/vendor/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/vendor/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/vendor/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/vendor/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    </x-slot>

    {{-- Form tambah transaksi (realisasi) --}}
    <x-form action="{{ route('belanja.store') }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="budget_id" id="budget_id" value="{{ old('budget_id') }}" required />

        {{-- input akun belanja (jenis_belanja) & bagian (divisi) --}}
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header pt-3">
                        <h4 class="header-title">Pagu</h4>
                    </div>

                    <div class="card-body">

                        {{-- Input jenis belanja (akun belanja) --}}
                        <div class="form-group row mb-3">
                            <label for="jenis_belanja" class="col-md-3 col-sm-12 col-form-label">
                                Akun Belanja <small class="text-danger">*</small>
                            </label>

                            <div class="input-group col-md-9 col-sm-12">
                                <div class="input-group-prepend">
                                    <button type="button"
                                        class="btn btn-sm btn-info"
                                        data-toggle="tooltip"
                                        data-original-title="Pilih akun belanja"
                                        data-placement="top"
                                        onclick="transaksi.showModalTableBudget(true)"
                                    >
                                        <i class="mdi mdi-table-large"></i>
                                    </button>
                                </div>

                                <input type="text"
                                    name="nama_akun_belanja"
                                    id="nama_akun_belanja"
                                    class="form-control @error('budget_id') is-invalid @else @error('nama_akun_belanja') is-invalid @enderror @enderror"
                                    placeholder="Akun belanja..."
                                    value="{{ old('nama_akun_belanja') }}"
                                    readonly
                                    required
                                />

                                <input type="text"
                                    name="kategori_belanja"
                                    id="kategori_belanja"
                                    class="form-control @error('budget_id') is-invalid @else @error('kategori_belanja') is-invalid @enderror @enderror"
                                    placeholder="Jenis belanja..."
                                    value="{{ old('kategori_belanja') }}"
                                    readonly
                                    required
                                />

                                @error('budget_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    @error('nama_akun_belanja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        @error('kategori_belanja')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @enderror
                                @enderror
                            </div>
                        </div>

                        {{-- input bagian (divisi) --}}
                        <div class="form-group row mb-3">
                            <label for="nama_divisi" class="col-md-3 col-sm-12 col-form-label">
                                Bagian <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="text"
                                    id="nama_divisi"
                                    name="nama_divisi"
                                    value="{{ old('nama_divisi') }}" class="form-control @error('nama_divisi') is-invalid @enderror"
                                    placeholder="Bagian..."
                                    readonly
                                    required
                                />

                                @error('nama_divisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input tahun anggaran --}}
                        <div class="form-group row mb-3">
                            <label for="tahun_anggaran" class="col-md-3 col-sm-12 col-form-label">
                                Tahun Anggaran <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="number"
                                    id="tahun_anggaran"
                                    name="tahun_anggaran"
                                    value="{{ old('tahun_anggaran') }}"
                                    class="form-control @error('tahun_anggaran') is-invalid @enderror"
                                    placeholder="Tahun Anggaran..."
                                    readonly
                                />

                                @error('tahun_anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input sisa budget --}}
                        <div class="form-group row mb-3">
                            <label for="sisa_budget" class="col-md-3 col-sm-12 col-form-label">
                                Sisa Budget <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12 input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Rp.</span>
                                </div>

                                <input type="text"
                                    id="sisa_budget"
                                    name="sisa_budget"
                                    value="{{ old('sisa_budget') }}"
                                    class="form-control @error('sisa_budget') is-invalid @enderror"
                                    placeholder="Sisa budget..."
                                    readonly
                                />

                                @error('sisa_budget')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- input kegiatan, jumlah nominal & uraian --}}
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mt-2">Kegiatan</h4>
                    </div>

                    <div class="card-body">

                        {{-- input tanggal --}}
                        <div class="form-group row mb-3">
                            <label for="tanggal" class="col-md-3 col-sm-12 col-form-label">
                                Tanggal <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="date"
                                    id="tanggal"
                                    name="tanggal"
                                    placeholder="Masukan tanggal transaksi belanja..."
                                    value="{{ old('tanggal') }}"
                                    class="form-control @error('tanggal') is-invalid @enderror"
                                    required
                                />

                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input kegiatan --}}
                        <div class="form-group row mb-3">
                            <label for="kegiatan" class="col-md-3 col-sm-12 col-form-label">
                                Kegiatan <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="text"
                                    id="kegiatan"
                                    name="kegiatan"
                                    placeholder="Masukan kegiatan..."
                                    value="{{ old('kegiatan') }}"
                                    class="form-control @error('kegiatan') is-invalid @enderror"
                                    required
                                />

                                @error('kegiatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input jumlah_nominal --}}
                        <div class="form-group row mb-3">
                            <label for="jumlah_nominal" class="col-md-3 col-sm-12 col-form-label">
                                Jumlah Nominal <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12 input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"> Rp.</span>
                                </div>

                                <input type="number"
                                    id="jumlah_nominal"
                                    name="jumlah_nominal"
                                    min="0"
                                    placeholder="Masukan jumlah nominal..."
                                    value="{{ old('jumlah_nominal') }}"
                                    class="form-control @error('jumlah_nominal') is-invalid @enderror"
                                    required
                                />

                                @error('jumlah_nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input nama approval --}}
                        <div class="form-group row mb-3">
                            <label for="approval" class="col-md-3 col-sm-12 col-form-label">
                                Nama Approval <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="text"
                                    id="approval"
                                    name="approval"
                                    placeholder="Masukan nama approval..."
                                    value="{{ old('approval') }}"
                                    class="form-control @error('approval') is-invalid @enderror"
                                    required
                                />

                                @error('approval')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- input Outstanding --}}
                        <div class="form-group row justify-content-end">
                            <div class="col-md-9 col-sm-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                        name="outstanding"
                                        class="custom-control-input form-control-lg"
                                        id="outstanding"
                                        @if (old('outstanding')) checked @endif
                                    />

                                    <label class="custom-control-label" for="outstanding">
                                        Outstanding
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- input dokumen & file dokumen --}}
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title mt-2">Dokumen</h4>
                    </div>

                    <div class="card-body">

                        {{-- input no dokumen --}}
                        <div class="form-group row mb-3">
                            <label for="no_dokumen" class="col-md-3 col-sm-12 col-form-label">
                                No Dokumen <small class="text-danger">*</small>
                            </label>

                            <div class="col-md-9 col-sm-12">
                                <input type="text"
                                    id="no_dokumen"
                                    name="no_dokumen"
                                    placeholder="Masukan no dokumen..."
                                    value="{{ old('no_dokumen', $noDocument) }}"
                                    class="form-control @error('no_dokumen') is-invalid @enderror"
                                    required
                                />

                                @error('no_dokumen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- upload file dokument --}}
                        <div class="form-group row mb-3">
                            <span class="col-md-3 col-sm-12 col-form-label">
                                File Dokumen
                            </span>

                            <div class="col-md-9 col-sm-12">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="file_dokumen">
                                            <span type="button" class="btn btn-success btn-sm">
                                                <i class="mdi mdi-upload"></i>
                                                <span>Unggah File</span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-6">
                                        <span id="file-name" class="d-none badge badge-light py-1 px-1 mt-1" data-action="create"></span>
                                    </div>
                                </div>

                                <input type="file"
                                    id="file_dokumen"
                                    name="file_dokumen"
                                    value="{{ old('file_dokumen') }}"
                                    class="d-none is-invalid @error('file_dokumen') is-invalid @enderror"
                                />

                                @error('file_dokumen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- input uraian --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pt-3">
                        <h3 class="header-title">Uraian</h3>
                    </div>

                    <div class="card-body">
                        <textarea name="uraian" id="uraian" class="form-control @error('uraian') is-invalid @enderror">{{ old('uraian') }}</textarea>

                        @error('uraian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-sm mr-2">
                            <i class="mdi mdi-content-save"></i>
                            <span>Simpan</span>
                        </button>

                        <button type="reset" class="btn btn-dark btn-sm">
                            <i class="mdi mdi-close-circle"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-form>
    {{-- End form tambah transaksi (realisasi) --}}

    {{-- Modal tabel budget --}}
    <x-transaksi.modal-table-budget />

    <x-slot name="script">
        <script src="{{ asset('assets/js/vendor/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/transaksi.js') }}"></script>
    </x-slot>
</x-layouts.auth>
