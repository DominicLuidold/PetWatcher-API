<?php
declare(strict_types=1);

namespace PetWatcher\Tests\Validation\Rules;

use PetWatcher\Validation\Rules\UnixTimestamp;
use PHPUnit\Framework\TestCase;

class UnixTimestampTest extends TestCase {

    public function testTimestamp(): void {
        $timestampValidation = new UnixTimestamp();

        $this->assertTrue($timestampValidation->validate('1563021960'));
    }
}
