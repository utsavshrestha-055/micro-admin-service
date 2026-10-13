<?php

namespace App\Grpc;

use Auth\AccessTokenRequest;
use Auth\AssignRoleRequest;
use Auth\AuthenticationServiceClient;
use Auth\AuthorizationServiceClient;
use Auth\CheckPermissionRequest;
use Auth\CreatePermissionRequest;
use Auth\CreateRoleRequest;
use Auth\DeletePermissionRequest;
use Auth\DeleteRoleRequest;
use Auth\DeleteUserRequest;
use Auth\GetUserRequest;
use Auth\HealthCheckRequest;
use Auth\ListRolePermissionsRequest;
use Auth\ListUserRolesRequest;
use Auth\ListUsersRequest;
use Auth\LoginRequest;
use Auth\RolePermissionRequest;
use Auth\UnassignRoleRequest;
use Auth\UpdatePermissionRequest;
use Auth\UpdateRoleRequest;
use Auth\UpdateUserRequest;
use Google\Protobuf\Internal\Message;
use Grpc\ChannelCredentials;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GrpcTransport
{
    private ?AuthenticationServiceClient $authenticationClient = null;

    private ?AuthorizationServiceClient $authorizationClient = null;

    public function __construct(private ProtobufJsonMapper $jsonMapper) {}

    /** @var array<string, class-string<Message>> */
    private const REQUEST_TYPES = [
        'AuthenticationService.HealthCheck' => HealthCheckRequest::class,
        'AuthenticationService.Login' => LoginRequest::class,
        'AuthenticationService.Logout' => AccessTokenRequest::class,
        'AuthenticationService.GetUser' => GetUserRequest::class,
        'AuthenticationService.ListUsers' => ListUsersRequest::class,
        'AuthenticationService.UpdateUser' => UpdateUserRequest::class,
        'AuthenticationService.DeleteUser' => DeleteUserRequest::class,
        'AuthorizationService.CheckPermission' => CheckPermissionRequest::class,
        'AuthorizationService.ListRoles' => AccessTokenRequest::class,
        'AuthorizationService.CreateRole' => CreateRoleRequest::class,
        'AuthorizationService.UpdateRole' => UpdateRoleRequest::class,
        'AuthorizationService.DeleteRole' => DeleteRoleRequest::class,
        'AuthorizationService.ListPermissions' => AccessTokenRequest::class,
        'AuthorizationService.CreatePermission' => CreatePermissionRequest::class,
        'AuthorizationService.UpdatePermission' => UpdatePermissionRequest::class,
        'AuthorizationService.DeletePermission' => DeletePermissionRequest::class,
        'AuthorizationService.AssignRole' => AssignRoleRequest::class,
        'AuthorizationService.UnassignRole' => UnassignRoleRequest::class,
        'AuthorizationService.ListUserRoles' => ListUserRolesRequest::class,
        'AuthorizationService.ListRolePermissions' => ListRolePermissionsRequest::class,
        'AuthorizationService.GrantPermissionToRole' => RolePermissionRequest::class,
        'AuthorizationService.RevokePermissionFromRole' => RolePermissionRequest::class,
    ];

    /**
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    public function call(string $service, string $method, array $request): array
    {
        $startedAt = microtime(true);
        $requestId = (string) Str::uuid();
        $operation = $service.'.'.$method;

        try {
            $requestType = self::REQUEST_TYPES[$operation] ?? null;

            if ($requestType === null) {
                throw new RuntimeException('The requested authentication service operation is not supported.');
            }

            $client = $this->client($service);
            $requestMessage = $this->requestMessage($requestType, $request);
            $accessToken = $request['accessToken'] ?? null;
            $metadata = ['x-request-id' => [$requestId]];

            if (is_string($accessToken) && $accessToken !== '') {
                $metadata['authorization'] = ['Bearer '.$accessToken];
            }

            $timeoutMicroseconds = (int) round((float) config('auth_service.timeout_seconds', 5) * 1_000_000);
            [$response, $status] = $client->{$method}($requestMessage, $metadata, ['timeout' => $timeoutMicroseconds])->wait();

            if ($status->code !== 0) {
                $grpcStatus = $this->statusName((int) $status->code);
                $this->logOperation($operation, $requestId, $startedAt, strtolower($grpcStatus));
                throw new GrpcRequestException($grpcStatus);
            }

            if (! $response instanceof Message) {
                throw new RuntimeException('The authentication service returned an invalid response.');
            }

            $decodedResponse = json_decode($response->serializeToJsonString(), true, flags: JSON_THROW_ON_ERROR);

            if (! is_array($decodedResponse)) {
                throw new RuntimeException('The authentication service returned an invalid response.');
            }

            $normalizedResponse = $this->jsonMapper->normalizeResponse($decodedResponse);
            $this->logOperation($operation, $requestId, $startedAt, 'ok');

            return $normalizedResponse;
        } catch (GrpcRequestException $exception) {
            throw $exception;
        } catch (RuntimeException $exception) {
            $this->logOperation($operation, $requestId, $startedAt, 'client_error', [
                'exception_class' => $exception::class,
                'cause_class' => $exception->getPrevious() !== null ? $exception->getPrevious()::class : null,
            ]);
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logOperation($operation, $requestId, $startedAt, 'client_error', [
                'exception_class' => $exception::class,
                'cause_class' => $exception->getPrevious() !== null ? $exception->getPrevious()::class : null,
            ]);
            throw new RuntimeException('The authentication service request failed.', previous: $exception);
        }
    }

    private function client(string $service): AuthenticationServiceClient|AuthorizationServiceClient
    {
        $host = config('auth_service.host');

        if (! is_string($host) || trim($host) === '') {
            throw new RuntimeException('The authentication service endpoint is not configured.');
        }

        $options = ['credentials' => $this->channelCredentials()];

        return match ($service) {
            'AuthenticationService' => $this->authenticationClient ??= new AuthenticationServiceClient($host, $options),
            'AuthorizationService' => $this->authorizationClient ??= new AuthorizationServiceClient($host, $options),
            default => throw new RuntimeException('The requested authentication service is not supported.'),
        };
    }

    private function channelCredentials(): ?ChannelCredentials
    {
        if (! config('auth_service.tls.enabled')) {
            return ChannelCredentials::createInsecure();
        }

        $caCertificate = config('auth_service.tls.ca_certificate');
        $clientCertificate = config('auth_service.tls.client_certificate');
        $clientKey = config('auth_service.tls.client_key');

        if ((is_string($clientCertificate) && $clientCertificate !== '') !== (is_string($clientKey) && $clientKey !== '')) {
            throw new RuntimeException('Both client certificate and client key must be configured for mutual TLS.');
        }

        $rootCertificates = is_string($caCertificate) && $caCertificate !== ''
            ? file_get_contents($caCertificate)
            : null;

        if (is_string($caCertificate) && $caCertificate !== '' && $rootCertificates === false) {
            throw new RuntimeException('The configured authentication service CA certificate could not be read.');
        }

        $certificateChain = is_string($clientCertificate) && $clientCertificate !== ''
            ? file_get_contents($clientCertificate)
            : null;
        $privateKey = is_string($clientKey) && $clientKey !== '' ? file_get_contents($clientKey) : null;

        if (($certificateChain === false) || ($privateKey === false)) {
            throw new RuntimeException('The configured authentication service client certificate could not be read.');
        }

        return ChannelCredentials::createSsl($rootCertificates ?: null, $privateKey ?: null, $certificateChain ?: null);
    }

    /**
     * @param  class-string<Message>  $requestType
     * @param  array<string, mixed>  $fields
     */
    private function requestMessage(string $requestType, array $fields): Message
    {
        $request = new $requestType;

        foreach ($fields as $field => $value) {
            $setter = 'set'.ucfirst($field);

            if (! method_exists($request, $setter)) {
                throw new RuntimeException('The authentication service request contains an unsupported field.');
            }

            $request->{$setter}($value);
        }

        return $request;
    }

    private function statusName(int $statusCode): string
    {
        return match ($statusCode) {
            1 => 'CANCELLED',
            2 => 'UNKNOWN',
            3 => 'INVALID_ARGUMENT',
            4 => 'DEADLINE_EXCEEDED',
            5 => 'NOT_FOUND',
            6 => 'ALREADY_EXISTS',
            7 => 'PERMISSION_DENIED',
            8 => 'RESOURCE_EXHAUSTED',
            9 => 'FAILED_PRECONDITION',
            10 => 'ABORTED',
            11 => 'OUT_OF_RANGE',
            12 => 'UNIMPLEMENTED',
            13 => 'INTERNAL',
            14 => 'UNAVAILABLE',
            15 => 'DATA_LOSS',
            16 => 'UNAUTHENTICATED',
            default => 'RPC_ERROR',
        };
    }

    /** @param array<string, class-string<\Throwable>|null> $context */
    private function logOperation(string $operation, string $requestId, float $startedAt, string $status, array $context = []): void
    {
        Log::log($status === 'ok' ? 'info' : 'warning', 'Authentication service RPC completed.', [
            'operation' => $operation,
            'request_id' => $requestId,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'status' => $status,
        ] + $context);
    }
}
