<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContributorProfileLinksTest extends TestCase
{
    public function test_configured_profile_links_are_rendered_for_a_contributor(): void
    {
        $person = new class
        {
            public function getMeta(string $key): ?string
            {
                return match ($key) {
                    'orcid_url' => 'https://orcid.org/0000-0000-0000-0001',
                    'research_gate_url' => 'https://www.researchgate.net/profile/test',
                    default => null,
                };
            }
        };

        $html = view('frontend.scheduledConference.components.contributor-profile-links', compact('person'))
            ->render();

        $this->assertStringContainsString('https://orcid.org/0000-0000-0000-0001', $html);
        $this->assertStringContainsString('https://www.researchgate.net/profile/test', $html);
        $this->assertStringContainsString('cf-contributor-profile-links', $html);
        $this->assertStringContainsString('contributor-profile-logo', $html);
    }
}
