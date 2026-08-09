# Octane with FrankenPHP

This is an opt-in production runtime. Existing PHP-FPM/Nginx Docker, staging Compose, Nixpacks, and `php artisan serve` workflows remain unchanged.

The Octane artifact is one container with four FrankenPHP workers. Sessions, cache, media, private files, and plugin files stay on the container's local named volumes; it does not require Redis or shared storage.

## Start production runtime

Create a production `.env` from `.env.example` and set the normal application values. Replace the example `APP_KEY`, set `APP_URL`, and configure the existing database connection variables. Keep these values for the single-container runtime:

```dotenv
CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DISK=local
OCTANE_SERVER=frankenphp
```

Build and start the separate runtime:

```sh
docker compose -f docker-compose.octane.yml up -d --build
```

Run database migrations before a version that needs them:

```sh
docker compose -f docker-compose.octane.yml exec leconfe-octane php artisan migrate --force
```

The container starts Octane with `--workers=4` and `--max-requests=500`. Adjusting either is an intentional deployment change in `docker-compose.octane.yml`.

## Health, readiness, and lifecycle

Docker health combines ServerSideUp's `healthcheck-octane` process check with the Caddy `/healthz` readiness response. `/healthz` is served by Caddy before Laravel, so it does not query tenant state and must not be used as a benchmark endpoint.

Reload workers after deploying application code:

```sh
docker compose -f docker-compose.octane.yml exec leconfe-octane php artisan octane:reload
```

Stop gracefully with the configured 30-second grace period:

```sh
docker compose -f docker-compose.octane.yml stop
```

The artifact listens on port 8080. Put the existing production TLS proxy or load balancer in front of it.
