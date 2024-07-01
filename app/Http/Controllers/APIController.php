<?php

namespace App\Http\Controllers;

use Exception;
use FFMpeg\FFMpeg;
use GuzzleHttp\Client;
use FFMpeg\Format\Audio\Wav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;

class APIController extends Controller
{
    function generateRandomWord($language, $category)
    {
        $client = new Client();
        $prompt = "Generate 30 kata acak dari bahasa '$language' dalam kategori '$category'. Contoh kategori -> hewan: gajah, marmut, kucing, anjing. Buah: apel, pepaya, mangga, jeruk. Buat dalam format array";
        $apiKey = env("API_KEY_OPENAI");
        try {
            $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a random word generator.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 256,
                    'temperature' => 0.7
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            if (isset($data['choices'][0]['message']['content'])) {
                return response()->json(json_decode($data['choices'][0]['message']['content']));
            } else {
                return "Tidak ada kata yang dihasilkan.";
            }
        } catch (Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    // public function generateRandomWord()
    // {
    //     $curl = curl_init();

    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => "https://wordsapiv1.p.rapidapi.com/words/?random=true",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "GET",
    //         CURLOPT_HTTPHEADER => [
    //             "x-rapidapi-host: wordsapiv1.p.rapidapi.com",
    //             "x-rapidapi-key: d2ecf1000cmshe67379045cd9679p1b9e0djsn5ed8bb668b83"
    //         ],
    //     ]);

    //     $response = curl_exec($curl);
    //     $err = curl_error($curl);

    //     curl_close($curl);

    //     if ($err) {
    //         return response()->json(['error' => $err]);
    //     } else {
    //         return response()->json($response);
    //     }
    // }

    public function translate($word)
    {
        $authKey = env("AUTH_KEY_DEEPL", null);
        $translator = new \DeepL\Translator($authKey);

        $result = $translator->translateText($word, null, "id");
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

        $directory = 'public/';
        $files = Storage::files($directory);
        foreach ($files as $file) {
            if (strpos($file, '.mp3') !== false) {
                Storage::delete($file);
            }
            if (strpos($file, '.wav') !== false) {
                Storage::delete($file);
            }
        }

        $randomIdFile = uniqid();
        $newFilePath = $directory . $randomIdFile . '.mp3';

        Storage::put($newFilePath, $audioContent);

        $url = Storage::url($newFilePath);

        return response()->json(['audio_url' => $url]);
    }

    public function speechToText(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimetypes:video/webm',
            'language' => 'required|string',
        ]);

        $audioFile = $request->file('audio');

        // Konversi file audio dari webm ke wav
        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($audioFile->getPathname());
        $wavPath = storage_path('app/public/' . uniqid() . '.wav');
        $format = new Wav();
        $format->setAudioChannels(1);
        $audio->save($format, $wavPath);

        // Baca konten file wav
        $audioContent = file_get_contents($wavPath);

        // Inisiasi client Google Speech-to-Text
        $client = new SpeechClient([
            'credentials' => config('services.google.application_credentials'),
        ]);

        $audio = (new RecognitionAudio())
            ->setContent($audioContent);

        $config = (new RecognitionConfig())
            ->setSampleRateHertz(48000)
            ->setLanguageCode($request->language);

        try {
            $response = $client->recognize($config, $audio);

            $transcription = [];
            foreach ($response->getResults() as $result) {
                $alternatives = $result->getAlternatives();
                foreach ($alternatives as $alternative) {
                    $transcription[] = $alternative->getTranscript();
                }
            }

            // Tutup klien untuk menghindari kebocoran sumber daya
            $client->close();

            // Hapus file wav setelah diproses
            unlink($wavPath);

            return response()->json(['transcription' => $transcription]);
        } catch (\Exception $e) {
            error_log('Error during speech recognition: ' . $e->getMessage());
            return response()->json(['error' => 'Speech recognition failed. Note:' . $e->getMessage()], 500);
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
