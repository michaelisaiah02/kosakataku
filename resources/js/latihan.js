$(document).ready(function () {
    let lastWord;
    let wordsArray = [];
    let currentIndex = 0;

    function displayWord(word) {
        $("#randomWord").html(word);
        textToSpeech(word);
        translate(word).then((response) => {
            $("#translatedWord").html(response);
        });
        exampleSentences(word).then((response) => {
            const example =
                response.examples[
                    Math.floor(Math.random() * response.examples.length)
                ] || null;
            $("#example").html(example);
        });
        lastWord = word;
        $("#spellingSection").show();
        $("#nextSection").hide();
    }

    function nextWord() {
        if (currentIndex < wordsArray.length) {
            displayWord(wordsArray[currentIndex]);
            currentIndex++;
        } else {
            generateRandomWord();
        }
    }

    function generateRandomWord(language = "inggris", category) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/random-word/${language}/${category}`,
                success: function (response) {
                    wordsArray = response;
                    console.log(wordsArray);
                    console.log(response);
                    currentIndex = 0;
                    nextWord();
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function translate(word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/translate/${encodeURIComponent(word)}`,
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

    function textToSpeech(word) {
        $.ajax({
            type: "post",
            url:
                window.location.origin +
                `/text-to-speech/${encodeURIComponent(word)}`,
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

    function startRecording() {
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
                    formData.append("language", "en-US");

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
                            $("#spelledWord").html(speechResult);
                            if (
                                speechResult.toLowerCase() ===
                                lastWord.toLowerCase()
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
            startRecording();
            $("#onMic").show();
            $("#offMic").hide();
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
            $("#onMic").hide();
            $("#offMic").show();
        }
    });

    function exampleSentences(word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/example-sentences/${encodeURIComponent(word)}`,
                success: function (response) {
                    var result = JSON.parse(response);
                    console.log(result);
                    resolve(result);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    $("#onMic").hide();
    $("#offMic").show();
    $("#nextSection").hide();

    // Fetch kata pertama saat halaman dimuat
    generateRandomWord("inggris", "hewan");

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        nextWord();
    });
});
