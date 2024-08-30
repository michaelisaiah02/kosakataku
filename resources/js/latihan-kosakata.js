$(document).ready(function () {
    const idLatihan = $("#data").data("id-latihan");
    const idBahasa = $("#data").data("id-bahasa");
    const bahasa = $("#data").data("bahasa");
    const kategori = $("#data").data("kategori");
    const bantuanSuara = $("#data").data("bantuan-suara");
    let list;
    let totalWords = 5; // Jumlah kata yang akan dilatih
    let currentWordCount = 0; // Menambahkan variabel untuk menghitung jumlah kata yang sudah dilatih
    let totalCorrect = 0; // Jumlah kata yang benar
    let attemptCount = 0; // Jumlah percobaan
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
        if (currentWordCount >= totalWords) {
            return;
        } else if (getIndex() !== -1) {
            wordList[getIndex()].durasi = (new Date() - startTime) / 1000;
            updateLocalStorage();
        }
    }

    window.onbeforeunload = function () {
        saveCurrentWordData();
    };

    function updateLocalStorage() {
        localStorage.setItem("currentWordCount", currentWordCount);
        localStorage.setItem("totalCorrect", totalCorrect);
        localStorage.setItem("wordList", JSON.stringify(wordList));
    }

    function loadFromLocalStorage() {
        currentWordCount =
            parseInt(localStorage.getItem("currentWordCount")) || 0;
        totalCorrect = parseInt(localStorage.getItem("totalCorrect")) || 0;
        wordList = JSON.parse(localStorage.getItem("wordList")) || [];
    }

    function clearLocalStorage() {
        localStorage.removeItem("currentWordCount");
        localStorage.removeItem("totalCorrect");
        localStorage.removeItem("wordList");
        localStorage.clear();
    }

    function displayWord() {
        $("#kata").text(list.word);
        $("#ejaan").text(list.pronunciation);
        $("#translatedWord").text(list.translation);
        $("#translatedIcon").show();
        $("#offMic").show();
        $("#skipSection").show();

        // Reset percobaan
        attemptCount = 0;

        // Mengatur waktu mulai
        startTime = new Date();
    }

    function getWord() {
        currentWordCount++;
        $("#spellingBtn").prop("disabled", false);
        $("#translatedIcon").hide();
        $("#spellingSection").hide();
        $("#onMic").hide();
        $("#spelledSection").hide();
        $("#trueSection").hide();
        $("#exampleSentenceSection").hide();
        $("#correctSpellingAudio").hide();
        $("#skipSection").addClass("d-flex justify-content-center").show();
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/word/${bahasa}/${kategori}`,
                success: function (response) {
                    list = response[0];
                    wordList.push({
                        kata: list.word,
                        cara_baca: list.pronunciation,
                        terjemahan: list.translation,
                        percobaan: 0,
                        benar: 0,
                        durasi: 0,
                    });
                    speechContextWords.push(list.word);
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

    function textToSpeech(attempt = 1) {
        const maxAttempts = 5;
        const formTTS = new FormData();
        formTTS.append("kata", list.word);
        formTTS.append("idBahasa", idBahasa);
        formTTS.append("bantuanSuara", bantuanSuara);

        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/text-to-speech`,
                data: formTTS,
                processData: false,
                contentType: false,
                success: function (response) {
                    const audioUrl = response.audio_url;
                    fetch(audioUrl)
                        .then((res) => {
                            if (!res.ok) {
                                throw new Error(
                                    `HTTP error! status: ${res.status}`
                                );
                            }
                            return res.blob();
                        })
                        .then((blob) => {
                            const mainAudio = new Audio(
                                URL.createObjectURL(blob)
                            );
                            const correctSpellingAudio = $(
                                "#correctSpellingAudio"
                            );
                            mainAudio.controls = true;
                            correctSpellingAudio.html(mainAudio);
                            mainAudio.play().catch((error) => {
                                console.error("Audio playback failed:", error);
                            });
                            resolve();
                        })
                        .catch((error) => {
                            console.error("Audio load failed:", error);
                            if (attempt < maxAttempts) {
                                setTimeout(
                                    () =>
                                        textToSpeech(attempt + 1)
                                            .then(resolve)
                                            .catch(reject),
                                    1000
                                );
                            } else {
                                reject();
                            }
                        });
                },
                error: function (xhr) {
                    console.error(
                        `Attempt ${attempt} failed:`,
                        xhr.responseText
                    );
                    if (attempt < maxAttempts) {
                        setTimeout(
                            () =>
                                textToSpeech(attempt + 1)
                                    .then(resolve)
                                    .catch(reject),
                            1000
                        );
                    } else {
                        reject();
                    }
                },
            });
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
                            if (
                                matchedResult == undefined ||
                                matchedResult == ""
                            ) {
                                Swal.fire({
                                    title: "Kesalahan!",
                                    text: "Ucapanmu tidak terdengar dengan jelas, coba ucapkan kata secara perlahan! Ini tidak akan dihitung sebagai kesalahan.",
                                    icon: "warning",
                                });
                            } else {
                                $("#spelledWord").text(
                                    matchedResult.replace(/[\p{P}\p{S}]+$/u, "")
                                );
                                $("#spelledWord").removeClass(
                                    "text-success text-danger"
                                );
                                attemptCount++;
                            }
                            try {
                                if (isCorrect) {
                                    $("#spelledSection").show();
                                    playAudio("correct");

                                    totalCorrect++;
                                    if (getIndex() !== -1) {
                                        wordList[getIndex()].benar = 1;
                                        wordList[getIndex()].percobaan =
                                            attemptCount;
                                    }
                                    $("#spelledWord").addClass("text-success");
                                    $("#spellingSection").hide();
                                    $("#trueSection").show();
                                    if (currentWordCount == totalWords) {
                                        $("#nextBtn").hide();
                                        $("#nextBtn").removeClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#nextBtn .btn").removeClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#finishBtn").show();
                                        $("#finishBtn").addClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#finishBtn .btn").addClass(
                                            "d-flex justify-content-center"
                                        );
                                    } else {
                                        $("#nextBtn").show();
                                        $("#nextBtn").addClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#nextBtn .btn").addClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#finishBtn").hide();
                                        $("#finishBtn").removeClass(
                                            "d-flex justify-content-center"
                                        );
                                        $("#finishBtn .btn").removeClass(
                                            "d-flex justify-content-center"
                                        );
                                    }
                                    $("#exampleSentenceSection").show();
                                    $("#skipSection").removeClass(
                                        "d-flex justify-content-center"
                                    );
                                    $("#skipSection").hide();
                                } else {
                                    playAudio("wrong");
                                    $("#spellingBtn").prop("disabled", false);
                                    $("#spelledSection").show();
                                    if (
                                        matchedResult == undefined ||
                                        matchedResult == ""
                                    ) {
                                        $("#spelledSection").hide();
                                    }
                                    $("#spelledWord").addClass("text-danger");
                                    $("#spellingSection").show();
                                    $("#exampleSentenceSection").hide();
                                    $("#correctSpellingAudio").show();

                                    if (getIndex() !== -1) {
                                        wordList[getIndex()].percobaan =
                                            attemptCount;
                                    }
                                }
                            } catch (error) {
                                console.error(error);
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
            .replace(/[^\p{L}\p{N}\s]/gu, "") // Remove non-letter, non-number, non-space characters
            .replace(/\s+/g, "") // Remove all spaces
            .toLowerCase(); // Convert to lowercase
    }

    function compareWords(hasilPengucapan) {
        const normalizedExpected = normalizeText(list.word);
        const normalizedActual = normalizeText(hasilPengucapan);
        return normalizedExpected === normalizedActual;
    }

    function playAudio(audioType) {
        let audioSrc;

        if (audioType === "correct") {
            audioSrc = "/audio/correct.mp3";
        } else if (audioType === "wrong") {
            audioSrc = "/audio/wrong.mp3";
        }

        const audioPlayer = new Audio(audioSrc);
        audioPlayer.play().catch((error) => {
            console.error("Audio playback failed:", error);
        });
    }

    function exampleSentences() {
        const formExampleSentences = new FormData();
        formExampleSentences.append("kata", list.word);
        formExampleSentences.append("terjemahan", list.translation);
        formExampleSentences.append("bahasa", bahasa);
        formExampleSentences.append("kategori", kategori);
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/example-sentences`,
                data: formExampleSentences,
                processData: false,
                contentType: false,
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

    function saveResults() {
        saveCurrentWordData();
        const form = $("#latihanForm");
        $("#jumlah_benar").val(totalCorrect);
        $("#list").val(JSON.stringify(wordList));
        clearLocalStorage();
        form.submit();
    }

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        saveCurrentWordData();
        if (currentWordCount < totalWords) {
            load();
        } else {
            saveResults();
        }
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
                load();
            }
        });
    });

    function load() {
        if (currentWordCount >= totalWords) {
            saveResults();
            return;
        } else {
            $("#loading")
                .addClass("d-flex justify-content-center full-screen")
                .show();
            getWord()
                .then(displayWord)
                .then(() => $("#loading").removeClass("full-screen"))
                .then(exampleSentences)
                .then(() => textToSpeech())
                .then(() => $("#spellingSection").show())
                .then(() =>
                    $("#loading")
                        .removeClass("d-flex justify-content-center")
                        .hide()
                )
                .then(() => {
                    $("#correctSpellingAudio").show();
                });
        }
    }

    // Fetch kata pertama saat halaman dimuat
    load();

    window.saveResults = saveResults;
});
