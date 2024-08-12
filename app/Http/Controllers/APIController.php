<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Flac;
use GuzzleHttp\Client;
use FFMpeg\Format\Audio\Wav;
use FFMpeg\Format\Video\Ogg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\SpeechContext;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Illuminate\Support\Facades\Log;

class APIController extends Controller
{
    public function getWord($language, $category)
    {
        $client = new Client();
        $apiKey = env("API_KEY_OPENAI");

        // Cek cache terlebih dahulu
        $cacheKey = "words_{$language}_{$category}";
        $allWords = Cache::get($cacheKey, []);

        // Jika cache kosong atau kurang dari 5 kata, lakukan permintaan baru
        if (is_array($allWords) && count($allWords) < 5) {
            $allWords = $this->generateRandomWord($client, $apiKey, $language, $category);
            Cache::put($cacheKey, $allWords, now()->addDays(1)); // Cache selama 1 hari
        }

        // Ambil 10 kata unik dari cache
        $uniqueWords = array_splice($allWords, 0, 1);
        Cache::put($cacheKey, $allWords); // Perbarui cache dengan kata-kata yang tersisa
        return response()->json($uniqueWords);
    }

    private function generateRandomWord($client, $apiKey, $language, $category)
    {
        $allWords = [];
        $prompt = "Generate a list of 50 random words in the language '$language' for the category '$category'. For each word, provide its translation in Indonesian and its pronunciation. Format the response strictly as: 'word' - 'translation' - 'pronunciation'. Each word should be on a new line. Avoid using any numbers or extra characters in the response.";

        $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a precise random word generator for language learning purposes. Provide words that are appropriate for the specified language and category, ensuring accuracy in translations and pronunciations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 2048,
                'temperature' => 0.5
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                $parts = array_map('trim', explode(' - ', $line));

                if (count($parts) === 3) {
                    $wordDetails = [
                        'word' => $parts[0],
                        'translation' => $parts[1],
                        'pronunciation' => $parts[2]
                    ];
                    $allWords[] = $wordDetails;
                }
            }
        }

        // Tambahkan penanganan kesalahan
        if (empty($allWords)) {
            throw new \Exception("Failed to generate words or parse the response.");
        }

        return $allWords;
    }

    public function textToSpeech(Request $request)
    {
        $bahasa = Bahasa::find($request->input('idBahasa'));
        $client = new TextToSpeechClient([
            'credentials' => config('services.google.application_credentials'),
        ]);

        $inputWord = (new SynthesisInput())
            ->setText(urldecode($request->input('kata')));

        $voice = (new VoiceSelectionParams())
            ->setLanguageCode($bahasa->kode_tts)
            ->setName($request->input('bantuanSuara') === 'wanita' ? $bahasa->suara_wanita : $bahasa->suara_pria);

        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3);

        $response = $client->synthesizeSpeech($inputWord, $voice, $audioConfig);

        $audioContent = $response->getAudioContent();

        $directory = 'public/';
        $files = Storage::files($directory);
        foreach ($files as $file) {
            if (strpos($file, '.mp3') !== false) {
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
        $audioFile = $request->file('audio');
        $bahasa = Bahasa::find($request->input('languageId'));

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

        $speechContextWords = json_decode($request->input('speechContext'), true);
        $speechContext = (new SpeechContext())
            ->setPhrases($speechContextWords);

        $config = (new RecognitionConfig())
            ->setEncoding(RecognitionConfig\AudioEncoding::LINEAR16)
            ->setSampleRateHertz(48000)
            ->setLanguageCode($bahasa->kode_stt)
            ->setSpeechContexts([$speechContext])
            ->setEnableAutomaticPunctuation(true)
            ->setEnableWordTimeOffsets(true)
            ->setMaxAlternatives(3);

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

            return response()->json([
                'transcription' => $transcription,
                'speech_context' => $speechContextWords
            ]);
        } catch (Exception $e) {
            error_log('Error during speech recognition: ' . $e->getMessage());
            return response()->json(['error' => 'Speech recognition failed. Note: ' . $e->getMessage()], 500);
        }
    }

    public function exampleSentences(Request $request)
    {
        $client = new Client();
        $prompt = "Generate 5 example sentences using the word '$request->kata' (meaning: $request->terjemahan) in the language '$request->bahasa'. This word belongs to the category '$request->kategori'. Ensure the sentences clearly demonstrate the meaning of '$request->kata' as '$request->terjemahan'. Each sentence should be followed by its Indonesian translation on a new line, separated by a dash ('-'). No numbering or extra formatting. Example output format:Sentence in $request->bahasa - Translation in Indonesian";

        $apiKey = env("API_KEY_OPENAI");

        try {
            $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an expert example sentence generator for language learning. You specialize in creating accurate, context-appropriate sentences that clearly demonstrate the meaning of specific words. Always use the provided word with its given meaning and category."
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 1024,
                    'temperature' => 0.3
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            if (isset($data['choices'][0]['message']['content'])) {
                $content = $data['choices'][0]['message']['content'];
                $examples = $this->parseExampleSentences($content);
                return response()->json($examples);
            } else {
                return response()->json(["error" => "Tidak ada kalimat yang dihasilkan."]);
            }
        } catch (Exception $e) {
            return response()->json(["error" => "Terjadi kesalahan: " . $e->getMessage()]);
        }
    }

    private function parseExampleSentences($content)
    {
        $lines = explode("\n", $content);
        $examples = [];
        foreach ($lines as $line) {
            $parts = explode(' - ', $line);
            if (count($parts) == 2) {
                $examples[] = [
                    'sentence' => trim($parts[0]),
                    'translation' => trim($parts[1])
                ];
            }
        }
        return $examples;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
