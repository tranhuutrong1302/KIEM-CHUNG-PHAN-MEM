<?php

use PHPUnit\Framework\TestCase;

class PaymentRuleUnitTest extends TestCase
{
    public function testRejectsWhenAmountMismatch(): void
    {
        $result = evaluateConfirmPayment(1500000, 1499999, false);

        $this->assertFalse($result['ok']);
        $this->assertSame('INVALID_AMOUNT', $result['code']);
    }

    public function testAcceptsWhenAmountMatchesAndNoOrderExists(): void
    {
        $result = evaluateConfirmPayment(1500000, 1500000, false);

        $this->assertTrue($result['ok']);
        $this->assertSame('CONFIRM_PAYMENT_ACCEPTED', $result['code']);
    }

    public function testRejectsWhenOrderExists(): void
    {
        $result = evaluateConfirmPayment(1500000, 1500000, true);

        $this->assertFalse($result['ok']);
        $this->assertSame('ORDER_EXISTS', $result['code']);
    }
}
