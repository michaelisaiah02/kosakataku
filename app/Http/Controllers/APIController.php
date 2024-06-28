<?php

namespace App\Http\Controllers;

use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\StreamingRecognitionConfig;

class APIController extends Controller
{
    public function generateRandomWord()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wordsapiv1.p.rapidapi.com/words/?random=true",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: wordsapiv1.p.rapidapi.com",
                "x-rapidapi-key: d2ecf1000cmshe67379045cd9679p1b9e0djsn5ed8bb668b83"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['error' => $err]);
        } else {
            return response()->json($response);
        }
    }

    public function translate($language = 'id', $word)
    {
        $authKey = env("AUTH_KEY_DEEPL", null);
        $translator = new \DeepL\Translator($authKey);

        $result = $translator->translateText($word, null, $language);
        return response()->json($result->text);
    }

    public function speechToText($language = 'en', $audio)
    {
        $speechClient = new \Google\Cloud\Speech\V1\SpeechClient();

        $recognitionConfig = new RecognitionConfig();
        $recognitionConfig->setEncoding(AudioEncoding::FLAC);
        $recognitionConfig->setSampleRateHertz(44100);
        $recognitionConfig->setLanguageCode('en-US');
        $config = new StreamingRecognitionConfig();
        $config->setConfig($recognitionConfig);

        $audioResource = fopen('path/to/audio.flac', 'r');

        $responses = $speechClient->recognizeAudioStream($config, $audioResource);
        dd($responses);
        foreach ($responses as $element) {
            // doSomethingWith($element);
        }

        $speechClient->close();
    }

    public function exampleSentences($word)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wordsapiv1.p.rapidapi.com/words/$word/examples",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: wordsapiv1.p.rapidapi.com",
                "x-rapidapi-key: d2ecf1000cmshe67379045cd9679p1b9e0djsn5ed8bb668b83"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
