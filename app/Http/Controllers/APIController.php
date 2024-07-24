<?php

namespace App\Http\Controllers;

use Exception;
use FFMpeg\FFMpeg;
use GuzzleHttp\Client;
use FFMpeg\Format\Audio\Wav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

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
        $prompt = "Generate a list of 50 random words in the language '$language' for the category '$category'. For each word, provide its translation in Indonesian and its pronunciation. Format the response as: word - translation - pronunciation.";

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
                        'content' => 'You are a random word generator.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 4096,
                'temperature' => 0.7
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];
            $lines = explode("\n", $content);

            foreach ($lines as $line) {
                // Pemisahan kata, terjemahan, dan cara baca
                $parts = array_map('trim', explode(' - ', $line));

                if (count($parts) === 3) {
                    // Menghapus nomor atau karakter lain dari kata
                    $word = preg_replace('/^\d+\.\s*/', '', $parts[0]);

                    $wordDetails = [
                        'word' => $word,
                        'translation' => $parts[1],
                        'pronunciation' => $parts[2]
                    ];
                    $allWords[] = $wordDetails;
                }
            }
        }

        return $allWords;
    }

    public function textToSpeech($language_code, $word)
    {
        $client = new TextToSpeechClient([
            'credentials' => config('services.google.application_credentials'),
        ]);

        $inputWord = (new SynthesisInput())
            ->setText($word);

        $voice = (new VoiceSelectionParams())
            ->setLanguageCode($language_code)
            ->setSsmlGender(SsmlVoiceGender::NEUTRAL);

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
            ->setLanguageCode($request->language_code);

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
        } catch (Exception $e) {
            error_log('Error during speech recognition: ' . $e->getMessage());
            return response()->json(['error' => 'Speech recognition failed. Note: ' . $e->getMessage()], 500);
        }
    }

    public function exampleSentences($language, $word)
    {
        $client = new Client();
        $prompt = "Generate 5 example sentences with the word '$word' in the language '$language' and their translations to Indonesian. Each sentence and its translation should be on a new line, separated by a dash ('-'). No numbering or extra formatting. Example output:\nSentence in language - Translation in Indonesian";
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
                            'content' => 'You are an example sentence generator.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 512,
                    'temperature' => 0.7
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
        $sentences = explode("\n", $content);
        $examples = [];

        foreach ($sentences as $sentence) {
            if (strpos($sentence, ' - ') !== false) {
                list($exampleSentence, $translation) = explode(' - ', $sentence);
                $examples[] = [
                    'sentence' => trim($exampleSentence),
                    'translation' => trim($translation)
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
