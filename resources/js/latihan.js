$(document).ready(function () {
    let word;
    let totalWords = 0;
    let totalCorrect = 0;
    let wordList = [];
    let mediaRecorder;
    let audioChunks = [];
    let recordingTimeout;
    loadFromLocalStorage();

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

    function displayWord(word) {
        $("#randomWord").html(word);
        textToSpeech(word, googlecode);
        translate(deeplcode, word).then((response) => {
            $("#translatedWord").html(response);
        });
        exampleSentences(language, word).then((response) => {
            console.log(response);
            if (response.length !== 0) {
                loadExampleSentences(response);
            } else {
                $("#exampleSentenceSection").hide();
            }
        });
        $("#onMic").hide();
        $("#offMic").show();
        $("#spelledSection").hide();
        $("#spellingSection").show();
        $("#skipSection").show();
        $("#trueSection").hide();
        $("#exampleSentenceSection").hide();
        $("#spellingBtn").prop("disabled", false);
    }

    function getWord(language, category) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/word/${language}/${category}`,
                success: function (response) {
                    word = response[0];
                    totalWords++;
                    wordList.push({ kata: word, benar: 0 });
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
                        url: "/speech-to-text",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            const speechResult = response.transcription[0];
                            console.log(speechResult);
                            $("#spelledWord").html(speechResult);
                            $("#spelledWord").removeClass(
                                "text-success text-danger"
                            );
                            $("#spelledSection").show();
                            try {
                                if (
                                    speechResult.toLowerCase() ===
                                    word.toLowerCase()
                                ) {
                                    totalCorrect++;
                                    const index = wordList.findIndex(
                                        (w) => w.kata === word
                                    );
                                    if (index !== -1) {
                                        wordList[index].benar = 1;
                                    }
                                    $("#spelledWord").addClass("text-success");
                                    $("#spellingSection").hide();
                                    $("#trueSection").show();
                                    $("#exampleSentenceSection").show();
                                    $("#skipSection").hide();
                                    updateLocalStorage();
                                } else {
                                    $("#spelledWord").addClass("text-danger");
                                    $("#spellingSection").show();
                                    $("#spellingBtn").prop("disabled", false);
                                    $("#exampleSentenceSection").hide();
                                    $("#skipSection").show();
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
                                console.log(error);
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
                const item = `
                <div class="carousel-item ${activeClass} text-center">
                <blockquote class="blockquote mb-0">
                <p>${example.sentence}</p>
                <footer class="blockquote-footer">${example.translation}</footer>
                </blockquote>
                </div>
                `;
                carouselInner.append(item);
            });
        }
    }

    function saveResults() {
        Swal.fire({
            title: "Selesai Latihan?",
            text: "Ingin menyelesaikan latihan ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#DC3545",
            confirmButtonText: "Iya",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $("#latihanForm");
                $("#jumlah_kata").val(totalWords);
                $("#jumlah_benar").val(totalCorrect);
                $("#list").val(JSON.stringify(wordList));
                localStorage.clear();
                form.submit();
            }
        });
    }

    $("#onMic").hide();
    $("#offMic").show();
    $("#spellingSection").show();
    $("#spelledSection").hide();
    $("#trueSection").hide();
    $("#skipSection").show();
    $("#exampleSentenceSection").hide();

    // Fetch kata pertama saat halaman dimuat
    getWord(language, category).then(displayWord);

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        getWord(language, category).then(displayWord);
    });

    $("#skipBtn").on("click", function () {
        Swal.fire({
            title: "Lewati Kata Ini?",
            text: "Yakin melewati kata ini? Kata ini akan dianggap salah.",
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
