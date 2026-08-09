<?php

namespace Tests\Feature;

use Tests\TestCase;

class OctaneFrankenPhpRuntimeConfigurationTest extends TestCase
{
    public function test_octane_uses_frankenphp_when_started(): void
    {
        // Given: the application has been configured for its optional Octane runtime.
        // When: Octane resolves its configured server.
        // Then: it uses FrankenPHP.
        $this->assertSame('frankenphp', config('octane.server'));
        $this->assertStringContainsString('OCTANE_SERVER=frankenphp', file_get_contents(base_path('.env.example')));
    }

    public function test_octane_deployment_runs_bounded_workers_with_health_and_readiness_checks(): void
    {
        // Given: the optional Octane deployment is selected.
        // When: its production Compose and Caddy configuration are read.
        // Then: they start bounded FrankenPHP workers and expose non-tenant checks.
        $compose = file_get_contents(base_path('docker-compose.octane.yml'));
        $dockerfile = file_get_contents(base_path('Dockerfile.octane'));
        $caddyfile = file_get_contents(base_path('docker/octane/Caddyfile'));

        $this->assertStringContainsString('Dockerfile.octane', $compose);
        $this->assertStringContainsString('STOPSIGNAL SIGINT', $dockerfile);
        $this->assertStringContainsString('--workers=4', $compose);
        $this->assertStringContainsString('--max-requests=500', $compose);
        $this->assertStringContainsString('healthcheck-octane', $compose);
        $this->assertStringContainsString('octane-storage:/var/www/html/storage', $compose);
        $this->assertStringContainsString('octane-plugins:/var/www/html/plugins', $compose);
        $this->assertStringContainsString('stop_grace_period: 30s', $compose);
        $this->assertStringContainsString('path /healthz', $caddyfile);
        $this->assertStringContainsString('respond @readiness "ready" 200', $caddyfile);
    }

    public function test_fpm_deployment_defaults_remain_available(): void
    {
        // Given: the default deployment files.
        // When: their runtime declarations are inspected.
        // Then: they continue to select FPM instead of Octane.
        $this->assertStringContainsString('fpm-nginx', file_get_contents(base_path('dockerfile')));
        $this->assertStringContainsString('fpm-nginx', file_get_contents(base_path('Dockerfile.staging')));
        $this->assertStringContainsString('php-fpm', file_get_contents(base_path('nixpacks.toml')));
        $this->assertStringNotContainsString('octane:', file_get_contents(base_path('docker-compose.staging.yml')));
    }
}
