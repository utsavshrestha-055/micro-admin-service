<?php

namespace App\Grpc;

use Illuminate\Support\Str;

class ProtobufJsonMapper
{
    /** @return array<string|int, mixed> */
    public function normalizeResponse(array $value): array
    {
        $normalized = [];

        foreach ($value as $key => $item) {
            $normalizedKey = is_string($key) ? Str::snake($key) : $key;
            $normalized[$normalizedKey] = is_array($item) ? $this->normalizeResponse($item) : $item;
        }

        return $normalized;
    }
}
