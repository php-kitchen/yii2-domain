<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Tests\Base\TestGuyTests;

/**
 * Unit test for {@link \PHPKitchen\CodeSpecs\Integration\Codeception\CodeSpecs}
 *
 * @coversDefaultClass \PHPKitchen\CodeSpecs\Integration\Codeception\CodeSpecs
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class CodeSpecsTest extends Unit {
    use TestGuyTests;
    /**
     * @var \UnitTester
     */
    protected $tester;

}
