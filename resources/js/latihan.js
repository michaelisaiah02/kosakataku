$(document).ready(function () {
    let word;
    let totalWords = 0;
    let totalCorrect = 0;
    let attemptCount = 0; // New variable to track incorrect attempts per word
    let consecutiveErrors = 0; // New variable to track consecutive errors per word
    let wordList = [];
    let mediaRecorder;
    let audioChunks = [];
    let recordingTimeout;
    let startTime;

    // Cek apakah ini latihan baru atau lanjutan
    function isNewSession() {
        return parseInt(localStorage.getItem("idLatihan")) !== idLatihan;
    }

    // Jika ini adalah latihan baru, hapus local storage
    if (isNewSession()) {
        clearLocalStorage();
        localStorage.setItem("idLatihan", idLatihan);
    } else {
        loadFromLocalStorage();
    }

    // Memuat data dari local storage

    function saveCurrentWordData() {
        if (word) {
            const index = wordList.findIndex((w) => w.kata === word);
            if (index !== -1) {
                wordList[index].terjemahan = $("#translatedWord").text();
                wordList[index].percobaan = attemptCount;
                wordList[index].durasi = (new Date() - startTime) / 1000;
                updateLocalStorage();
            }
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
        console.log("storage", localStorage);
    }

    function clearLocalStorage() {
        localStorage.removeItem("idLatihan");
        localStorage.removeItem("totalWords");
        localStorage.removeItem("totalCorrect");
        localStorage.removeItem("wordList");
        localStorage.clear();
        console.log("storage", localStorage);
    }

    function displayWord(word) {
        $("#randomWord").text(word);
        textToSpeech(word, googlecode);
        translate(deeplcode, word).then((response) => {
            $("#translatedWord").text(response);
            $("#translatedIcon").show();
            $("#spellingSection").show();
            $("#offMic").show();
            $("#skipSection").show();
            if (consecutiveErrors == delayBantuan) {
                if (bantuanPengejaan == true) {
                    $("#correctSpellingAudio").show();
                }
            }
        });
        exampleSentences(language, word);

        // Reset errors and assistance for new word
        attemptCount = 0;
        consecutiveErrors = 0;

        // Set start time
        startTime = new Date();
    }

    function getWord(language, category) {
        $("#spellingBtn").prop("disabled", false);
        $("#translatedIcon").hide();
        $("#onMic").hide();
        $("#spelledSection").hide();
        $("#trueSection").hide();
        $("#exampleSentenceSection").hide();
        $("#correctSpellingAudio").hide();
        $("#skipSection").addClass("d-flex justify-content-center");
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/word/${language}/${category}`,
                success: function (response) {
                    word = response[0];
                    totalWords++;
                    wordList.push({
                        kata: word,
                        benar: 0,
                        terjemahan: "",
                        percobaan: 0,
                        durasi: 0,
                    });
                    updateLocalStorage();
                    resolve(word);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function translate(deeplcode, word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${
                    window.location.origin
                }/translate/${deeplcode}/${encodeURIComponent(word)}`,
                success: function (response) {
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function textToSpeech(word, googlecode) {
        $.ajax({
            type: "post",
            url: `${
                window.location.origin
            }/text-to-speech/${googlecode}/${encodeURIComponent(word)}`,
            success: function (response) {
                const audioUrl = response.audio_url;
                var audioContainer = $("#correctSpellingAudio");

                audioContainer.empty();

                var mainAudio = document.createElement("audio");
                mainAudio.setAttribute("controls", "controls");
                mainAudio.src = audioUrl;

                audioContainer.append(mainAudio);

                mainAudio.play();
            },
            error: function (xhr, status, error) {
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
                    const formData = new FormData();
                    formData.append("audio", audioBlob, "audio.webm");
                    formData.append("language_code", googlecode);

                    $.ajax({
                        type: "POST",
                        url: `${window.location.origin}/speech-to-text`,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            const speechResult = response.transcription[0];
                            $("#spelledWord").text(speechResult);
                            $("#spelledWord").removeClass(
                                "text-success text-danger"
                            );
                            $("#spelledSection").show();
                            attemptCount++;
                            try {
                                if (compareWords(speechResult, word)) {
                                    if (attemptCount <= maksSalah) {
                                        totalCorrect++;
                                        const index = wordList.findIndex(
                                            (w) => w.kata === word
                                        );
                                        if (index !== -1) {
                                            wordList[index].benar = 1;
                                            wordList[index].terjemahan =
                                                $("#translatedWord").text();
                                            wordList[index].percobaan =
                                                attemptCount;
                                            wordList[index].durasi =
                                                (new Date() - startTime) / 1000;
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
                                    updateLocalStorage();
                                } else {
                                    consecutiveErrors++;
                                    $("#spelledWord").addClass("text-danger");
                                    $("#spellingSection").show();
                                    $("#spellingBtn").prop("disabled", false);
                                    $("#exampleSentenceSection").hide();

                                    if (consecutiveErrors == delayBantuan) {
                                        if (bantuanPengejaan == true) {
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
                            } catch (error) {
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
                            if (xhr.responseText.includes("cURL error 56")) {
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
            startRecognition(googlecode);
            $("#onMic").show();
            $("#offMic").hide();
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
        }
    });

    function exampleSentences(language, word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${
                    window.location.origin
                }/example-sentences/${language}/${encodeURIComponent(word)}`,
                success: function (response) {
                    console.log(response);
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

    function normalizeText(text) {
        return text
            .normalize("NFKC") // Unicode normalization
            .replace(/\s+/g, "") // Remove spaces
            .toLowerCase(); // Convert to lowercase
    }

    function compareWords(word1, word2) {
        return normalizeText(word1) === normalizeText(word2);
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

    // Fetch kata pertama saat halaman dimuat
    getWord(language, category).then(displayWord);

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        saveCurrentWordData();
        getWord(language, category).then(displayWord);
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
                getWord(language, category).then(displayWord);
            }
        });
    });

    window.saveResults = saveResults;
});
