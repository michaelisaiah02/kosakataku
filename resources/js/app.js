import $ from "jquery";
window.$ = window.jQuery = $;

import "./bootstrap";

import Swal from "sweetalert2";
window.Swal = Swal;

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";

$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
    });

    $(".dropdown").on("show.bs.dropdown", function () {
        $(this).find(".dropdown-menu").first().stop(true, true).fadeIn(150);
    });

    $(".dropdown").on("hide.bs.dropdown", function () {
        $(this).find(".dropdown-menu").first().stop(true, true).fadeOut(0);
    });

    $("#copy-link").on("click", function () {
        copyToClipboard("http://kosakataku.my.id");
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(
            function () {
                $("#copy-link").text("Berhasil menyalin link.");
                setTimeout(function () {
                    $("#copy-link").text("sini");
                }, 1000);
            },
            function (err) {
                $("#copy-link").text("Gagal menyalin link.");
                console.error("Gagal menyalin link: ", err);
            }
        );
    }
});
