$(document).ready(function () {
    const idLatihan = $("#data").data("id-latihan");
    const idBahasa = $("#data").data("id-bahasa");
    const bahasa = $("#data").data("bahasa");
    const kategori = $("#data").data("kategori");
    const bantuanSuara = $("#data").data("bantuan-suara");
    const bantuanPengucapan = $("#data").data("bantuan-pengucapan");
    const delayBantuan = $("#data").data("delay-bantuan");
    const maksSalah = $("#data").data("maks-salah");
    let list;
    let totalWords = 0; // Jumlah kata yang sudah dilatih
    let totalCorrect = 0; // Jumlah kata yang benar
    let attemptCount = 0; // Jumlah percobaan
    let consecutiveErrors = 0; // Jumlah kesalahan berturut-turut
    let wordList = []; // Daftar kata yang sudah dilatih
    let speechContextWords = []; // Konteks ucapan kata saat pengenalan suara
    let mediaRecorder;
    let audioChunks = [];
    let recordingTimeout;
    let startTime;

    // Cek apakah ini latihan baru atau lanjutan
    function isNewSession(idLatihan) {
        return parseInt(localStorage.getItem("idLatihan")) !== idLatihan;
    }

    function getIndex() {
        const index = wordList.findIndex((w) => w.kata === list.word);
        return index;
    }

    // Jika ini adalah latihan baru, hapus local storage
    if (isNewSession(idLatihan)) {
        clearLocalStorage();
        localStorage.setItem("idLatihan", idLatihan);
    } else {
        loadFromLocalStorage();
    }

    // Memuat data dari local storage
    function saveCurrentWordData() {
        if (getIndex() !== -1) {
            wordList[getIndex()].durasi = (new Date() - startTime) / 1000;
            updateLocalStorage();
        }
    }

    window.onbeforeunload = function () {
        saveCurrentWordData();
    };

    function updateLocalStorage() {
        localStorage.setItem("totalWords", totalWords);
        localStorage.setItem("totalCorrect", totalCorrect);
        localStorage.setItem("wordList", JSON.stringify(wordList));
    }

    function loadFromLocalStorage() {
        totalWords = parseInt(localStorage.getItem("totalWords")) || 0;
        totalCorrect = parseInt(localStorage.getItem("totalCorrect")) || 0;
        wordList = JSON.parse(localStorage.getItem("wordList")) || [];
    }

    function clearLocalStorage() {
        localStorage.removeItem("idLatihan");
        localStorage.removeItem("totalWords");
        localStorage.removeItem("totalCorrect");
        localStorage.removeItem("wordList");
        localStorage.clear();
    }

    function displayWord() {
        $("#kata").text(list.word);
        $("#ejaan").text(list.pronunciation);
        $("#translatedWord").text(list.translation);
        $("#translatedIcon").show();
        $("#spellingSection").show();
        $("#offMic").show();
        $("#skipSection").show();
        $("#loading").removeClass("d-flex justify-content-center");
        $("#loading").hide();
        if (consecutiveErrors == delayBantuan) {
            if (bantuanPengucapan == true) {
                $("#correctSpellingAudio").show();
            }
        }
        exampleSentences(list.word);

        // Reset percobaan dan kesalahan
        attemptCount = 0;
        consecutiveErrors = 0;

        // Mengatur waktu mulai
        startTime = new Date();
    }

    function getWord() {
        $("#loading").show();
        $("#loading").addClass("d-flex justify-content-center");
        $("#spellingBtn").prop("disabled", false);
        $("#translatedIcon").hide();
        $("#spellingSection").hide();
        $("#onMic").hide();
        $("#spelledSection").hide();
        $("#trueSection").hide();
        $("#exampleSentenceSection").hide();
        $("#correctSpellingAudio").hide();
        $("#skipSection").addClass("d-flex justify-content-center");
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/word/${bahasa}/${kategori}`,
                success: function (response) {
                    list = response[0];
                    totalWords++;
                    wordList.push({
                        kata: list.word,
                        cara_baca: list.pronunciation,
                        terjemahan: list.translation,
                        percobaan: 0,
                        benar: 0,
                        durasi: 0,
                    });
                    speechContextWords.push(list.word);
                    textToSpeech();
                    updateLocalStorage();
                    resolve(list);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function textToSpeech() {
        $.ajax({
            type: "get",
            url: `${
                window.location.origin
            }/text-to-speech/${idBahasa}/${encodeURIComponent(
                list.word
            )}/${bantuanSuara}`,
            processData: false,
            contentType: false,
            success: function (response) {
                const audioUrl = response.audio_url;
                var audioContainer = $("#correctSpellingAudio");

                audioContainer.empty();

                var mainAudio = document.createElement("audio");
                mainAudio.setAttribute("controls", "controls");
                mainAudio.src = audioUrl;

                audioContainer.append(mainAudio);

                mainAudio.play().catch((error) => {
                    console.error("Audio playback failed:", error);
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            },
        });
    }

    function startRecognition() {
        navigator.mediaDevices
            .getUserMedia({ audio: true })
            .then((stream) => {
                mediaRecorder = new MediaRecorder(stream, {
                    mimeType: "audio/webm",
                });
                mediaRecorder.start();

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, {
                        type: "audio/webm",
                    });
                    const formSTT = new FormData();
                    formSTT.append("audio", audioBlob, "audio.webm");
                    formSTT.append("languageId", idBahasa);
                    formSTT.append(
                        "speechContext",
                        JSON.stringify(speechContextWords)
                    );

                    $.ajax({
                        type: "POST",
                        url: `${window.location.origin}/speech-to-text`,
                        data: formSTT,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            const speechResults = response.transcription; // Array of 3 alternatives
                            let matchedResult = speechResults[0];

                            // Check for any matching transcription
                            let isCorrect = false;
                            for (let i = 0; i < speechResults.length; i++) {
                                if (compareWords(speechResults[i])) {
                                    matchedResult = speechResults[i];
                                    isCorrect = true;
                                    break;
                                }
                            }

                            $("#spelledWord").text(matchedResult); // Show matched result
                            $("#spelledWord").removeClass(
                                "text-success text-danger"
                            );
                            $("#spelledSection").show();
                            attemptCount++;
                            try {
                                if (isCorrect) {
                                    // Play correct audio
                                    document
                                        .getElementById("correct-audio")
                                        .play();

                                    if (attemptCount <= maksSalah) {
                                        totalCorrect++;
                                        if (getIndex() !== -1) {
                                            wordList[getIndex()].benar = 1;
                                        }
                                    }
                                    $("#spelledWord").addClass("text-success");
                                    $("#spellingSection").hide();
                                    $("#trueSection").show();
                                    $("#exampleSentenceSection").show();
                                    $("#skipSection").removeClass(
                                        "d-flex justify-content-center"
                                    );
                                    $("#skipSection").hide();
                                } else {
                                    // Play wrong audio
                                    document
                                        .getElementById("wrong-audio")
                                        .play();

                                    consecutiveErrors++;
                                    $("#spelledWord").addClass("text-danger");
                                    $("#spellingSection").show();
                                    $("#spellingBtn").prop("disabled", false);
                                    $("#exampleSentenceSection").hide();

                                    if (consecutiveErrors == delayBantuan) {
                                        if (bantuanPengucapan == true) {
                                            $("#correctSpellingAudio").show();
                                        }
                                    }

                                    if (attemptCount == maksSalah) {
                                        Swal.fire({
                                            title: "Batas kesalahan tercapai!",
                                            html: `Kamu sudah mencapai batas maksimum kesalahan.<br>Kalau salah lagi, kata ini akan dianggap salah.`,
                                            icon: "warning",
                                            confirmButtonText: "OK",
                                        });
                                    }
                                    if (attemptCount == maksSalah + 1) {
                                        Swal.fire({
                                            title: "Kata masih salah!",
                                            html: `Kamu masih salah.<br>Kamu boleh terus mencoba, tapi kata ini akan dianggap salah walaupun sudah benar. Semangat!`,
                                            icon: "info",
                                            confirmButtonText: "OK",
                                        });
                                    }
                                }
                                if (getIndex() !== -1) {
                                    wordList[getIndex()].percobaan =
                                        attemptCount;
                                }
                                saveCurrentWordData();
                            } catch (error) {
                                console.error(error);
                                if (error instanceof TypeError) {
                                    Swal.fire({
                                        title: "Kesalahan!",
                                        text: "Ucapanmu tidak terdengar dengan jelas, coba ucapkan kata secara perlahan!",
                                        icon: "warning",
                                    });
                                }
                                $("#spelledSection").hide();
                                $("#spellingBtn").prop("disabled", false);
                            }
                        },
                        error: function (xhr, status, error) {
                            if (xhr.responseText.includes("error")) {
                                Swal.fire({
                                    title: "Kesalahan!",
                                    text: "Koneksi internet terputus!",
                                    icon: "warning",
                                });
                            }
                            console.error(xhr.responseText, status, error);
                            $("#spellingBtn").prop("disabled", false);
                        },
                    });

                    audioChunks = [];
                };
                recordingTimeout = setTimeout(() => {
                    stopRecording();
                    $("#spellingBtn").prop("disabled", true);
                }, 2000);
            })
            .catch(
                (error) =>
                    console.error("Error accessing media devices.", error),
                $("#spellingBtn").prop("disabled", false)
            );
    }

    function stopRecording() {
        clearTimeout(recordingTimeout);
        mediaRecorder.stop();
        $("#onMic").hide();
        $("#offMic").show();
    }

    $("#spellingBtn").on("click", function () {
        if (!mediaRecorder || mediaRecorder.state === "inactive") {
            startRecognition();
            $("#onMic").show();
            $("#offMic").hide();
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
        }
    });

    function normalizeText(text) {
        return text
            .normalize("NFKC") // Unicode normalization
            .replace(/\s+/g, "") // Remove spaces
            .toLowerCase(); // Convert to lowercase
    }

    function compareWords(hasilPengucapan) {
        return normalizeText(list.word) === normalizeText(hasilPengucapan);
    }

    function exampleSentences() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${
                    window.location.origin
                }/example-sentences/${bahasa}/${encodeURIComponent(list.word)}`,
                success: function (response) {
                    loadExampleSentences(response);
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function loadExampleSentences(sentences) {
        const carouselInner = $("#exampleSentencesCarousel");
        carouselInner.empty();
        if (sentences !== null) {
            sentences.forEach((example, index) => {
                const activeClass = index === 0 ? "active" : "";
                const item = `<div class="carousel-item ${activeClass} text-center">
                        <blockquote class="blockquote mb-0">
                            <p>${example.sentence}</p>
                            <footer class="blockquote-footer">
                                ${example.translation}
                            </footer>
                        </blockquote>
                    </div>`;
                carouselInner.append(item);
            });
        }
    }

    function saveResults(finish = false) {
        Swal.fire({
            title: "Selesai Latihan?",
            html:
                finish === false
                    ? `Ingin menyelesaikan latihan ini?<br>Kata ini akan dianggap salah.`
                    : "Ingin menyelesaikan latihan ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#DC3545",
            confirmButtonText: "Iya",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                saveCurrentWordData();
                const form = $("#latihanForm");
                $("#jumlah_kata").val(totalWords);
                $("#jumlah_benar").val(totalCorrect);
                $("#list").val(JSON.stringify(wordList));
                form.submit();
            }
        });
    }

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        getWord().then(displayWord);
    });

    $("#skipBtn").on("click", function () {
        saveCurrentWordData();
        Swal.fire({
            title: "Lewati Kata Ini?",
            html: `Yakin melewati kata ini?<br>Kata ini akan dianggap salah.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#DC3545",
            confirmButtonText: "Ya, lewati!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                getWord().then(displayWord);
            }
        });
    });

    // Fetch kata pertama saat halaman dimuat
    getWord().then(displayWord);

    window.saveResults = saveResults;
});
