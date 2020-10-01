<?php

namespace Drupal\Tests\shortlinks\Unit;

use Drupal\Tests\UnitTestCase;
// Use PHPUnit\Framework\TestCase;.
use Drupal\shortlinks\Form\GenerateShortLink;

/**
 * Class Testshortlinksunit for unit test.
 */
class Testshortlinksunit extends UnitTestCase {

  /**
   * Decleare protectted variable.
   *
   * @var unit
   */
  protected $unit;

  /**
   * Initiaalize Unit test.
   */
  public function setUp() {
    $this->unit = new GenerateShortLink();
  }

  /**
   * Test randomAlphanumeric function.
   */
  public function testRandomAlphaNumeric() {
    // Positive Test case.
    $titleVals = $this->unit->randomAlphanumeric(9);
    $this->assertEquals(strlen($titleVals), 9);

  }

  /**
   * Test isValidDomainName function.
   */
  public function testisValidDomainName() {
    // Positive Test case.
    $titleVals = $this->unit->isValidDomainName('www.yahoo.com');
    $this->assertEquals($titleVals, 1);
    // Negetive test case.
    $titleVals2 = $this->unit->isValidDomainName('');
    $this->assertEquals($titleVals2, FALSE);
    // Negetive test case.
    $titleVals3 = $this->unit->isValidDomainName('http://yah--.com');
    $this->assertEquals($titleVals3, FALSE);
  }

  /**
   * Close instance.
   */
  public function tearDown() {
    unset($this->unit);
  }

}
