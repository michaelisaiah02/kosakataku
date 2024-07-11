$(document).ready(function () {
    let word;

    function displayWord(word) {
        console.log(word);
        $("#randomWord").html(word);
        textToSpeech(word, googlecode);
        translate(deeplcode, word).then((response) => {
            $("#translatedWord").html(response);
        });
        exampleSentences(language, word).then((response) => {
            if (response !== null) {
                $("#example").html(response.examples);
            }
        });
        $("#spellingSection").show();
        $("#nextSection").hide();
    }

    function getWord(language, category) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: window.location.origin + `/word/${language}/${category}`,
                success: function (response) {
                    word = response[0];
                    console.log(word);
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
                url:
                    window.location.origin +
                    `/translate/${deeplcode}/${encodeURIComponent(word)}`,
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
        console.log(googlecode, word);
        $.ajax({
            type: "post",
            url:
                window.location.origin +
                `/text-to-speech/${googlecode}/${encodeURIComponent(word)}`,
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

    let mediaRecorder;
    let audioChunks = [];

    function startRecording(googlecode) {
        console.log(googlecode);
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
                            $("#spelledWord").html("");
                            $("#spelledWord").removeClass(
                                "text-success text-danger"
                            );
                            $("#spelledWordLabel").show();
                            $("#spelledWord").html(speechResult);
                            if (
                                speechResult.toLowerCase() ===
                                word.toLowerCase()
                            ) {
                                $("#spelledWord").addClass("text-success");
                                if (example) {
                                    $("#exampleSentence").html(example);
                                }
                                $("#spellingSection").hide();
                                $("#nextSection").show();
                            } else {
                                $("#spelledWord").addClass("text-danger");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        },
                    });

                    audioChunks = [];
                };
            })
            .catch((error) =>
                console.error("Error accessing media devices.", error)
            );
    }

    function stopRecording() {
        mediaRecorder.stop();
    }

    $("#spellingBtn").on("click", function () {
        if (!mediaRecorder || mediaRecorder.state === "inactive") {
            startRecording(googlecode);
            $("#onMic").show();
            $("#offMic").hide();
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
            $("#onMic").hide();
            $("#offMic").show();
        }
    });

    function exampleSentences(language, word) {
        console.log(language, word);
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/example-sentences/${language}/${encodeURIComponent(
                        word
                    )}`,
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

        sentences.forEach((example, index) => {
            const activeClass = index === 0 ? "active" : "";
            const item = `
                <div class="carousel-item ${activeClass}">
                    <blockquote class="blockquote mb-0">
                        <p>${example.sentence}</p>
                        <footer class="blockquote-footer">${example.translation}</footer>
                    </blockquote>
                </div>
            `;
            carouselInner.append(item);
        });

        $("#exampleCarousel").carousel();
    }

    const language = "Japanese";
    const category = "vehicles";
    const deeplcode = "JA";
    const googlecode = "ja-JP";

    $("#onMic").hide();
    $("#offMic").show();
    $("#spelledWordLabel").hide();
    $("#nextSection").hide();

    // Fetch kata pertama saat halaman dimuat
    getWord(language, category).then((response) => {
        if (response !== null) {
            displayWord(word);
        }
    });

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        getWord(language, category).then((response) => {
            if (response !== null) {
                displayWord(word);
            }
        });
    });
});
