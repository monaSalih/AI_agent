use this code in Services\GeminiService.php to follwoing:
```
<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function generate(string $prompt): string
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model');

        if (empty($apiKey) || empty($model)) {
            throw new RuntimeException('Gemini configuration is missing.');
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->timeout(40)
            ->retry(
                [1000, 2000, 4000],
                fn ($exception) =>
                    $exception instanceof RequestException
                    && $exception->response?->status() === 503,
                throw: false
            )
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Gemini API Error: ' .
                $response->status() . ' - ' .
                $response->json('error.message', 'Unknown error')
            );
        }

        $parts = $response->json('candidates.0.content.parts', []);

        $text = collect($parts)
            ->pluck('text')
            ->filter(fn ($text) => is_string($text) && $text !== '')
            ->implode("\n");

        if ($text === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $text;
    }
}
```

2. and double check that config\services file containe the following
```
'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
    ]
```
3. and double check that .env file containe the following
```
GEMINI_API_KEY=your key
GEMINI_MODEL=gemini-3.5-flash-lite
```
4. write this command
```
php artisan config:clear
```

5. In tinker may show this error
```
Illuminate\Http\Client\ConnectionException cURL error 60: SSL certificate problem: unable to get local issuer certificate (see https://curl.se/libcurl/c/libcurl-errors.html) for https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent.

```
* to solve do the follwoing:
1. Install the package
``` 
composer require composer/ca-bundle
```
2. then update Services\GeminiService.php to follwoing:

```
<?php

namespace App\Services;

use Composer\CaBundle\CaBundle;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function generate(string $prompt): string
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model');

        if (empty($apiKey) || empty($model)) {
            throw new RuntimeException('Gemini configuration is missing.');
        }

        $response = Http::acceptJson()
            ->withOptions([
                'verify' => CaBundle::getSystemCaRootBundlePath(),
            ])
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->timeout(40)
            ->retry(
                [1000, 2000, 4000],
                fn ($exception) =>
                    $exception instanceof RequestException
                    && $exception->response?->status() === 503,
                throw: false
            )
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Gemini API Error: ' .
                $response->status() . ' - ' .
                $response->json('error.message', 'Unknown error')
            );
        }

        $parts = $response->json('candidates.0.content.parts', []);

        $text = collect($parts)
            ->pluck('text')
            ->filter(fn ($text) => is_string($text) && $text !== '')
            ->implode("\n");

        if ($text === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $text;
    }
}
```

3. then do the following
```
php artisan config:clear
php artisan tinker
```