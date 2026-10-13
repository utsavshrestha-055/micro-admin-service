<?php

namespace App\Grpc;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GrpcurlTransport
{
    public function __construct(private ProtobufJsonMapper $jsonMapper) {}

    /**
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    public function call(string $service, string $method, array $request): array
    {
        $startedAt = microtime(true);
        $requestId = (string) Str::uuid();
        $operation = $service.'.'.$method;
        $host = config('auth_service.host');
        $binary = config('auth_service.grpcurl_binary');
        $clientCertificate = config('auth_service.tls.client_certificate');
        $clientKey = config('auth_service.tls.client_key');

        if (! is_string($host) || trim($host) === '' || ! is_string($binary) || trim($binary) === '') {
            $this->logOperation($operation, $requestId, $startedAt, 'configuration_error');
            throw new RuntimeException('The authentication service endpoint is not configured.');
        }

        if (! is_file((string) config('auth_service.proto_file'))) {
            $this->logOperation($operation, $requestId, $startedAt, 'contract_missing');
            throw new RuntimeException('The authentication service protobuf contract is missing.');
        }

        if ((is_string($clientCertificate) && $clientCertificate !== '') !== (is_string($clientKey) && $clientKey !== '')) {
            $this->logOperation($operation, $requestId, $startedAt, 'configuration_error');
            throw new RuntimeException('Both client certificate and client key must be configured for mutual TLS.');
        }

        $arguments = [
            $binary,
            '-max-time',
            (string) config('auth_service.timeout_seconds'),
            '-connect-timeout',
            (string) config('auth_service.connect_timeout_seconds'),
            '-import-path',
            (string) config('auth_service.proto_directory'),
            '-proto',
            (string) config('auth_service.proto_file'),
            '-d',
            '@',
        ];

        if (! config('auth_service.tls.enabled')) {
            $arguments[] = '-plaintext';
        } else {
            $caCertificate = config('auth_service.tls.ca_certificate');
            $clientCertificate = config('auth_service.tls.client_certificate');
            $clientKey = config('auth_service.tls.client_key');

            if (is_string($caCertificate) && $caCertificate !== '') {
                array_push($arguments, '-cacert', $caCertificate);
            }

            if (is_string($clientCertificate) && $clientCertificate !== '' && is_string($clientKey) && $clientKey !== '') {
                array_push($arguments, '-cert', $clientCertificate, '-key', $clientKey);
            }
        }

        $arguments[] = $host;
        $arguments[] = 'auth.'.$service.'.'.$method;

        $process = @proc_open($arguments, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, base_path(), null, ['bypass_shell' => true]);

        if (! is_resource($process)) {
            $this->logOperation($operation, $requestId, $startedAt, 'process_error');
            throw new RuntimeException('The authentication service client could not start.');
        }

        try {
            fwrite($pipes[0], json_encode($request, JSON_THROW_ON_ERROR));
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            if ($exitCode !== 0) {
                $grpcStatus = $this->grpcStatus($stderr);
                $this->logOperation($operation, $requestId, $startedAt, strtolower($grpcStatus));
                throw new GrpcRequestException($grpcStatus);
            }

            $response = json_decode($stdout, true, flags: JSON_THROW_ON_ERROR);

            if (! is_array($response)) {
                $this->logOperation($operation, $requestId, $startedAt, 'invalid_response');
                throw new RuntimeException('The authentication service returned an invalid response.');
            }

            $response = $this->jsonMapper->normalizeResponse($response);

            $this->logOperation($operation, $requestId, $startedAt, 'ok');

            return $response;
        } catch (Throwable $exception) {
            if (is_resource($process)) {
                proc_terminate($process);
                proc_close($process);
            }

            if ($exception instanceof RuntimeException) {
                throw $exception;
            }

            $this->logOperation($operation, $requestId, $startedAt, 'client_error');
            throw new RuntimeException('The authentication service returned an invalid response.');
        }
    }

    private function grpcStatus(string $stderr): string
    {
        if (preg_match('/Code:\s*([A-Za-z_]+)/i', $stderr, $matches) === 1) {
            return strtoupper($matches[1]);
        }

        return 'RPC_ERROR';
    }

    private function logOperation(string $operation, string $requestId, float $startedAt, string $status): void
    {
        Log::log($status === 'ok' ? 'info' : 'warning', 'Authentication service RPC completed.', [
            'operation' => $operation,
            'request_id' => $requestId,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'status' => $status,
        ]);
    }
}
