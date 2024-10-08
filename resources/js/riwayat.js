$(document).ready(function () {
    // Inisialisasi DataTables untuk tabel riwayat tanpa drawCallback
    $("#tabelRiwayatPengucapan").DataTable({
        scrollX: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: '<i class="bi bi-search fs-5"></i>',
            paginate: {
                first: '<i class="bi bi-skip-start"></i>',
                last: '<i class="bi bi-skip-end"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
            },
        },
    });

    $("#tabelRiwayatArtiKata").DataTable({
        scrollX: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: '<i class="bi bi-search fs-5"></i>',
            paginate: {
                first: '<i class="bi bi-skip-start"></i>',
                last: '<i class="bi bi-skip-end"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
            },
        },
    });

    // Menambahkan event listener untuk tombol detail
    $("#detailModalPengucapan").on("hide.bs.modal", function (event) {
        $("#tabelDetail").DataTable().destroy();
    });

    $("#detailModalArtiKata").on("hide.bs.modal", function (event) {
        $("#tabelDetail").DataTable().destroy();
    });

    $("#detailModalPengucapan").on("show.bs.modal", function (event) {
        const button = $(event.relatedTarget);
        const latihanId = button.data("id");

        // Mengambil data dari server
        fetch(`${window.location.origin}/detail-riwayat/${latihanId}`)
            .then((response) => response.json())
            .then((data) => {
                $("#tanggal").text(
                    new Date(data.created_at).toLocaleString("ID", {
                        weekday: "long",
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    }) +
                        " - " +
                        new Date(data.created_at).toLocaleTimeString("ID", {
                            hour: "numeric",
                            minute: "numeric",
                            second: "numeric",
                        })
                );
                $("#bahasa").text(data.bahasa.indonesia);
                $("#benar").text(data.jumlah_pengucapan_benar);
                $("#kategori").text(data.kategori.indonesia);
                $("#waktu").text(
                    calculateDuration(data.created_at, data.updated_at)
                );

                // Menampilkan data detail latihan dalam tabel
                const tabelDetailBody = $("#tabelDetailBody");
                tabelDetailBody.empty();
                JSON.parse(data.list_latihan_kosakata).forEach((list) => {
                    const row = `<tr class="align-middle">
                        <td class="text-nowrap">${list.kata}</td>
                        <td class="text-nowrap">${list.cara_baca}</td>
                        <td class="text-nowrap">${list.terjemahan}</td>
                        <td class="text-center">${list.percobaan}</td>
                        <td class="text-center">${Math.round(list.durasi)}</td>
                        <td class="text-center">${
                            list.benar
                                ? '<i class="bi bi-check text-success"></i>'
                                : '<i class="bi bi-x text-danger"></i>'
                        }</td>
                    </tr>`;
                    tabelDetailBody.append(row);
                });
                $("#tabelDetail")
                    .DataTable({
                        language: {
                            lengthMenu: "Tampilkan _MENU_ data per halaman",
                            zeroRecords: "Tidak ada data yang ditemukan",
                            info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
                            infoEmpty: "Tidak ada data tersedia",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            search: '<i class="bi bi-search fs-5"></i>',
                            paginate: {
                                first: '<i class="bi bi-skip-start"></i>',
                                last: '<i class="bi bi-skip-end"></i>',
                                next: '<i class="bi bi-chevron-right"></i>',
                                previous: '<i class="bi bi-chevron-left"></i>',
                            },
                        },
                    })
                    .draw();
            })
            .catch((error) =>
                console.error("Error fetching latihan data:", error)
            );
    });

    $("#detailModalArtiKata").on("show.bs.modal", function (event) {
        const button = $(event.relatedTarget);
        const latihanId = button.data("id");

        // Mengambil data dari server
        fetch(`${window.location.origin}/detail-riwayat/${latihanId}`)
            .then((response) => response.json())
            .then((data) => {
                $("#tanggal").text(
                    new Date(data.created_at).toLocaleString("ID", {
                        weekday: "long",
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    }) +
                        " - " +
                        new Date(data.created_at).toLocaleTimeString("ID", {
                            hour: "numeric",
                            minute: "numeric",
                            second: "numeric",
                        })
                );
                $("#bahasa").text(data.bahasa.indonesia);
                $("#benar").text(data.jumlah_artikata_benar);
                $("#kategori").text(data.kategori.indonesia);
                $("#waktu").text(
                    calculateDuration(data.created_at, data.updated_at)
                );

                // Menampilkan data detail latihan dalam tabel
                const tabelDetailBody = $("#tabelDetailBody");
                tabelDetailBody.empty();
                JSON.parse(data.list_latihan_kosakata).forEach((list) => {
                    const row = `<tr class="align-middle">
                        <td class="text-nowrap">${list.kata}</td>
                        <td class="text-nowrap">${list.cara_baca}</td>
                        <td class="text-nowrap">${list.terjemahan}</td>
                        <td class="text-center">${list.percobaan}</td>
                        <td class="text-center">${Math.round(list.durasi)}</td>
                        <td class="text-center">${
                            list.benar
                                ? '<i class="bi bi-check text-success"></i>'
                                : '<i class="bi bi-x text-danger"></i>'
                        }</td>
                    </tr>`;
                    tabelDetailBody.append(row);
                });
                $("#tabelDetail")
                    .DataTable({
                        language: {
                            lengthMenu: "Tampilkan _MENU_ data per halaman",
                            zeroRecords: "Tidak ada data yang ditemukan",
                            info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
                            infoEmpty: "Tidak ada data tersedia",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            search: '<i class="bi bi-search fs-5"></i>',
                            paginate: {
                                first: '<i class="bi bi-skip-start"></i>',
                                last: '<i class="bi bi-skip-end"></i>',
                                next: '<i class="bi bi-chevron-right"></i>',
                                previous: '<i class="bi bi-chevron-left"></i>',
                            },
                        },
                    })
                    .draw();
            })
            .catch((error) =>
                console.error("Error fetching latihan data:", error)
            );
    });

    // Function to calculate the duration
    function calculateDuration(start, end) {
        const startTime = new Date(start);
        const endTime = new Date(end);
        const duration = (endTime - startTime) / 1000; // durasi dalam detik
        const hours = Math.floor(duration / 3600);
        const minutes = Math.floor((duration % 3600) / 60);
        const seconds = Math.floor(duration % 60);

        let result = "";
        if (hours > 0) result += `${hours} jam `;
        if (minutes > 0) result += `${minutes} menit `;
        if (seconds > 0) result += `${seconds} detik`;

        return result.trim();
    }
});
