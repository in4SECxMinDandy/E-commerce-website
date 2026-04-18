<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ValidImageMagicBytes implements ValidationRule
{
    /**
     * @var array<int, string>
     */
    private array $allowedTypes = ['jpeg', 'png', 'gif', 'webp'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The uploaded file is invalid.');

            return;
        }

        $bytes = @file_get_contents($value->getRealPath(), false, null, 0, 12);

        if ($bytes === false) {
            $fail('The uploaded file could not be inspected.');

            return;
        }

        $type = $this->detectType($bytes);

        if (! in_array($type, $this->allowedTypes, true)) {
            $fail('Only JPEG, PNG, GIF, and WEBP images are allowed.');
        }
    }

    private function detectType(string $bytes): ?string
    {
        if (str_starts_with($bytes, "\xFF\xD8\xFF")) {
            return 'jpeg';
        }

        if (str_starts_with($bytes, "\x89PNG\x0D\x0A\x1A\x0A")) {
            return 'png';
        }

        if (str_starts_with($bytes, 'GIF87a') || str_starts_with($bytes, 'GIF89a')) {
            return 'gif';
        }

        if (str_starts_with($bytes, 'RIFF') && substr($bytes, 8, 4) === 'WEBP') {
            return 'webp';
        }

        return null;
    }
}
