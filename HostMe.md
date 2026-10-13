# Run and host Commerce Admin

This guide covers local development on macOS, Windows, and Linux, plus production hosting on Linux. Commerce Admin is a Laravel web app that calls two separate Rust services: the auth service and the product service. The PHP app host needs network access to both gRPC endpoints.

## Requirements

- PHP 8.3 or newer. This project was set up with PHP 8.4 on macOS.
- Composer and the PHP `grpc` extension. The extension must be enabled in both CLI PHP and the PHP web runtime (PHP-FPM, Apache, or IIS).
- Node.js and npm to install and build Vite assets.
- Access to the auth and product services' gRPC endpoints; use TLS in hosted environments.
- `protoc` and `grpc_php_plugin` only when regenerating the checked-in clients. The generated PHP files are committed under `app/Grpc/Generated`.

Laravel also needs its standard PHP extensions: Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer, and XML. Some are bundled with PHP; package names vary by OS. `composer check-platform-reqs` reports missing requirements.

## macOS local setup

### 1. Install PHP 8.4 and gRPC

This is the Homebrew sequence used for this project. The uninstall step is only for replacing an existing Homebrew `php@8.4` installation.

```sh
brew tap shivammathur/php
brew tap shivammathur/extensions
brew uninstall --ignore-dependencies php@8.4
brew install shivammathur/php/php@8.4
brew link --force --overwrite shivammathur/php/php@8.4
brew install shivammathur/extensions/grpc@8.4
```

Check the active PHP and extension:

```sh
php -v
php -m | grep grpc
composer check-platform-reqs
```

### 2. Install application dependencies

From the project directory:

```sh
composer install
npm ci
npm run build
```

Create `.env` and an application key if this is a fresh checkout:

```sh
cp .env.example .env
php artisan key:generate
```

Keep an existing `APP_KEY`; changing it invalidates encrypted sessions. Configure both gRPC service endpoints in `.env`, using hostnames reachable from your Mac:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
AUTH_GRPC_HOST=auth.example.internal:50052
AUTH_GRPC_TIMEOUT=5
AUTH_GRPC_TLS=false
AUTH_GRPC_PROTO_FILE=proto/auth.proto
COMMERCE_GRPC_HOST=micro-product-service.batman:50062
COMMERCE_GRPC_TIMEOUT=5
COMMERCE_GRPC_TLS=false
SESSION_DRIVER=file
SESSION_ENCRYPT=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

The product-service host serves catalog, product image, and order RPCs; those APIs do not use separate host variables. For TLS, set the corresponding `AUTH_GRPC_TLS` and `COMMERCE_GRPC_TLS` values to `true` and configure `AUTH_GRPC_CA_CERT` and/or `COMMERCE_GRPC_CA_CERT` if a custom CA is needed. For mutual TLS, configure each service's matching client certificate and key variables as required.

### 3. Run locally

Run the Laravel development processes:

```sh
composer run dev
```

Open `http://localhost:8000`; stop with `Ctrl+C`. To run the web server and Vite separately, use two terminals:

```sh
php artisan serve --host=127.0.0.1 --port=8000
```

```sh
npm run dev
```

## Windows local setup

The official gRPC support matrix lists PHP on Linux and macOS, not Windows. Use WSL2 with a Linux distribution or Docker for the complete app runtime if you cannot obtain a compatible Windows `grpc` extension. A Windows extension DLL, when available, must match the exact PHP version, architecture, and thread-safety build.

