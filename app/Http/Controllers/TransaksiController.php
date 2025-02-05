<?php

namespace App\Http\Controllers;

use App\Exports\LaporanTransaksiExport;
use App\Models\AkunBelanja;
use App\Models\Budget;
use App\Models\Divisi;
use App\Models\Transaksi;
use App\Traits\UserAccessTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    use UserAccessTrait;

    /**
     * generate nomer dokumen baru
     *
     * @return string
     */
    public function generateNoDocument()
    {
        /**
         * buat format
         */
        $format = 'DOK-' . date('Y-m') . '-';

        /**
         * ambil no dokumen tertinggi berdasarkan bulan dan tahun sekarang
         */
        $maxDoc = Transaksi::select('no_dokumen')
            ->where('no_dokumen', 'like', "{$format}%")
            ->max('no_dokumen');

        /**
         * ambil no unique dokumen dan tambahkan 1
         */
        $no = (int) substr($maxDoc, 12) + 1;

        /**
         * cek panjang nomer unique $no
         */
        switch (strlen($no)) {
            case 1:
                $format .= "000{$no}";
                break;

            case 2:
                $format .= "00{$no}";
                break;

            case 3:
                $format .= "0{$no}";
                break;

            default:
                $format .= $no;
                break;
        }

        return trim($format);
    }

    /**
     * view halaman transaksi
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /**
         * ambil user menu akses
         */
        $userAccess = $this->getAccess(href: '/belanja');

        /**
         * ambil data user sebagai admin atau bukan
         */
        $isAdmin = $this->isAdmin(href: '/belanja');

        /**
         * Validasi rule
         */
        $validateRules = [
            'periode_awal'  => [],
            'periode_akhir' => [],
            'divisi'        => [],
            'jenis_belanja' => [],
            'no_dokumen'    => [],
        ];

        /**
         * Pesan error validasi
         */
        $validateErrorMessage = [
            'periode_awal.required'  => 'Periode harus diisi.',
            'periode_awal.date'      => 'Periode harus tanggal yang valid.',
            'periode_akhir.required' => 'Periode harus diisi.',
            'periode_akhir.date'     => 'Periode harus tanggal yang valid.',
            'divisi.exists'          => 'Divisi tidak ada. Pilih divisi yang ditentukan.',
            'jenis_belanja.exists'   => 'Jenis belanja tidak ada. Pilih jenis belanja yang ditentukan.',
            'no_dokumen.exists'      => 'No dokumen tidak ditemukan.',
        ];

        /**
         * jika periode_awal & periode_akhir dikirim tambahkan validasi
         */
        if ($request->periode_awal || $request->periode_akhir) {
            array_push($validateRules['periode_awal'], 'required', 'date');
            array_push($validateRules['periode_akhir'], 'required', 'date');
        }

        /**
         * jika jenis belanja dipilah tambahkan validasi
         */
        if ($request->jenis_belanja != null) {
            array_push($validateRules['jenis_belanja'], 'exists:jenis_belanja,kategori_belanja');
        }

        /**
         * Cek user admin atau tidak
         */
        if ($isAdmin) {

            /**
             * jika divisi dipilih tambahkan validasi
             */
            if ($request->divisi != null) {
                array_push($validateRules['divisi'], 'exists:divisi,nama_divisi');
            }

            /**
             * jika no dokumen diisi tambahkan validasi
             */
            if ($request->no_dokumen != null) {
                array_push($validateRules['no_dokumen'], 'exists:transaksi,no_dokumen');
            }
        }

        /**
         * jalankan validasi
         */
        $request->validate($validateRules, $validateErrorMessage);

        /**
         * periode default
         */
        $periodeAwal = $request->periode_awal ?? date('Y-m-d', time() - (60 * 60 * 24 * 13));
        $periodeAkhir = $request->periode_akhir ?? date('Y-m-d');

        /**
         * Query join table transaksi, divisi, user & profil
         */
        $query = Transaksi::with('budget.divisi', 'budget.jenisBelanja.akunBelanja', 'user.profil')
            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);

        /**
         * jika jenis belanja dipilih tambahkan query
         */
        if ($request->jenis_belanja != null) {
            $query->whereHas('budget.jenisBelanja', function (Builder $query) use ($request) {
                $query->where('kategori_belanja', $request->jenis_belanja);
            });
        }

        /**
         * Cek user admin atau tidak
         */
        if ($isAdmin) {

            /**
             * jika divisi dipilih tambahkan query
             */
            if ($request->divisi != null) {
                $query->whereHas('budget.divisi', function (Builder $query) use ($request) {
                    $query->where('nama_divisi', $request->divisi);
                });
            }


            /**
             * jika no dokumen dipilih tambahkan validasi
             */
            if ($request->no_dokumen != null) {
                $query->where('no_dokumen',  $request->no_dokumen);
            }
        } else {

            /**
             * query berdasarkan divisi user yang sedang login
             */
            $query->whereHas('budget.divisi', function (Builder $query) {
                $query->where('id', Auth::user()->divisi->id);
            });
        }

        /**
         * buat order
         */
        $transactions = $query->orderBy('tanggal', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        /**
         * ambil data bagian (divisi)
         */
        $divisi = Divisi::where('active', 1)
            ->orderBy('nama_divisi', 'asc')
            ->get();

        /**
         * ambil data akun belanja (jenis_belanja)
         */
        $akunBelanja = AkunBelanja::with('jenisBelanja')
            ->where('active', 1)
            ->orderBy('nama_akun_belanja', 'asc')
            ->get();

        /**
         * return view
         */
        return view('pages.transaksi.index', compact(
            'transactions',
            'divisi',
            'akunBelanja',
            'userAccess',
            'isAdmin',
            'periodeAwal',
            'periodeAkhir',
        ));
    }

    /**
     * view halaman tambah transaksi
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $noDocument = $this->generateNoDocument();
        return view('pages.transaksi.create', compact('noDocument'));
    }

    /**
     * Tambah data transaksi ke database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * validasi rule
         */
        $validateRules = [
            'budget_id'         => ['required', 'exists:budget,id'],
            'nama_divisi'       => ['required', 'exists:divisi,nama_divisi'],
            'nama_akun_belanja' => ['required', 'exists:akun_belanja,nama_akun_belanja'],
            'kategori_belanja'  => ['required', 'exists:jenis_belanja,kategori_belanja'],
            'tahun_anggaran'    => ['required', 'numeric', 'exists:budget,tahun_anggaran'],
            'sisa_budget'       => ['required', 'numeric'],
            'tanggal'           => ['required', 'date'],
            'kegiatan'          => ['required', 'max:100'],
            'approval'          => ['required', 'max:100'],
            'jumlah_nominal'    => ['required', 'numeric', 'min:0', "max:{$request->sisa_budget}"],
            'no_dokumen'        => ['required', 'string', 'unique:transaksi,no_dokumen', 'max:100'],
            'uraian'            => [],
        ];

        /**
         * pesan error validasi
         */
        $validateErrorMessage = [
            'budget_id.required'         => 'Akun belanja harus dipilih.',
            'budget_id.exists'           => 'Akun belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'nama_divisi.required'       => 'Bagian harus diisi.',
            'nama_divisi.exists'         => 'Bagian tidak ada.',
            'nama_akun_belanja.required' => 'Akun belanja harus dipilih.',
            'nama_akun_belanja.exists'   => 'Akun belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'kategori_belanja.required'  => 'Jenis belanja harus dipilih.',
            'kategori_belanja.exists'    => 'Jenis belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'tahun_anggaran.required'    => 'Tahun anggaran harus diisi.',
            'tahun_anggaran.numeric'     => 'Tahun anggaran harus tahun yang valid (yyyy).',
            'tahun_anggaran.exists'      => 'Tidak ada budget pada tahun anggaran yang masukan.',
            'sisa_budget.required'       => 'Sisa budget harus diisi.',
            'sisa_budget.numeric'        => 'Sisa budget harus bertipe angka yang valid.',
            'tanggal.required'           => 'Tanggal harus diisi.',
            'tanggal.date'               => 'Tanggal tidak valid, masukan tanggal yang valid.',
            'kegiatan.required'          => 'Kegiatan harus diisi.',
            'kegiatan.max'               => 'Kegiatan tidak boleh lebih dari 100 karakter.',
            'approval.required'          => 'Nama approval harus diisi.',
            'approval.max'               => 'Nama approval tidak lebih dari 100 karakter.',
            'jumlah_nominal.required'    => 'Jumlah nominal harus diisi.',
            'jumlah_nominal.numeric'     => 'Jumlah nominal harus bertipe angka yang valid.',
            'jumlah_nominal.min'         => 'Jumlah nominal tidak boleh kurang dari 0.',
            'jumlah_nominal.max'         => 'Jumlah nominal melebihi sisa nominal budget.',
            'no_dokumen.required'        => 'Nomer dokumen harus diisi.',
            'no_dokumen.unique'          => 'Nomer dokumen sudah digunakan.',
            'no_dokumen.max'             => 'Nomer dokumen tidak boleh lebih dari 100 karakter.',
        ];

        /**
         * cek file dokmen diupload atau tidak
         * jika diupload tambah validari rules & pesan error validasi.
         */
        if ($request->file_dokumen) {
            $validateRules['file_dokumen']             = ['file', 'max:10000'];
            $validateErrorMessage['file_dokumen.file'] = 'File dokumen gagal diupload.';
            $validateErrorMessage['file_dokumen.max']  = 'Ukuran file dokumen tidak boleh lebih dari 10.000 kilobytes.';
        }

        /**
         * jalankan validasi
         */
        $request->validate($validateRules, $validateErrorMessage);

        /**
         * Ambil data budget berdasarkan "id" budget yang di-request
         */
        $budget = Budget::find($request->budget_id);

        /**
         * cek file_dokumen di upload atau tidak
         * jika di upload simpan pada storage
         */
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $extension = $file->extension();
            $fileName = 'dokumen-' . date('Y-m-d-H-i-s') . '.' . $extension;
            $fileDocument = $file->storeAs('transaksi', $fileName);
        } else {
            $fileDocument = null;
        }

        try {

            /**
             * Proses tambah transaksi
             */
            Transaksi::create([
                'user_id'        => Auth::user()->id,
                'budget_id'      => $request->budget_id,
                'tanggal'        => $request->tanggal,
                'kegiatan'       => $request->kegiatan,
                'jumlah_nominal' => $request->jumlah_nominal,
                'approval'       => $request->approval,
                'no_dokumen'     => $request->no_dokumen,
                'file_dokumen'   => $fileDocument,
                'uraian'         => $request->uraian ?? null,
                'outstanding'    => (bool) $request->outstanding,
            ]);

            /**
             * kurangi sisa_nominal pada budget
             */
            $budget->sisa_nominal -= $request->jumlah_nominal;
            $budget->save();
        } catch (\Exception $e) {

            /**
             * return jika proses simpan gagal
             */
            return redirect()
                ->route('belanja.create')
                ->with('alert', [
                    'type'    => 'danger',
                    'message' => 'Gagal menambahkan data realisasi. ' . $e->getMessage(),
                ]);
        }

        /**
         * return jika proses simpan sukses.
         */
        return redirect()
            ->route('belanja.create')
            ->with('alert', [
                'type'    => 'success',
                'message' => 'Data realisasi berhasil ditambahkan.',
            ]);
    }

    /**
     * View detail data transaksi
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function show(Transaksi $transaksi)
    {
        $result = Transaksi::with('budget.divisi', 'budget.jenisBelanja', 'user.profil')->find($transaksi->id);
        $linkDownload = !empty($result->file_dokumen) ? route('belanja.download', ['transaksi' => $result->id]) : null;

        return response()->json([
            'transaksi' => $result,
            'download'  => $linkDownload
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaksi $transaksi)
    {
        return view('pages.transaksi.edit', compact('transaksi'));
    }

    /**
     * Update data transaksi
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        /**
         * validasi rule
         */
        $validateRules = [
            'budget_id'         => ['required', 'exists:budget,id'],
            'nama_divisi'       => ['required', 'exists:divisi,nama_divisi'],
            'kategori_belanja'  => ['required', 'exists:jenis_belanja,kategori_belanja'],
            'nama_akun_belanja' => ['required', 'exists:akun_belanja,nama_akun_belanja'],
            'tahun_anggaran'    => ['required', 'numeric', 'exists:budget,tahun_anggaran'],
            'sisa_budget'       => ['required', 'numeric'],
            'tanggal'           => ['required', 'date'],
            'kegiatan'          => ['required', 'max:100'],
            'approval'          => ['required', 'max:100'],
            'jumlah_nominal'    => ['required', 'numeric', 'min:0', "max:{$request->sisa_budget}"],
            'uraian'            => [],
        ];

        /**
         * pesan error validasi
         */
        $validateErrorMessage = [
            'budget_id.required'         => 'Akun belanja harus dipilih.',
            'budget_id.exists'           => 'Akun belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'nama_divisi.required'       => 'Bagian harus diisi.',
            'nama_divisi.exists'         => 'Bagian tidak ada.',
            'nama_akun_belanja.required' => 'Akun belanja harus dipilih.',
            'nama_akun_belanja.exists'   => 'Akun belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'kategori_belanja.required'  => 'Jenis belanja harus dipilih.',
            'kategori_belanja.exists'    => 'Jenis belanja tidak ada. Pilih akun belanja yang ditentukan.',
            'tahun_anggaran.required'    => 'Tahun anggaran harus diisi.',
            'tahun_anggaran.numeric'     => 'Tahun anggaran harus tahun yang valid (yyyy).',
            'tahun_anggaran.exists'      => 'Tidak ada budget pada tahun anggaran yang masukan.',
            'sisa_budget.required'       => 'Sisa budget harus diisi.',
            'sisa_budget.numeric'        => 'Sisa budget harus bertipe angka yang valid.',
            'tanggal.required'           => 'Tanggal harus diisi.',
            'tanggal.date'               => 'Tanggal tidak valid, masukan tanggal yang valid.',
            'kegiatan.required'          => 'Kegiatan harus diisi.',
            'kegiatan.max'               => 'Kegiatan tidak boleh lebih dari 100 karakter.',
            'approval.required'          => 'Nama approval harus diisi.',
            'approval.max'               => 'Nama approval tidak lebih dari 100 karakter.',
            'jumlah_nominal.required'    => 'Jumlah nominal harus diisi.',
            'jumlah_nominal.numeric'     => 'Jumlah nominal harus bertipe angka yang valid.',
            'jumlah_nominal.min'         => 'Jumlah nominal tidak boleh kurang dari 0.',
            'jumlah_nominal.max'         => 'Jumlah nominal melebihi sisa nominal budget.',
        ];

        /**
         * cek apakah no dokumen dirubah atau tidak
         * jika dirubah tambahkan validasi
         */
        if ($request->no_dokumen != $transaksi->no_dokumen) {
            $validateRules['no_dokumen']                 = ['required', 'unique:transaksi,no_dokumen', 'max:100'];
            $validateErrorMessage['no_dokumen.required'] = 'Nomer dokumen harus diisi.';
            $validateErrorMessage['no_dokumen.unique']   = 'Nomer dokumen sudah digunakan.';
            $validateErrorMessage['no_dokumen.max']      = 'Nomer dokumen tidak boleh lebih dari 100 karakter.';
        }

        /**
         * cek jika file dokumen di upload
         * jika diupload tambahakan validasi
         */
        if ($request->file_dokumen) {
            $validateRules['file_dokumen']             = ['file', 'max:10000'];
            $validateErrorMessage['file_dokumen.file'] = 'File dokumen gagal diupload.';
            $validateErrorMessage['file_dokumen.max']  = 'Ukuran file dokumen tidak boleh lebih dari 10.000 kilobytes.';
        }

        /**
         * jalankan validasi
         */
        $request->validate($validateRules, $validateErrorMessage);

        /**
         * mambil path file_dokumen sebelumnya
         */
        $fileDokumen = $transaksi->file_dokumen;

        /**
         * cek file_dokumen dirubah atau tidak.
         * jika dirubah hapus file_dokumen yang lama dan upload file_dokumen yang baru.
         */
        if ($request->hasFile('file_dokumen')) {

            /**
             * hapus file_dokumen lama dari storage jika ada
             */
            if (Storage::exists($transaksi->file_dokumen)) {
                Storage::delete($transaksi->file_dokumen);
            }

            /**
             * simpan file_dokumen baru ke storage
             */
            $file        = $request->file('file_dokumen');
            $extension   = $file->extension();
            $fileName    = 'dokumen-' . date('Y-m-d-H-i-s') . '.' . $extension;
            $fileDokumen = $file->storeAs('transaksi', $fileName);
        }

        /**
         * update database
         */
        try {

            /**
             * cek budget dirubah atau tidak
             * jika dirubah tambah nominal dan sisa_budget pada budget yang lama.
             * kurangi nominal dan sisa_nominal pada budget yang baru.
             */
            if ($transaksi->budget_id != $request->budget_id) {

                /**
                 * kembalikan (tambah) sisa_budget pada budget akun belanja yang lama
                 */
                $budgetLama = Budget::find($transaksi->budget_id);
                $budgetLama->sisa_nominal += $transaksi->jumlah_nominal;
                $budgetLama->save();

                /**
                 * kurangi sisa_nominal pada budget akun belanja yang baru
                 */
                $budgetBaru = Budget::find($request->budget_id);
                $budgetBaru->sisa_nominal -= $request->jumlah_nominal;
                $budgetBaru->save();
            } else {

                /**
                 * ambil data budget lama
                 */
                $budgetLama = Budget::find($transaksi->budget_id);

                /**
                 * jika budget tidak dirubah cek jumlah nominal lebih banyak...
                 * ...atau lebih sedikit dari jumlah_nominal sebelumnya.
                 *
                 * update sisa_nominal budget pada akun belanja (jenis_belanja) yang lama.
                 */
                if ($request->jumlah_nominal > $transaksi->jumlah_nominal) {

                    /**
                     * kurangi sisa_nominal pada budget lama
                     */
                    $budgetLama->sisa_nominal -= $request->jumlah_nominal - $transaksi->jumlah_nominal;
                    $budgetLama->save();
                } else if ($request->jumlah_nominal < $transaksi->jumlah_nominal) {

                    /**
                     * jika jumlah_nominal baru lebih sedikit dari jumlah_nominal lama.
                     * tambahkan sisa_nominal budget pada akun belanja (jenis_belanja) lama.
                     */
                    $budgetLama->sisa_nominal += $transaksi->jumlah_nominal - $request->jumlah_nominal;
                    $budgetLama->save();
                }
            }

            /**
             * update data transaksi (belanja)
             */
            Transaksi::where('id', $transaksi->id)
                ->update([
                    'user_id'        => Auth::user()->id,
                    'budget_id'      => $request->budget_id,
                    'tanggal'        => $request->tanggal,
                    'jumlah_nominal' => $request->jumlah_nominal,
                    'approval'       => $request->approval,
                    'no_dokumen'     => $request->no_dokumen,
                    'file_dokumen'   => Storage::exists($fileDokumen) ? $fileDokumen : null,
                    'uraian'         => $request->uraian ?? null,
                    'outstanding'    => (bool) $request->outstanding,
                ]);
        } catch (\Exception $e) {

            /**
             * response jika proses update gagal
             */
            return redirect()
                ->route('belanja.edit', ['transaksi' => $transaksi->id])
                ->with('alert', [
                    'type'    => 'danger',
                    'message' => 'Data realisasi gagal dirubah. ' . $e->getMessage(),
                ]);
        }

        /**
         * response update sukses
         */
        return redirect()
            ->route('belanja.edit', ['transaksi' => $transaksi->id])
            ->with('alert', [
                'type'    => 'success',
                'message' => 'Data realisasi berhasil dirubah.',
            ]);
    }

    /**
     * hapus data transaksi
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function delete(Transaksi $transaksi)
    {

        try {

            /**
             * update sisa_nominal pada budget
             */
            $budget = Budget::find($transaksi->budget_id);
            $budget->sisa_nominal += (int) $transaksi->jumlah_nominal;
            $budget->save();

            /**
             * jika ada hapus dari storage
             */
            if (Storage::exists($transaksi->file_dokumen)) {
                Storage::delete($transaksi->file_dokumen);
            }

            /**
             * Hapus data belanja (transaksi) dari database
             */
            Transaksi::destroy($transaksi->id);
        } catch (\Exception $e) {

            /**
             * response jika proses hapus gagal
             */
            return redirect()
                ->route('belanja')
                ->with('alert', [
                    'type'    => 'danger',
                    'message' => 'Gagal menghapus realisasi. ' . $e->getMessage(),
                ]);
        }

        /**
         * response jika proses hapus berhasil.
         */
        return redirect()
            ->route('belanja')
            ->with('alert', [
                'type'    => 'success',
                'message' => '1 data realisasi berhasil dihapus.',
            ]);
    }

    /**
     * Download file dokumen
     *
     * @param Transaksi $transaksi
     *
     * @return void
     */
    public function download(Transaksi $transaksi)
    {
        /**
         * cek file ada atau tidak pada storage
         * jika ada jalankan proses download
         */
        if (Storage::exists($transaksi->file_dokumen)) {
            return Storage::download($transaksi->file_dokumen);
        } else {
            return redirect()
                ->route('belanja')
                ->with('alert', [
                    'type'    => 'info',
                    'message' => 'File dokumen tidak tersedia.',
                ]);
        }
    }

    /**
     * filter data untuk export excel & print pdf
     *
     * @param mixed $request
     *
     * @return array
     */
    public function fillter($request)
    {
        /**
         * ambil user menu akses
         */
        $userAccess = $this->getAccess(href: '/belanja');

        /**
         * ambil data user sebagai admin atau bukan
         */
        $isAdmin = $this->isAdmin(href: '/belanja');

        /**
         * periode default
         */
        $periodeAwal  = $request->periode_awal ?? date('Y-m-d', time() - (60 * 60 * 24 * 14));
        $periodeAkhir = $request->periode_akhir ?? date('Y-m-d');

        /**
         * Query join table transaksi, divisi, user & profil
         */
        $query = Transaksi::with('budget.divisi', 'budget.jenisBelanja.akunBelanja', 'user.profil')
            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);

        /**
         * jika jenis belanja dipilih tambahkan query
         */
        if ($request->jenis_belanja) {
            $query->whereHas('budget.jenisBelanja', function (Builder $query) use ($request) {
                $query->where('kategori_belanja', $request->jenis_belanja);
            });
        }

        /**
         * Cek user admin atau tidak
         */
        if ($isAdmin) {

            /**
             * jika divisi dipilih tambahkan query
             */
            if ($request->divisi != null) {
                $query->whereHas('budget.divisi', function (Builder $query) use ($request) {
                    $query->where('nama_divisi', $request->divisi);
                });
            }

            /**
             * jika no dokumen dipilih tambahkan validasi
             */
            if ($request->no_dokumen != null) {
                $query->where('no_dokumen',  $request->no_dokumen);
            }
        } else {

            /**
             * query berdasarkan divisi user yang sedang login
             */
            $query->whereHas('budget.divisi', function (Builder $query) {
                $query->where('id', Auth::user()->divisi->id);
            });
        }

        /**
         * hitung jumlah nominal;
         */
        $totalTransaksi = $query->sum('jumlah_nominal');

        /**
         * buat order
         */
        $laporanTransaksi = $query->orderBy('tanggal', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();


        return [
            'laporanTransaksi' => $laporanTransaksi,
            'totalTransaksi'   => $totalTransaksi,
            'userAccess'       => $userAccess,
            'isAdmin'          => $isAdmin,
            'periodeAwal'      => $periodeAwal,
            'periodeAkhir'     => $periodeAkhir,
        ];
    }

    /**
     * export excel
     *
     * @param Request $request
     *
     * @return download
     */
    public function exportExcel(Request $request)
    {
        $data = $this->fillter($request);
        $dateTime = date('Y_m_d_h_i_s');

        return Excel::download(
            new LaporanTransaksiExport($data),
            "Realisasi_{$dateTime}.xlsx"
        );
    }

    /**
     * print PDF
     *
     * @param Request $request
     */
    public function exportPdf(Request $request)
    {
        $data = $this->fillter($request);

        $pdf = PDF::loadView(
            'export.pdf.laporan-transaksi',
            $data,
            [],
            [
                'orientation'   => 'L',
                'margin_header' => 5,
                'margin_footer' => 5,
                'margin_top'    => 37,
                'margin_bottom' => 18,
            ]
        );

        $dateTime = date('Y_m_d_h_i_s');

        // return $pdf->stream('Realisasi (' . date('Y-m-d h.i.s') . ').pdf');
        return $pdf->stream("Realisasi_{$dateTime}.pdf");
    }

    /**
     * Fungsi membuat data tabel jenis_belanja
     *
     */
    public function dataTable()
    {
        /**
         * Option where query builder
         */
        $whereQuery = [
            ['divisi.id', Auth::user()->divisi_id],
            ['divisi.active', 1],
            ['akun_belanja.active', 1],
            ['jenis_belanja.active', 1],
        ];

        /**
         * Option select query builder
         */
        $selectQuery = [
            'budget.id',
            'budget.tahun_anggaran',
            'budget.sisa_nominal',
            'divisi.nama_divisi',
            'jenis_belanja.kategori_belanja',
            'akun_belanja.nama_akun_belanja',
        ];

        /**
         * Ambil divisi (bagian) pada user yang sedang login
         */
        $userDivisi = strtolower(Auth::user()->divisi->nama_divisi);

        /**
         * Cek jika divisi (bagian) dari user merupakan kepala kantor...
         * ...atau bagian umum maka update $whereQuery
         */
        if ($userDivisi == "kepala kantor" || $userDivisi == "bagian umum") {
            $whereQuery = [
                ['divisi.active', 1],
                ['akun_belanja.active', 1],
                ['jenis_belanja.active', 1],
            ];
        }

        /**
         * Query budget (pagu)
         */
        $budgets = Budget::leftJoin('divisi', 'divisi.id', '=', 'budget.divisi_id')
            ->leftJoin('jenis_belanja', 'jenis_belanja.id', '=', 'budget.jenis_belanja_id')
            ->leftJoin('akun_belanja', 'akun_belanja.id', '=', 'jenis_belanja.akun_belanja_id')
            ->where($whereQuery)
            ->select($selectQuery)
            ->orderBy('budget.tahun_anggaran', 'desc')
            ->orderBy('divisi.nama_divisi', 'asc')
            ->orderBy('akun_belanja.nama_akun_belanja', 'asc')
            ->orderBy('jenis_belanja.kategori_belanja', 'asc')
            ->get();

        /**
         * Buat datatable
         */
        return DataTables::of($budgets)
            ->addColumn('action', function ($budget) {
                return "
                    <button
                        onclick='transaksi.setFormValue({$budget->id}, {$budget->tahun_anggaran}, \"{$budget->nama_divisi}\", \"{$budget->nama_akun_belanja}\", \"{$budget->kategori_belanja}\", {$budget->sisa_nominal})'
                        class='btn btn-sm btn-success btn-sm'
                    >
                        <i class='mdi mdi-hand-pointing-up'></i>
                        <span>Pilih</span>
                    </button>
                ";
            })->make(true);
    }
}
