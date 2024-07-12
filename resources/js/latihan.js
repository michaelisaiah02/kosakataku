$(document).ready(function () {
    let word;
    let recognition;

    function displayWord(word) {
        $("#randomWord").html(word);
        textToSpeech(word, googlecode);
        translate(deeplcode, word).then((response) => {
            $("#translatedWord").html(response);
        });
        exampleSentences(language, word).then((response) => {
            if (response !== null) {
                loadExampleSentences(response);
            }
        });
        $("#spellingSection").show();
        $("#nextSection").hide();
        $("#trueSection").hide();
    }

    function getWord(language, category) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "post",
                url: `${window.location.origin}/word/${language}/${category}`,
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
        console.log(googlecode, word);
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
        recognition = new (window.SpeechRecognition ||
            window.webkitSpeechRecognition)();
        recognition.lang = googlecode;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;

        recognition.onresult = function (event) {
            const speechResult = event.results[0][0].transcript;
            $("#spelledWord").html(speechResult);
            $("#spelledWord").removeClass("text-success text-danger");
            $("#spelledSection").show();
            if (speechResult.toLowerCase() === word.toLowerCase()) {
                $("#spelledWord").addClass("text-success");
                $("#exampleSentence").show();
                $("#spellingSection").hide();
                $("#trueSection").show();
                $("#exampleSentenceSection").show();
                recognition.stop();
            } else {
                $("#spelledWord").addClass("text-danger");
                recognition.stop();
                startRecognition(); // Restart recognition
            }
        };

        recognition.onerror = function (event) {
            console.error("Speech recognition error detected: " + event.error);
            $("#onMic").hide();
            $("#offMic").show();
        };

        recognition.onend = function () {
            console.log("Speech recognition service disconnected");
        };

        recognition.start();
    }

    $("#spellingBtn").on("click", function () {
        startRecognition();
        $("#onMic").show();
        $("#offMic").hide();
    });

    function exampleSentences(language, word) {
        console.log(language, word);
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

    const language = "German";
    const category = "animals";
    const deeplcode = "DE";
    const googlecode = "de-DE";

    $("#onMic").hide();
    $("#offMic").show();
    $("#spelledSection").hide();
    $("#trueSection").hide();
    $("#exampleSentenceSection").hide();

    // Fetch kata pertama saat halaman dimuat
    getWord(language, category).then(displayWord);

    // Tambahkan event listener untuk tombol 'Lanjut'
    $("#nextBtn").on("click", function () {
        getWord(language, category).then(displayWord);
        $("#onMic").hide();
        $("#offMic").show();
        $("#spelledSection").hide();
        $("#trueSection").hide();
        $("#exampleSentenceSection").hide();
    });
});
