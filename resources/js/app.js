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
});

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