See the [gRPC platform support table](https://grpc.io/docs/) and [PHP's Windows extension installation guide](https://www.php.net/manual/en/install.pecl.windows.php) before attempting a native Windows runtime.

For a native Windows setup, install PHP 8.3+, Composer, and Node.js LTS, then install and enable a matching `grpc` extension DLL using the PHP Windows extension instructions. Check `php --ini` to find the active `php.ini`, add `extension=grpc`, and restart the PHP web runtime after changing it. In PowerShell, confirm and install the app dependencies:

```powershell
php -v
php -m | Select-String grpc
composer check-platform-reqs
composer install
npm ci
npm run build
Copy-Item .env.example .env
php artisan key:generate
```

Set the same auth-service, product-service, and session values as in the macOS example, using Windows-compatible paths for any certificate files. Run with `composer run dev`. PHP gRPC extension availability is the limiting dependency for native Windows hosting; use Linux for a production host.

## Linux server hosting

### 1. Provision the host and runtime

Use a supported Linux release and PHP 8.3 or newer. Install a web server (Nginx below), PHP CLI, PHP-FPM, PHP development headers, PEAR/PECL, the Laravel PHP extensions listed above, Composer, and Node.js/npm on the build host. The exact package names and PHP version depend on the distribution and enabled repositories.

On Debian/Ubuntu, a starting package set is:

```sh
sudo apt update
sudo apt install nginx php-cli php-fpm php-dev php-pear php-curl php-xml php-mbstring php-zip php-intl composer nodejs npm
```

On RHEL-compatible systems (such as Rocky Linux, AlmaLinux, or RHEL), a starting package set is:

```sh
sudo dnf install nginx php-cli php-fpm php-devel php-pear php-mbstring php-xml php-curl php-zip php-intl composer nodejs npm
```

Confirm the installed PHP is at least 8.3. If the distribution's default PHP is older, enable its supported PHP stream/repository or use a maintained PHP repository for that distribution before proceeding. Install the gRPC extension for that exact PHP build, for example with PECL after installing the development headers:

```sh
sudo pecl install grpc
```

Enable `extension=grpc.so` in the PHP configuration used by both CLI and PHP-FPM (often a file under `/etc/php/*/mods-available/` on Debian/Ubuntu or `/etc/php.d/` on RHEL-family systems). Restart PHP-FPM, then check both `php -m` and the PHP-FPM/web runtime. Align CLI and PHP-FPM PHP versions and extension configuration.

### 2. Build a release

Deploy the repository to a release directory. Build frontend assets and install production Composer dependencies:

```sh
npm ci
npm run build
composer install --no-dev --prefer-dist --optimize-autoloader
```

Create a host-managed `.env` for this release. Example:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin.example.com
APP_KEY=base64:REPLACE_WITH_A_STABLE_GENERATED_KEY
AUTH_GRPC_HOST=auth.internal.example:50052
AUTH_GRPC_TIMEOUT=5
AUTH_GRPC_TLS=true
AUTH_GRPC_PROTO_FILE=proto/auth.proto
AUTH_GRPC_CA_CERT=/run/secrets/auth-service-ca.pem
# Configure both only if mutual TLS is required:
# AUTH_GRPC_CLIENT_CERT=/run/secrets/admin-service.pem
# AUTH_GRPC_CLIENT_KEY=/run/secrets/admin-service-key.pem
COMMERCE_GRPC_HOST=product.internal.example:50062
COMMERCE_GRPC_TIMEOUT=5
COMMERCE_GRPC_TLS=true
COMMERCE_GRPC_CA_CERT=/run/secrets/product-service-ca.pem
# Configure both only if mutual TLS is required:
# COMMERCE_GRPC_CLIENT_CERT=/run/secrets/admin-service.pem
# COMMERCE_GRPC_CLIENT_KEY=/run/secrets/admin-service-key.pem
SESSION_DRIVER=file
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Generate `APP_KEY` once with `php artisan key:generate --show`, store it in the host secret manager, and keep it stable across releases. Never commit `.env`, tokens, or certificate/key material. Make sure the PHP-FPM user can read configured certificate files.

### 3. Configure web serving and writable paths

Point Nginx's document root at the app's `public/` directory and pass PHP requests to the matching PHP-FPM pool. Do not expose the project root. Terminate browser HTTPS at Nginx or a trusted load balancer and enable secure cookies. Keep outbound access from the host to both gRPC endpoints; use TLS and a trusted CA.

The PHP-FPM user must be able to write to `storage` and `bootstrap/cache`:

```sh
chmod -R ug+rwX storage bootstrap/cache
```

Once the production environment is set, cache Laravel's optimized configuration and routes:

```sh
php artisan optimize
```

When `.env` changes, rebuild the configuration cache with `php artisan config:cache` or clear it with `php artisan config:clear`, then reload PHP-FPM as needed.

For the full Laravel server requirements and Nginx example, see [Laravel deployment documentation](https://laravel.com/framework/docs/deployment).

### 4. Start and verify

Enable and start Nginx and PHP-FPM with the host's service manager. Check `/up`, the login page, the PHP web runtime's loaded gRPC extension, and connectivity to both configured gRPC endpoints. Successful login also requires an account granted `admin.dashboard.view`.

## Hosting notes

- This app does not need a database, migrations, or queue worker with its configured file/sync drivers. Do not run `php artisan migrate` for this project.
- Persist `storage/framework/sessions` and `storage/logs` across deployments if sessions and logs must survive release switches. For multiple web hosts, configure a shared session store instead of local files.
- Deploy `app/Grpc/Generated`; production hosts do not need `protoc` or `grpc_php_plugin` unless regenerating the clients.
- Permission checks are requested from the auth service on each protected request; there is no permission-check cache.
- Product, category, image, and order pages use the product-service endpoint. Other commerce pages remain placeholders until their APIs are connected.
- Uploaded product image bytes are stored by the product service. Apply its database migrations and deploy its updated gRPC API before enabling image uploads from this admin app.
