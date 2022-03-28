class Transaksi {
    constructor() {
        this.dataTableBudget = null;
    }

    delete(a) {
        bootbox.confirm({
            title: "Anda insin menghapus data belanja?",
            message: String.raw`
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">
                        <i class="dripicons-warning mr-1"></i>
                        Peringatan!
                    </h4>
                    <ul>
                        <li>Tindakan ini tidak dapat dibatalkan.</li>
                        <li>Data belanja yang dihapus tidak dapat dikembalikan.</li>
                        <li>Pastikan anda berhati-hati dalam menghapus data.</li>
                    </ul>
                </div>
            `,
            buttons: {
                confirm: {
                    label: String.raw`<i class='mdi mdi-delete mr-1'></i> Hapus`,
                    className: "btn btn-danger btn-sm btn-rounded",
                },
                cancel: {
                    label: String.raw`<i class='mdi mdi-close-circle mr-1'></i> Batal`,
                    className: "btn btn-sm btn-dark btn-rounded mr-2",
                },
            },
            callback: (t) => {
                if (t) {
                    const t = $("#form-delete-transaksi");
                    t.attr("action", `${main.baseUrl}/belanja/${a}`),
                        t.submit();
                }
            },
        });
    }

    showModalLoading(a) {
        a
            ? ($("#modal-detail-loading").show(),
              $("#modal-detail-content").hide())
            : ($("#modal-detail-loading").hide(),
              $("#modal-detail-content").show());
    }

    showDetail(a) {
        $("#modal-detail").modal("show"),
            this.showModalLoading(!0),
            $.ajax({
                type: "GET",
                url: `${main.baseUrl}/belanja/${a}`,
                data: { _token: main.csrfToken },
                dataType: "json",
                success: (a) => {
                    this.showModalLoading(!1);
                    const { nama_divisi: t } = a.transaksi.budget.divisi,
                        { kategori_belanja: e } =
                            a.transaksi.budget.jenis_belanja,
                        { nama_lengkap: n } = a.transaksi.user.profil,
                        {
                            uraian: l,
                            tanggal: i,
                            approval: d,
                            kegiatan: s,
                            no_dokumen: o,
                            jumlah_nominal: r,
                            created_at: m,
                            updated_at: u,
                        } = a.transaksi;
                    $("#detail-uraian").html(l),
                        $("#detail-nama-divisi").text(t),
                        $("#detail-kategori-belanja").text(e),
                        $("#detail-submitter").text(n),
                        $("#detail-approval").text(d),
                        $("#detail-created-at").text(m),
                        $("#detail-updated-at").text(u),
                        $("#detail-kegiatan").text(s),
                        $("#detail-tanggal").text(i),
                        $("#detail-no-dokumen").text(o),
                        $("#detail-jumlah-nominal").text(
                            "Rp. " + main.formatRupiah(r)
                        ),
                        null !== a.download
                            ? $("#detail-download-dokumen").html(String.raw`
                                    <a href="${a.download}" class="btn btn-light btn-sm btn-rounded">
                                        <i class="mdi mdi-download mr-1"></i>
                                        <span>Unduh</span>
                                    </a>
                                `)
                            : $("#detail-download-dokumen").text(
                                  "File tidak tersedia"
                              );
                },
            });
    }
    closeDetail() {
        $("#modal-detail").modal("hide");
    }
    showModalTableBudget(a) {
        $("#modal-table-budget").modal(a ? "show" : "hide"),
            null == this.dataTableBudget &&
                (this.dataTableBudget = $("#datatable-budget").DataTable({
                    processing: !0,
                    serverSide: !0,
                    pageLength: 20,
                    lengthChange: !1,
                    scrollX: !0,
                    destroy: !1,
                    info: !1,
                    scrollY: "300px",
                    scrollCollapse: !0,
                    ajax: `${main.baseUrl}/belanja/budget/datatable`,
                    pagingType: "simple",
                    language: { paginate: { previous: "Prev", next: "Next" } },
                    columns: [
                        {
                            data: "action",
                            name: "action",
                            orderable: !1,
                            searchable: !1,
                            className: "text-center",
                        },
                        { data: "tahun_anggaran", name: "tahun_anggaran" },
                        { data: "nama_divisi", name: "nama_divisi" },
                        {
                            data: "nama_akun_belanja",
                            name: "nama_akun_belanja",
                        },
                        { data: "kategori_belanja", name: "kategori_belanja" },
                        {
                            data: "sisa_nominal",
                            name: "sisa_nominal",
                            render: (a) => "Rp. " + main.formatRupiah(a),
                        },
                    ],
                }));
    }

    setFormValue(a, t, e, n, l, i) {
        this.showModalTableBudget(!1),
            $("#budget_id").val(a),
            $("#nama_akun_belanja").val(n),
            $("#kategori_belanja").val(l),
            $("#nama_divisi").val(e),
            $("#tahun_anggaran").val(t),
            $("#sisa_budget").val(i),
            $("#jumlah_nominal").attr("max", i);
    }
}

const transaksi = new Transaksi();

$(document).ready(function () {
    $("#uraian").summernote({
        placeholder: "Masukan uraian...",
        height: 230,
        toolbar: [
            ["style", ["style"]],
            ["font", ["bold", "underline"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["view", ["fullscreen", "help"]],
        ],
    });

    $("#file_dokumen").change(function (a) {
        const t = $("#file-name"),
            { files: e } = $(this)[0];
        e.length > 0
            ? (t.removeClass("d-none"), t.text(e[0].name))
            : "create" === t.data("action")
            ? t.addClass("d-none")
            : t.text(t.data("file"));
    });

    $("button[type=reset]").click(function (a) {
        const t = $("#file-name");
        "create" === t.data("action")
            ? t.addClass("d-none")
            : t.text(t.data("file"));
    });

    $(".btn-export").click(function (a) {
        a.preventDefault();
        const t = $(this).data("route"),
            e = $("#form-export");
        e.attr("action", t), e.submit();
    });
});
