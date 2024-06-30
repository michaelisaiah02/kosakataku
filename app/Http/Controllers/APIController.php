<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\SpeechClient;

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

    public function textToSpeech($word)
    {
        $client = new TextToSpeechClient([
            'credentials' => config('services.google.application_credentials'),
        ]);

        $inputWord = (new \Google\Cloud\TextToSpeech\V1\SynthesisInput())
            ->setText($word);

        $voice = (new \Google\Cloud\TextToSpeech\V1\VoiceSelectionParams())
            ->setLanguageCode('en-US')
            ->setSsmlGender(\Google\Cloud\TextToSpeech\V1\SsmlVoiceGender::NEUTRAL);

        $audioConfig = (new \Google\Cloud\TextToSpeech\V1\AudioConfig())
            ->setAudioEncoding(\Google\Cloud\TextToSpeech\V1\AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($inputWord, $voice, $audioConfig);

        $audioContent = $response->getAudioContent();

        $randomIdFile = uniqid();
        $newFilePath = 'public/' . $randomIdFile . '.mp3';
        if (isset($_COOKIE['previous_file_path'])) {
            $previousFilePath = $_COOKIE['previous_file_path'];
            if (Storage::exists($previousFilePath)) {
                Storage::delete($previousFilePath);
            }
            // Hapus cookie lama
            setcookie('previous_file_path', '', time() - 3600);
        }

        Storage::put($newFilePath, $audioContent);

        // Simpan file path baru ke dalam cookie
        setcookie('previous_file_path', $newFilePath, time() + 10800, "/"); // Cookie berlaku selama 30 hari

        $url = Storage::url($newFilePath);

        return response()->json(['audio_url' => $url]);
    }

    public function speechToText(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimetypes:audio/webm',
            'language' => 'required|string',
        ]);

        $audioFile = $request->file('audio');

        // Memeriksa MIME type secara manual
        // if ($audioFile->getMimeType() !== 'audio/webm') {
        //     return response()->json(['error' => 'Invalid audio file type. Only WEBM files are accepted. MimeTypes: ' . $audioFile->getMimeType()], 422);
        // }

        // Logging informasi file
        error_log('File name: ' . $audioFile->getClientOriginalName());
        error_log('File mime type: ' . $audioFile->getMimeType());
        error_log('File size: ' . $audioFile->getSize());

        $audioContent = file_get_contents($audioFile->getPathname());

        $client = new SpeechClient([
            'credentials' => config('services.google.application_credentials'),
        ]);

        $audio = (new RecognitionAudio())
            ->setContent($audioContent);

        $config = (new RecognitionConfig())
            ->setEncoding(RecognitionConfig\AudioEncoding::WEBM_OPUS) // Pastikan menggunakan encoding yang sesuai
            ->setSampleRateHertz(16000)
            ->setLanguageCode($request->language);

        try {
            $response = $client->recognize($config, $audio);

            $transcriptions = [];
            foreach ($response->getResults() as $result) {
                $alternatives = $result->getAlternatives();
                foreach ($alternatives as $alternative) {
                    $transcriptions[] = $alternative->getTranscript();
                }
            }

            // Tutup klien untuk menghindari kebocoran sumber daya
            $client->close();

            return response()->json(['transcriptions' => $transcriptions]);
        } catch (\Exception $e) {
            error_log('Error during speech recognition: ' . $e->getMessage());
            return response()->json(['error' => 'Speech recognition failed.'], 500);
        }
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
