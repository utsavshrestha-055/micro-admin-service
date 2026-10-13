# Commerce Admin

Laravel-based superadmin workspace for identity and access management through the Rust authentication service. The application has no local user, role, permission, session, queue, or cache database. The authentication service is the source of truth for identity and access records.

## Requirements

- PHP 8.3 or newer and Composer
- Node.js and npm for compiling the Tailwind/Vite assets
- `grpcurl` on the server running Laravel
- PHP `proc_open` enabled for the bounded `grpcurl` subprocess
- The checked-in `proto/auth.proto` contract
- Network access from Laravel to the Rust gRPC proxy

The PHP gRPC and protobuf extensions are not required by this transport. The clients invoke the verified protobuf methods using `grpcurl`; request JSON is written to the subprocess standard input, so tokens and passwords are not included in its command line.

## Local setup

```sh
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

Configure the values in `.env` for the local service. The default local example uses plaintext gRPC for the described Nginx proxy. Production deployments must enable TLS and use a trusted CA; configure mutual TLS certificates when the proxy requires client authentication.

```dotenv
AUTH_GRPC_HOST=micro-auth-service.batman:50052
GRPCURL_BINARY=grpcurl
AUTH_GRPC_TIMEOUT=5
AUTH_GRPC_CONNECT_TIMEOUT=2
AUTH_GRPC_TLS=false
SESSION_DRIVER=file
SESSION_ENCRYPT=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Do not set `DB_CONNECTION`, and do not run database migrations. The runtime has no configured database connection. The starter SQLite file is left untouched on disk and is not read by this application. File sessions are encrypted; set `SESSION_SECURE_COOKIE=true` when serving HTTPS if `APP_ENV` is not `production`.

Run the development server with `composer run dev`. The UI is available at the configured `APP_URL`.

## Authentication and authorization

Login uses `AuthenticationService.Login`. The opaque access token remains in Laravel’s encrypted server-side session. Each protected route checks its required permission through `AuthorizationService.CheckPermission`; a denied check returns 403, and an unavailable authorization service returns 503. Expired tokens are cleared and sent back to login. Logout attempts the service RPC and always clears the local session.

User, role, and permission records are read and changed only through their service RPCs. The users search filters the current page because `ListUsers` exposes offset pagination but has no server-side search field. Commerce pages are protected placeholders; they show no fabricated metrics and require dedicated commerce APIs before operational behavior can be added.

The version-controlled permission catalog also includes commerce capability names for future services. Creating those permission records does not create commerce functionality or authorize a commerce API that is not connected.

## Service integration notes

- `proto/auth.proto` is copied from the sibling auth-service checkout and defines the RPC request/response fields used by the clients.
- `app/Grpc/Clients/` contains separate Authentication and Authorization clients; `GrpcurlTransport` applies per-call deadlines, TLS options, and structured logging without credentials.
- The Rust service endpoint was not reachable from the development environment during implementation. Confirm network routing and TLS settings before using the UI against a deployed service.
