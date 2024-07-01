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

    function generateRandomWord() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: window.location.origin + "/random-word",
                success: function (response) {
                    var result = JSON.parse(response);
                    resolve(result);
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
                var audio = $("#spelledWord");
                var mainAudio = document.createElement("audio");
                mainAudio.setAttribute("controls", "controls");
                audio.append(mainAudio);
                mainAudio.src = audioUrl;
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
                            $("#resultWord").html("");
                            $("#resultWord").removeClass(
                                "text-success text-danger"
                            );
                            $("#resultWord").html(speechResult);
                            if (
                                speechResult.toLowerCase() ===
                                lastWord.toLowerCase()
                            ) {
                                $("#resultWord").addClass("text-success");
                                if (example) {
                                    $("#exampleSentence").html(
                                        example.replace(
                                            lastWord,
                                            `<strong>${lastWord}</strong>`
                                        )
                                    );
                                }
                            } else {
                                $("#resultWord").addClass("text-danger");
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
            $(this).text("Stop Recording");
        } else if (mediaRecorder.state === "recording") {
            stopRecording();
            $(this).text("Start Recording");
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

    var lastWord;
    var example;

    generateRandomWord()
        .then((result) => {
            lastWord = result.word;
            $("#randomWord").html(result.word);
            textToSpeech(result.word);
            translate(result.word).then((response) => {
                $("#translatedWord").html(response);
            });
            exampleSentences(result.word).then((response) => {
                example =
                    response.examples[
                        Math.round(Math.random() * response.examples.length)
                    ] || null;
            });
        })
        .catch((error) => {
            console.error(error);
        });
});

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
