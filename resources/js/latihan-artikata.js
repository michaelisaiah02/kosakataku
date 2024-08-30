$(document).ready(function () {
    let soalArtiKata = [];
    let currentQuestionIndex = 0;
    let totalCorrect = 0;
    let answeredQuestions = [];
    let questionStartTime = null;
    const idLatihan = $("#data").data("id-latihan");

    function isNewSession() {
        console.log(localStorage);
        console.log(parseInt(localStorage.getItem("idLatihan")), idLatihan);
        return parseInt(localStorage.getItem("idLatihan")) !== idLatihan;
    }

    function clearLocalStorage() {
        localStorage.removeItem("idLatihan");
        localStorage.removeItem("currentQuestionIndex");
        localStorage.removeItem("totalCorrect");
        localStorage.removeItem("answeredQuestions");
        localStorage.removeItem("soalArtiKata");
    }

    function saveToLocalStorage() {
        localStorage.setItem("idLatihan", idLatihan);
        localStorage.setItem("currentQuestionIndex", currentQuestionIndex);
        localStorage.setItem("totalCorrect", totalCorrect);
        localStorage.setItem(
            "answeredQuestions",
            JSON.stringify(answeredQuestions)
        );
        localStorage.setItem("soalArtiKata", JSON.stringify(soalArtiKata));
    }

    function loadFromLocalStorage() {
        currentQuestionIndex =
            parseInt(localStorage.getItem("currentQuestionIndex")) || 0;
        totalCorrect = parseInt(localStorage.getItem("totalCorrect")) || 0;
        answeredQuestions =
            JSON.parse(localStorage.getItem("answeredQuestions")) || [];
        soalArtiKata = JSON.parse(localStorage.getItem("soalArtiKata")) || [];
    }

    // Ambil data soal dari server
    function fetchSoal() {
        if (isNewSession()) {
            clearLocalStorage();
            $.ajax({
                url: `/latihan/${idLatihan}/artikata/soal`,
                method: "GET",
                success: function (response) {
                    soalArtiKata = response;
                    saveToLocalStorage();
                    showQuestion();
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching questions:", error);
                    Swal.fire({
                        title: "Terjadi kesalahan saat mengambil soal!",
                        text: "Silakan coba lagi.",
                        icon: "warning",
                    });
                },
            });
        } else {
            loadFromLocalStorage();
            showQuestion();
        }
    }

    function showQuestion() {
        if (currentQuestionIndex >= soalArtiKata.length) {
            console.log(currentQuestionIndex, soalArtiKata.length);
            Swal.fire({
                title: "Selamat, Kamu Telah Menyelesaikan Latihan!",
                text: `Jumlah jawaban benar: ${totalCorrect} dari ${soalArtiKata.length} soal.`,
                icon: "success",
            });
            // finishLatihan();
            return;
        }

        questionStartTime = new Date();

        const question = soalArtiKata[currentQuestionIndex];
        $("#kata").text(question.pertanyaan);

        // Set pilihan jawaban
        for (let i = 0; i < 3; i++) {
            $(`#option${i + 1}`).val(question.pilihan[i]);
            $(`label[for="option${i + 1}"]`)
                .text(question.pilihan[i])
                .removeClass("btn-success btn-danger")
                .addClass("btn-outline-primary")
                .prop("disabled", false);
        }

        // Reset pilihan dan tombol
        $('input[name="options"]')
            .prop("checked", false)
            .prop("disabled", false);
        $("#checkBtn").prop("disabled", false);
        $("#nextBtn").hide();
        $("#finishBtn").hide();

        // Jika pertanyaan ini sudah dijawab sebelumnya, tampilkan hasilnya
        const answeredQuestion = answeredQuestions[currentQuestionIndex];
        if (answeredQuestion) {
            $(`input[value="${answeredQuestion.jawaban_user}"]`).prop(
                "checked",
                true
            );
            $("#checkBtn").click();
        }
    }

    $("#checkBtn").on("click", function () {
        const selectedAnswer = $('input[name="options"]:checked').val();
        if (!selectedAnswer) {
            Swal.fire({
                title: "Kamu Belum Menjawab!",
                text: "Silakan pilih jawaban terlebih dahulu.",
                icon: "warning",
            });
            return;
        }

        const currentQuestion = soalArtiKata[currentQuestionIndex];
        const isCorrect = selectedAnswer === currentQuestion.jawaban_benar;

        // Disable semua tombol pilihan
        $('input[name="options"]').prop("disabled", true);
        $('label[for^="option"]').prop("disabled", true);

        // Calculate duration
        const endTime = new Date();
        const duration = (endTime - questionStartTime) / 1000; // in seconds

        // Ubah warna tombol yang dipilih
        const selectedLabel = $(
            `label[for="${$('input[name="options"]:checked').attr("id")}"]`
        );
        selectedLabel
            .removeClass("btn-outline-primary")
            .addClass(isCorrect ? "btn-success" : "btn-danger");

        if (isCorrect) {
            totalCorrect++;
            playAudio("benar");
            Swal.fire({
                title: "Hore...",
                text: "Jawabanmu Benar!",
                icon: "success",
            });
        } else {
            playAudio("salah");
            Swal.fire({
                title: "Jawabanmu Kurang Tepat!",
                html: `Jawaban yang benar adalah: <strong>${currentQuestion.jawaban_benar}</strong>`,
                icon: "error",
            });
            const correctLabel = $(
                `label:contains('${currentQuestion.jawaban_benar}')`
            );
            correctLabel
                .removeClass("btn-outline-primary")
                .addClass("btn-success");
        }

        answeredQuestions[currentQuestionIndex] = {
            duration: duration,
            pertanyaan: currentQuestion.pertanyaan,
            jawaban_user: selectedAnswer,
            jawaban_benar: currentQuestion.jawaban_benar,
            benar: isCorrect ? 1 : 0,
        };

        saveToLocalStorage();

        if (currentQuestionIndex < soalArtiKata.length - 1) {
            $("#nextBtn").show();
        } else {
            $("#finishBtn").show();
        }
    });

    $("#nextBtn").on("click", function () {
        currentQuestionIndex++;
        saveToLocalStorage();
        showQuestion();
    });

    $("#finishBtn").on("click", function () {
        finishLatihan();
    });

    function finishLatihan() {
        const form = $("#latihanForm");
        $("#jumlah_benar").val(totalCorrect);
        $("#list").val(JSON.stringify(answeredQuestions));
        clearLocalStorage();
        form.submit();
    }

    function playAudio(audioType) {
        let audioSrc;

        if (audioType === "benar") {
            audioSrc = "/audio/benar.mp3";
        } else if (audioType === "salah") {
            audioSrc = "/audio/salah.mp3";
        }

        const audioPlayer = new Audio(audioSrc);
        audioPlayer.play().catch((error) => {
            console.error("Audio playback failed:", error);
        });
    }

    // Mulai latihan
    fetchSoal();
    $("#checkBtn").hide();

    $('input[name="options"]').on("change", function () {
        $("#checkBtn").click();
    });
});
