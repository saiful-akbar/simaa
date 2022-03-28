class Dashboard {
    constructor() {
        this.chartByAkunBelanja = {
            tahunAnggaran: new Date().getFullYear(),
            divisiId: "",
            akunBelanjaId: "",
            jenisBelanjaId: "",
        };
    }
    setGlobalBudgetChart(a) {
        $.ajax({
            type: "get",
            url: `${main.baseUrl}/dashboard/chart/${a}`,
            dataType: "json",
            success: function (a) {
                $("#global-divisi").text(a.namaDivisi),
                    $("#global-total-budget").text(
                        "Rp. " + main.formatRupiah(a.totalBudget)
                    ),
                    $("#global-sisa-budget").text(
                        "Rp. " + main.formatRupiah(a.sisaBudget)
                    ),
                    $("#global-total-belanja").text(
                        "Rp. " + main.formatRupiah(a.totalTransaksi)
                    );
                const t = {
                        chart: { height: 300, type: "pie" },
                        legend: { show: !1 },
                        fill: { type: "gradient" },
                        series: [a.totalTransaksi, a.sisaBudget],
                        labels: ["Total Belanja", "Sisa Budget"],
                        colors: ["#0acf97", "#727cf5"],
                        responsive: [
                            {
                                breakpoint: 480,
                                options: { chart: { height: 220 } },
                            },
                        ],
                    },
                    e = new ApexCharts(
                        document.querySelector("#global-chart"),
                        t
                    );
                e.render(), e.updateSeries([a.totalTransaksi, a.sisaBudget]);
            },
        });
    }
    setBudgetChartByDivisi(a, t) {
        $.ajax({
            type: "get",
            url: `${main.baseUrl}/dashboard/chart/admin/${a}/${t}/divisi`,
            dataType: "json",
            success: (t) => {
                $(`#total-budget-divisi-${a}`).text(
                    "Rp. " + main.formatRupiah(t.totalBudget)
                ),
                    $(`#sisa-budget-divisi-${a}`).text(
                        "Rp. " + main.formatRupiah(t.sisaBudget)
                    ),
                    $(`#total-belanja-divisi-${a}`).text(
                        "Rp. " + main.formatRupiah(t.totalTransaksi)
                    );
                const e = {
                        chart: { height: 230, type: "donut" },
                        legend: { show: !1 },
                        series: [t.totalTransaksi, t.sisaBudget],
                        labels: ["Total Transaksi", "Sisa Budget"],
                        colors: ["#0acf97", "#fa5c7c"],
                        responsive: [
                            {
                                breakpoint: 480,
                                options: { chart: { height: "100%" } },
                            },
                        ],
                    },
                    n = new ApexCharts(
                        document.querySelector(`#divisi-${a}`),
                        e
                    );
                n.render(), n.updateSeries([t.totalTransaksi, t.sisaBudget]);
            },
        });
    }
    setBudgetChartByAkunBelanja() {
        let a = `${main.baseUrl}/dashboard/chart/admin/akun-belanja`;
        (a += `?tahun_anggaran=${this.chartByAkunBelanja.tahunAnggaran}`),
            (a += `&divisi=${this.chartByAkunBelanja.divisiId}`),
            (a += `&akun_belanja=${this.chartByAkunBelanja.akunBelanjaId}`),
            (a += `&jenis_belanja=${this.chartByAkunBelanja.jenisBelanjaId}`),
            $.ajax({
                type: "get",
                url: a,
                dataType: "json",
                success: function (a) {
                    $("#admin__chart-by-akun-belanja__total-budget").text(
                        "Rp. " + main.formatRupiah(a.totalBudget)
                    ),
                        $(
                            "#admin__chart-by-akun-belanja__total-transaksi"
                        ).text("Rp. " + main.formatRupiah(a.totalTransaksi)),
                        $("#admin__chart-by-akun-belanja__sisa-budget").text(
                            "Rp. " + main.formatRupiah(a.sisaBudget)
                        );
                    const t = {
                            chart: { height: 300, type: "pie" },
                            legend: { show: !1 },
                            fill: { type: "gradient" },
                            series: [a.totalTransaksi, a.sisaBudget],
                            labels: ["Total Belanja", "Sisa Budget"],
                            colors: ["#39afd1", "#ffbc00"],
                            responsive: [
                                {
                                    breakpoint: 480,
                                    options: { chart: { height: 220 } },
                                },
                            ],
                        },
                        e = new ApexCharts(
                            document.querySelector(
                                "#admin__chart-by-akun-belanja"
                            ),
                            t
                        );
                    e.render(),
                        e.updateSeries([a.totalTransaksi, a.sisaBudget]);
                },
            });
    }
    setTransaksiChartLine(a) {
        $.ajax({
            type: "get",
            url: `${main.baseUrl}/dashboard/chart/divisi/${a}/jenis-belanja`,
            dataType: "json",
            success: (a) => {
                const t = {
                        chart: {
                            height: 364,
                            type: "line",
                            dropShadow: {
                                enabled: !0,
                                opacity: 0.2,
                                blur: 7,
                                left: -7,
                                top: 7,
                            },
                        },
                        title: {
                            text: "Grafik Transaksi Belanja per Akun Belanja",
                            align: "left",
                        },
                        stroke: { curve: "smooth", with: 4 },
                        series: a.data,
                        xaxis: {
                            type: "string",
                            categories: [
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "Mei",
                                "Jun",
                                "Jul",
                                "Agus",
                                "Sep",
                                "Okt",
                                "Nov",
                                "Des",
                            ],
                            tooltip: { enabled: !0 },
                            axisBorder: { show: !0 },
                        },
                        yaxis: {
                            title: { text: "Nominal Transaksi Belanja" },
                            labels: {
                                formatter: function (a) {
                                    return "Rp. " + main.formatRupiah(a);
                                },
                            },
                        },
                        legend: {
                            position: "bottom",
                            horizontalAlign: "center",
                            itemMargin: { horizontal: 5, vertical: 5 },
                        },
                    },
                    e = new ApexCharts(
                        document.querySelector("#divisi__transaksi-chart-line"),
                        t
                    );
                e.render(), e.updateSeries(a.data);
            },
        });
    }
}
const dashboard = new Dashboard();
$(document).ready(function () {
    dashboard.setGlobalBudgetChart(new Date().getFullYear());
    const a = $("#divisi__transaksi-chart-line");
    $("#admin__chart-by-akun-belanja").length > 0 &&
        (dashboard.setBudgetChartByAkunBelanja(),
        $("#admin__chart-by-akun-belanja__select-tahun-anggaran").change(
            function (a) {
                a.preventDefault(),
                    (dashboard.chartByAkunBelanja.tahunAnggaran =
                        $(this).val()),
                    dashboard.setBudgetChartByAkunBelanja();
            }
        ),
        $("#admin__chart-by-akun-belanja__select-divisi").change(function (a) {
            a.preventDefault(),
                (dashboard.chartByAkunBelanja.divisiId = $(this).val()),
                dashboard.setBudgetChartByAkunBelanja();
        }),
        $("#admin__chart-by-akun-belanja__select-akun-belanja").change(
            function (a) {
                a.preventDefault(),
                    (dashboard.chartByAkunBelanja.akunBelanjaId =
                        $(this).val()),
                    dashboard.setBudgetChartByAkunBelanja();
            }
        ),
        $("#admin__chart-by-akun-belanja__select-jenis-belanja").change(
            function (a) {
                a.preventDefault(),
                    (dashboard.chartByAkunBelanja.jenisBelanjaId =
                        $(this).val()),
                    dashboard.setBudgetChartByAkunBelanja();
            }
        )),
        a.length > 0 &&
            (dashboard.setTransaksiChartLine(new Date().getFullYear()),
            $("#divisi__transaksi-chart-line__select-tahun-anggaran").change(
                function (a) {
                    a.preventDefault(),
                        dashboard.setTransaksiChartLine($(this).val());
                }
            )),
        $("#periode-global").change(function (a) {
            a.preventDefault(), dashboard.setGlobalBudgetChart($(this).val());
        }),
        $.each($(".divisi-chart"), function (a, t) {
            const e = $(t).data("divisi-id");
            dashboard.setBudgetChartByDivisi(e, new Date().getFullYear());
        }),
        $(".periode-divisi").change(function (a) {
            a.preventDefault(),
                dashboard.setBudgetChartByDivisi(
                    $(this).data("divisi-id"),
                    $(this).val()
                );
        });
});
