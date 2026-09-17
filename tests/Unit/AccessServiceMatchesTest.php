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
}
