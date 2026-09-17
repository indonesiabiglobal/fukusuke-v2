<?php

namespace Tests\Unit;

use App\Services\AccessService;
use PHPUnit\Framework\TestCase;

class AccessServiceMatchesTest extends TestCase
{
    private AccessService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AccessService();
    }

    public function test_matches_true_when_user_has_required_code(): void
    {
        $this->assertTrue($this->service->matches(['ORDER', 'MASTER'], ['ORDER']));
    }

    public function test_matches_false_when_user_lacks_required_code(): void
    {
        $this->assertFalse($this->service->matches(['MASTER'], ['ORDER']));
    }

    public function test_matches_true_for_any_of_multiple_required_codes(): void
    {
        $this->assertTrue($this->service->matches(['JAM-KERJA-SEITAI'], ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI']));
    }

    public function test_matches_true_when_user_has_wildcard(): void
    {
        $this->assertTrue($this->service->matches(['*'], ['ADM']));
    }

    public function test_matches_true_when_no_codes_required(): void
    {
        $this->assertTrue($this->service->matches([], []));
    }

    public function test_matches_false_when_user_has_no_codes(): void
    {
        $this->assertFalse($this->service->matches([], ['ORDER']));
    }

    public function test_resolve_landing_picks_first_priority_code(): void
    {
        $this->assertSame('/nippo-infure', $this->service->resolveLanding(['NIPPO-INFURE']));
    }

    public function test_resolve_landing_handles_previously_failing_kenpin_only_role(): void
    {
        $this->assertSame('/kenpin-infure', $this->service->resolveLanding(['KENPIN']));
    }

    public function test_resolve_landing_wildcard_goes_to_nippo_infure(): void
    {
        $this->assertSame('/nippo-infure', $this->service->resolveLanding(['*']));
    }

    public function test_resolve_landing_falls_back_to_printer_settings_when_no_mapped_code(): void
    {
        $this->assertSame('/printer-settings', $this->service->resolveLanding(['SOME-UNMAPPED-CODE']));
    }

    public function test_resolve_landing_falls_back_to_printer_settings_when_no_codes_at_all(): void
    {
        $this->assertSame('/printer-settings', $this->service->resolveLanding([]));
    }

    public function test_resolve_landing_uses_declaration_order_when_user_has_multiple_codes(): void
    {
        $this->assertSame('/nippo-seitai', $this->service->resolveLanding(['WAREHOUSE', 'NIPPO-SEITAI']));
    }
}
