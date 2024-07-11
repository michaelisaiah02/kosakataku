import "./bootstrap";
import $ from "jquery";
window.$ = window.jQuery = $;

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
});

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
