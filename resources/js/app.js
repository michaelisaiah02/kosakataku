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

    function translate(language, word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/translate/${language}/${encodeURIComponent(word)}`,
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
                $("#spelledWord").attr("src", audioUrl);
                $("#spelledWord")[0].play();
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
                            console.log(
                                "Transcriptions:",
                                response.transcriptions
                            );
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

    // function speechToText(language) {
    //     try {
    //         recognition = new SpeechRecognition();
    //         recognition.lang = language || "en-US";
    //         spellingBtn.addClass("recording");
    //         spellingBtn.addClass("justify-content-evenly");
    //         spellingBtn.removeClass("justify-content-center");
    //         spellingBtn.find("p").html("Mendengar...");
    //         $("#recIcon").show();
    //         recognition.start();
    //         recognition.onresult = (event) => {
    //             const speechResult = event.results[0][0].transcript;
    //             $("#resultWord").html("");
    //             $("#resultWord").removeClass("text-success text-danger");
    //             resultWord.html(speechResult);
    //             if (speechResult.toLowerCase() === lastWord.toLowerCase()) {
    //                 $("#resultWord").addClass("text-success");
    //                 stopRecording();
    //             } else {
    //                 $("#resultWord").addClass("text-danger");
    //             }
    //             $("#example").html(example);
    //         };
    //         recognition.onspeechend = () => {
    //             speechToText();
    //         };
    //         recognition.onerror = (event) => {
    //             stopRecording();
    //             if (event.error === "no-speech") {
    //                 console.log(
    //                     "Tidak ada kata terdengar, berhenti mendengar..."
    //                 );
    //                 $("#resultWord").html("");
    //                 $("#recIcon").hide();
    //             } else if (event.error === "audio-capture") {
    //                 alert("Microphone tidak terdeteksi.");
    //                 $("#resultWord").html("");
    //                 $("#recIcon").hide();
    //             } else if (event.error === "not-allowed") {
    //                 alert("Akses microphone tidak diberikan.");
    //                 $("#resultWord").html("");
    //                 $("#recIcon").hide();
    //             } else if (event.error === "aborted") {
    //                 console.log("Berhenti mendengar..");
    //                 $("#resultWord").html("");
    //                 $("#recIcon").hide();
    //             } else {
    //                 alert("Error: " + event.error);
    //                 $("#resultWord").html("");
    //                 $("#recIcon").hide();
    //             }
    //         };
    //     } catch (error) {
    //         recording = false;

    //         console.log(error);

    //         $("#recIcon").hide();
    //     }
    // }

    // $("#spellingBtn").on("click", function () {
    //     if (!recording) {
    //         speechToText();
    //         recording = true;
    //     } else {
    //         stopRecording();
    //     }
    // });

    // function stopRecording() {
    //     recognition.stop();
    //     $("#recIcon").hide();
    //     spellingBtn.find("p").html("Mulai dengarkan");
    //     spellingBtn.addClass("justify-content-center");
    //     spellingBtn.removeClass("justify-content-evenly");
    //     spellingBtn.removeClass("recording");
    //     recording = false;
    // }

    function exampleSentences(word) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url:
                    window.location.origin +
                    `/example-sentences/${encodeURIComponent(word)}`,
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

    let SpeechRecognition =
            window.SpeechRecognition || window.webkitSpeechRecognition,
        recognition,
        recording = false;
    const spellingBtn = $("#spellingBtn");
    const resultWord = $("#resultWord");
    var lastWord;

    generateRandomWord()
        .then((result) => {
            lastWord = result.word;
            $("#randomWord").html(result.word);
            textToSpeech(result.word);
            // translate("id", result.word).then((response) => {
            //     $("#translatedWord").html(response);
            // });
            exampleSentences("car").then((response) => {
                var example =
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