$(document).ready(function () {
    // Inisialisasi DataTables untuk tabel riwayat tanpa drawCallback
    var table = $("#tabelRiwayat").DataTable({
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: '<i class="bi bi-skip-start"></i>',
                last: '<i class="bi bi-skip-end"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>',
            },
        },
    });

    // Menambahkan event listener untuk tombol detail
    $("#detailModal").on("hide.bs.modal", function (event) {
        $("#tabelDetail").DataTable().destroy();
    });

    $("#detailModal").on("show.bs.modal", function (event) {
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
                $("#benar").text(data.jumlah_benar);
                $("#kategori").text(data.kategori.indonesia);
                $("#kata").text(data.jumlah_kata);
                $("#kesulitan").text(data.tingkat_kesulitan.tingkat_kesulitan);
                $("#waktu").text(
                    calculateDuration(data.created_at, data.updated_at)
                );

                // Menampilkan data detail latihan dalam tabel
                const tabelDetailBody = $("#tabelDetailBody");
                tabelDetailBody.empty();
                JSON.parse(data.list).forEach((list, index) => {
                    const row = `<tr class="align-middle">
                        <td class="text-nowrap">${list.kata}</td>
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
                var detailTable = $("#tabelDetail").DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    language: {
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan _PAGE_ dari _PAGES_ halaman",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        search: "Cari:",
                        paginate: {
                            first: '<i class="bi bi-skip-start"></i>',
                            last: '<i class="bi bi-skip-end"></i>',
                            next: '<i class="bi bi-chevron-right"></i>',
                            previous: '<i class="bi bi-chevron-left"></i>',
                        },
                    },
                });
                detailTable.draw(); // Redraw tabel detail setelah data diupdate
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
