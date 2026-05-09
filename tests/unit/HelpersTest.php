<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MaBox_Helpers_Test extends TestCase {

    public function test_is_logged_in_returns_false_for_guests(): void {
        if ( ! function_exists( 'is_user_logged_in' ) ) {
            function is_user_logged_in() { return false; }
        }
        $this->assertFalse( MaBox_Helpers::is_logged_in() );
    }
}
