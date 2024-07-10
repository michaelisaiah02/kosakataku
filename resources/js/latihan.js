$(document).ready(function () {
    let lastWord;
    let wordsArray = [];
    let currentIndex = 0;

    function displayWord(word) {
        $("#randomWord").html(word);
        textToSpeech(word, googlecode);
        translate(true, word).then((response) => {
            $("#translatedWord").html(response);
        });
        exampleSentences(deeplcode, word).then((response) => {
            if (response !== null) {
                $("#example").html(response.examples);
            }
        });
        lastWord = word;
        $("#spellingSection").show();
        $("#nextSection").hide();
    }

    function nextWord(language, category) {
        if (currentIndex < wordsArray.length) {
            displayWord(wordsArray[currentIndex]);
            currentIndex++;
        } else {
            generateRandomWord(language, category);
        }
    }

    function generateRandomWord(language, category) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/random-word/${language}/${category}`,
                success: function (response) {
                    wordsArray = response;
                    currentIndex = 0;
                    nextWord(language, category);
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    function translate(json = true, word, googlecode = "id") {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/translate/${json}/${googlecode}/${encodeURIComponent(
                        word
                    )}`,
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
            startRecording(googlecode);
            $("#onMic").show();
            $("#offMic").hide();
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
            $("#onMic").hide();
            $("#offMic").show();
        }
    });

    function exampleSentences(deeplcode, word) {
        console.log(deeplcode, word);
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/example-sentences/${deeplcode}/${encodeURIComponent(
                        word
                    )}`,
                success: function (response) {
                    console.log(response);
                    $("#example").html(response);
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    reject(error);
                },
            });
        });
    }

    const language = "korea";
    const deeplcode = "KO";
    const googlecode = "ko-KR";
    const category = "benda";

    $("#onMic").hide();
    $("#offMic").show();
    $("#nextSection").hide();

    // Fetch kata pertama saat halaman dimuat
    generateRandomWord(language, category);

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        nextWord(language, category);
    });
});
