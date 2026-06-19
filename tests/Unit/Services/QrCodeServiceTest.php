<?php

namespace Tests\Unit\Services;

use App\Services\QrCodeService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QrCodeServiceTest extends TestCase
{
    private QrCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new QrCodeService;
    }

    public function test_matrix_returns_25x25_grid(): void
    {
        $matrix = $this->service->matrix('PAT-001');

        $this->assertCount(25, $matrix);

        foreach ($matrix as $row) {
            $this->assertCount(25, $row);
        }
    }

    public function test_matrix_contains_only_booleans(): void
    {
        $matrix = $this->service->matrix('PAT-001');

        foreach ($matrix as $row) {
            foreach ($row as $module) {
                $this->assertIsBool($module);
            }
        }
    }

    public function test_matrix_throws_on_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('QR value cannot be empty.');

        $this->service->matrix('');
    }

    public function test_matrix_throws_on_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('QR value cannot be empty.');

        $this->service->matrix('   ');
    }

    public function test_matrix_throws_on_unsupported_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('unsupported characters');

        $this->service->matrix('hello!');
    }

    public function test_matrix_converts_to_uppercase(): void
    {
        $lower = $this->service->matrix('pat-001');
        $upper = $this->service->matrix('PAT-001');

        $this->assertSame($lower, $upper);
    }

    public function test_matrix_is_deterministic(): void
    {
        $first = $this->service->matrix('PAT-123');
        $second = $this->service->matrix('PAT-123');

        $this->assertSame($first, $second);
    }

    public function test_matrix_differs_for_different_inputs(): void
    {
        $matrixA = $this->service->matrix('PAT-001');
        $matrixB = $this->service->matrix('PAT-002');

        $this->assertNotSame($matrixA, $matrixB);
    }

    public function test_matrix_has_finder_patterns(): void
    {
        $matrix = $this->service->matrix('PAT-001');

        // Top-left finder pattern: 7x7 block starting at (0,0) should have dark border
        for ($i = 0; $i < 7; $i++) {
            $this->assertTrue($matrix[0][$i], "Top-left finder: top row at col {$i}");
            $this->assertTrue($matrix[6][$i], "Top-left finder: bottom row at col {$i}");
            $this->assertTrue($matrix[$i][0], "Top-left finder: left col at row {$i}");
            $this->assertTrue($matrix[$i][6], "Top-left finder: right col at row {$i}");
        }
    }

    public function test_svg_returns_valid_svg_string(): void
    {
        $svg = $this->service->svg('PAT-001');

        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
        $this->assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $svg);
    }

    public function test_svg_contains_dark_module_rects(): void
    {
        $svg = $this->service->svg('PAT-001');

        $this->assertStringContainsString('fill="#111827"', $svg);
    }

    public function test_svg_uses_custom_scale(): void
    {
        $svg6 = $this->service->svg('PAT-001', 6);
        $svg10 = $this->service->svg('PAT-001', 10);

        // Different scale should produce different dimensions
        $this->assertNotSame($svg6, $svg10);
    }

    public function test_svg_includes_accessibility_label(): void
    {
        $svg = $this->service->svg('PAT-001');

        $this->assertStringContainsString('role="img"', $svg);
        $this->assertStringContainsString('aria-label="Patient QR code"', $svg);
    }

    public function test_matrix_handles_alphanumeric_characters(): void
    {
        $matrix = $this->service->matrix('DR-123');
        $this->assertCount(25, $matrix);

        $matrix = $this->service->matrix('SCAN-45');
        $this->assertCount(25, $matrix);
    }

    public function test_matrix_handles_odd_length_input(): void
    {
        $matrix = $this->service->matrix('ABC');
        $this->assertCount(25, $matrix);
    }

    public function test_matrix_handles_even_length_input(): void
    {
        $matrix = $this->service->matrix('ABCD');
        $this->assertCount(25, $matrix);
    }
}
