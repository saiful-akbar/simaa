@extends('templates.main')

@section('title', 'Bagian')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title mt-2">Tabel Bagian</h4>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 col-sm-12 mb-3">
                            @if ($userAccess->create == 1)
                                <a href="{{ route('divisi.create') }}" class="btn btn-sm btn-rounded btn-primary">
                                    <i class="mdi mdi-plus-circle mr-1"></i>
                                    <span>Tambah Bagian</span>
                                </a>
                            @endif
                        </div>


                        {{-- form search --}}
                        <div class="col-md-6 col-sm-12 mb-3">
                            <form action="{{ route('divisi') }}" method="GET" autocomplete="off">
                                <div class="input-group">
                                    <input type="search" name="search" placeholder="Cari bagian..." class="form-control"
                                        value="{{ request('search') }}" />

                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">
                                            <i class="uil-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- table --}}
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="table-responsive">
                                <table class="table table table-centered table-sm table-hover nowrap w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Bagian</th>
                                            <th class="text-center">Aktif</th>

                                            @if ($isAdmin)
                                                <th>Dibuat</th>
                                                <th>Diperbarui</th>
                                            @endif

                                            @if ($userAccess->update == 1 || $userAccess->delete == 1)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($divisi as $data)
                                            <tr>
                                                <td>
                                                    {{ $divisi->perPage() * ($divisi->currentPage() - 1) + $loop->iteration }}
                                                </td>
                                                <td>{{ ucwords($data->nama_divisi) }}</td>
                                                <td class="text-center">
                                                    @if ($data->active == 1)
                                                        <i class="mdi mdi-check text-success h4"></i>
                                                    @else
                                                        <i class="mdi mdi mdi-close text-danger h4"></i>
                                                    @endif
                                                </td>

                                                @if ($isAdmin)
                                                    <td>{{ $data->created_at }}</td>
                                                    <td>{{ $data->updated_at }}</td>
                                                @endif

                                                @if ($userAccess->update == 1 || $userAccess->delete == 1)
                                                    <td class="text-center">

                                                        @if ($userAccess->update == 1)
                                                            <a href="{{ route('divisi.edit', ['divisi' => $data->id]) }}"
                                                                class="btn btn-sm btn-light btn-icon mx-1" data-toggle="tooltip"
                                                                data-original-title="Edit" data-placement="top">
                                                                <i class="mdi mdi-square-edit-outline"></i>
                                                            </a>
                                                        @endif

                                                        @if ($userAccess->delete == 1)
                                                            <button
                                                                onclick="handleDelete({{ $data->id }}, '{{ $data->nama_divisi }}')"
                                                                class="btn btn-sm btn-light btn-icon mx-1" data-toggle="tooltip"
                                                                data-original-title="Hapus" data-placement="top">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        @endif

                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            {{ $divisi->links() }}
                        </div>
                    </div>
                    {{-- end table --}}

                </div>
            </div>
        </div>
    </div>

    {{-- form delete divisi --}}
    <form id="form-delete" method="post" style="display: none">
        @method('DELETE') @csrf
    </form>
@endsection

@section('js')
    <script src="{{ asset('assets/js/pages/divisi.js') }}"></script>
@endsection
