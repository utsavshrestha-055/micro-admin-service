# Commerce Admin

Commerce Admin is a Laravel web application for administrators managing identity, access, and commerce data. It delegates authentication and authorization to the Rust auth service and catalog, product image, and order operations to the Rust product service.

The application is an administration shell, not the source of commerce data. Product, category, product image, and order operations are connected to the product service. Merchant, customer, inventory, reporting, settings, and audit-log pages remain permission-protected placeholders until their respective services are connected.

## What it provides

- Login and logout through the external Authentication service.
- User browsing, editing, deletion, and role assignment through the auth APIs.
- Role and permission management through the Authorization service.
- Per-route permission checks against the Authorization service.
- A dashboard with an auth-service health status.
- Product, category, product image, and order management through the product service.
- Placeholder pages for commerce areas that do not yet have connected APIs.

## How it is structured

The browser communicates with Laravel over HTTP. Laravel keeps the opaque access token and a small user profile in an encrypted server-side session. For backend operations, `GrpcTransport` calls generated PHP clients over gRPC; the token is sent as authorization metadata and is also included in request messages where the checked-in proto defines that field.

`proto/auth.proto` is the RPC contract. Generated messages and service clients live under `app/Grpc/Generated`. The handwritten adapters in `app/Grpc/Clients` and services in `app/Services` keep controllers independent of gRPC details. Permission checks are made per request; the app does not cache them.

The auth service is the source of truth for users, roles, permissions, and access tokens. This app has no configured database connection and does not require database migrations. Sessions, cache, and queues use local file, file, and synchronous drivers by default.

## Technology and requirements

- Laravel 13 and PHP 8.3 or newer (PHP 8.4 is used in the documented macOS setup).
- PHP `grpc` extension, Composer packages `grpc/grpc` and `google/protobuf`.
- Node.js and npm to build the Vite/Tailwind assets.
- Network access from the web host to the Rust auth and product services' gRPC endpoints.
- `protoc` and `grpc_php_plugin` only when regenerating stubs.

The application host must be able to reach both the auth service and product service gRPC endpoints. Platform-specific local setup and Linux hosting steps are documented in [HostMe.md](HostMe.md). PHP gRPC is officially supported on Linux and macOS; Windows users should use WSL2 or Docker unless a compatible native extension is available.

## Local development

See [HostMe.md](HostMe.md) for macOS, Windows, and Linux setup, local run steps, and Linux host deployment process.

Quick start after PHP, Composer, Node.js, and `ext-grpc` are installed:

```sh
composer install
cp .env.example .env
php artisan key:generate
npm ci
npm run build
```

Set `AUTH_GRPC_HOST` and `COMMERCE_GRPC_HOST` to endpoints reachable from the admin app, then run:

```sh
composer run dev
```

The local UI defaults to `http://localhost:8000`.

## Configuration

The key gRPC settings are:

| Setting | Purpose |
| --- | --- |
| `AUTH_GRPC_HOST` | Auth service hostname and port, such as `auth.internal:50052`. |
| `AUTH_GRPC_TIMEOUT` | Per-call timeout in seconds. |
| `AUTH_GRPC_TLS` | Enable TLS; use `true` on production hosts. |
| `AUTH_GRPC_CA_CERT` | Optional trusted CA certificate path. |
| `AUTH_GRPC_CLIENT_CERT` / `AUTH_GRPC_CLIENT_KEY` | Optional mutual-TLS client certificate and key. Configure both together. |
| `AUTH_GRPC_PROTO_FILE` | Proto path used by the stub-generation target; defaults to `proto/auth.proto`. |
| `COMMERCE_GRPC_HOST` | Product service hostname and port. It serves catalog, product images, and order RPCs; default: `micro-product-service.batman:50062`. |
| `COMMERCE_GRPC_TIMEOUT` | Per-call timeout in seconds; default: `5`. |
| `COMMERCE_GRPC_TLS` | Enable TLS for the product service; default: `false` for the local plaintext endpoint. |
| `COMMERCE_GRPC_CA_CERT` | Optional trusted CA certificate path for product-service TLS. |
| `COMMERCE_GRPC_CLIENT_CERT` / `COMMERCE_GRPC_CLIENT_KEY` | Optional mutual-TLS client certificate and key for product-service calls. Configure both together. |

`AUTH_GRPC_HOST` and `COMMERCE_GRPC_HOST` are separate endpoints. Categories, products, images, and orders share the product-service host; they do not need separate host variables. The local `.env.example` uses plaintext gRPC for development. Production should use TLS and a trusted CA for both services; enable mutual TLS if required. Do not commit `.env`, access tokens, or certificate/key material.

Regenerate the checked-in PHP clients after changing the proto contract:

```sh
make grpc-stubs
```

Override the compiler or plugin locations with `PROTOC_BINARY` and `GRPC_PHP_PLUGIN_BINARY` if they are not on `PATH`.

## Authentication behavior

Login calls `AuthenticationService.Login`, stores the resulting token in the Laravel session, then checks `admin.dashboard.view`. Protected routes ask `AuthorizationService.CheckPermission` for their required permission. A denied permission returns 403; an expired/unauthenticated token clears the session and redirects to login; service availability failures return 503. Logout attempts remote token revocation and always clears the local session. Product, category, image, and order requests use `COMMERCE_GRPC_HOST`; uploads are forwarded to the product service, which stores their bytes in PostgreSQL.

For route details, see `routes/web.php`. The `/up` health route is provided by Laravel. Application and RPC logs are written under `storage/logs` by the default local logging configuration.
