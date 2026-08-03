<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $endpoint = config('services.google_recaptcha');

        try {
            $response = Http::asForm()->post($endpoint['url'], [
                'secret' => $endpoint['secret_key'],
                'response' => $value,
            ])->json();
        } catch (\Throwable $e) {
            $fail('No se pudo validar el captcha. Intente de nuevo.');

            return;
        }

        if (! is_array($response) || ! ($response['success'] ?? false)) {
            $fail('El captcha no es válido');
        }
    }
}
