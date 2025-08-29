<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class HttpClientService
{
    protected $maxRetries = 3;
    protected $retryDelay = 1000; // milliseconds
    protected $rateLimitDelay = 5000; // milliseconds
    protected $rateLimitKey = 'api_rate_limit';

    /**
     * Make HTTP request with retry mechanism
     */
    public function request(string $method, string $url, array $options = [])
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                // Check rate limit before making request
                $this->checkRateLimit();

                $response = Http::timeout(30)
                    ->retry($this->maxRetries, $this->retryDelay)
                    ->withHeaders($options['headers'] ?? [])
                    ->send($method, $url, $options);

                // Handle rate limit response
                if ($this->isRateLimited($response)) {
                    $this->handleRateLimit($response);
                    $attempt++;
                    continue;
                }

                // Handle successful response
                if ($response->successful()) {
                    return $response;
                }

                // Handle other errors
                if ($response->clientError() || $response->serverError()) {
                    Log::warning("HTTP request failed", [
                        'method' => $method,
                        'url' => $url,
                        'status' => $response->status(),
                        'attempt' => $attempt + 1
                    ]);

                    if ($attempt === $this->maxRetries - 1) {
                        throw new Exception("Request failed after {$this->maxRetries} attempts: " . $response->body());
                    }
                }

            } catch (Exception $e) {
                $lastException = $e;
                Log::error("HTTP request exception", [
                    'method' => $method,
                    'url' => $url,
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);

                if ($attempt === $this->maxRetries - 1) {
                    throw $e;
                }
            }

            $attempt++;
            $this->waitBeforeRetry($attempt);
        }

        throw $lastException ?? new Exception("Request failed after {$this->maxRetries} attempts");
    }

    /**
     * Check if response indicates rate limiting
     */
    protected function isRateLimited($response): bool
    {
        return $response->status() === 429 ||
            $response->header('X-RateLimit-Remaining') === '0' ||
            $response->header('Retry-After');
    }

    /**
     * Handle rate limit response
     */
    protected function handleRateLimit($response): void
    {
        $retryAfter = $response->header('Retry-After');
        $delay = $retryAfter ? (int) $retryAfter * 1000 : $this->rateLimitDelay;

        Log::warning("Rate limit hit, waiting {$delay}ms", [
            'retry_after' => $retryAfter,
            'delay' => $delay
        ]);

        // Store rate limit info in cache
        Cache::put($this->rateLimitKey, [
            'hit_at' => now(),
            'retry_after' => $retryAfter,
            'delay' => $delay
        ], 3600);

        usleep($delay * 1000);
    }

    /**
     * Check rate limit before making request
     */
    protected function checkRateLimit(): void
    {
        $rateLimitInfo = Cache::get($this->rateLimitKey);

        if ($rateLimitInfo) {
            $hitAt = $rateLimitInfo['hit_at'];
            $delay = $rateLimitInfo['delay'];

            // Check if enough time has passed
            if (now()->diffInMilliseconds($hitAt) < $delay) {
                $remainingDelay = $delay - now()->diffInMilliseconds($hitAt);
                Log::info("Rate limit active, waiting {$remainingDelay}ms");
                usleep($remainingDelay * 1000);
            }
        }
    }

    /**
     * Wait before retry with exponential backoff
     */
    protected function waitBeforeRetry(int $attempt): void
    {
        $delay = $this->retryDelay * pow(2, $attempt - 1);
        usleep($delay * 1000);
    }

    /**
     * Make GET request
     */
    public function get(string $url, array $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * Make POST request
     */
    public function post(string $url, array $options = [])
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * Make PUT request
     */
    public function put(string $url, array $options = [])
    {
        return $this->request('PUT', $url, $options);
    }

    /**
     * Make DELETE request
     */
    public function delete(string $url, array $options = [])
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * Set custom retry configuration
     */
    public function setRetryConfig(int $maxRetries, int $retryDelay): self
    {
        $this->maxRetries = $maxRetries;
        $this->retryDelay = $retryDelay;
        return $this;
    }

    /**
     * Set rate limit delay
     */
    public function setRateLimitDelay(int $delay): self
    {
        $this->rateLimitDelay = $delay;
        return $this;
    }
}