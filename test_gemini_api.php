<?php
// Test script để kiểm tra Gemini API

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

$apiKey = 'AIzaSyBQjPKXDjgC6T5VF3W0C6UZoPCni9ZxtFw';

$models = [
    'gemini-1.5-pro-latest',
    'gemini-1.5-flash-latest', 
    'gemini-pro',
    'gemini-1.5-flash',
    'gemini-2.0-flash',
];

echo "Testing Gemini API models...\n\n";

foreach ($models as $model) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    
    try {
        $response = Http::timeout(10)
            ->withoutVerifying()
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [['text' => 'Hello']]
                    ]
                ]
            ]);
        
        echo "✅ Model: {$model}\n";
        echo "   Status: " . $response->status() . "\n";
        if ($response->successful()) {
            echo "   ✓ SUCCESS!\n";
        }
    } catch (\Exception $e) {
        echo "❌ Model: {$model}\n";
        echo "   Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>