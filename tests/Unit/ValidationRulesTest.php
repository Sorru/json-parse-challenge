<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidationRulesTest extends TestCase
{

    public function test_value_has_three_consecutive_digits()
    {
        $rule = new \App\Rules\CardNumberHasThreeConsecutiveDigits();
        $this->assertTrue($rule->passes('attribute', '00111443'));
    }


    public function test_value_does_not_have_three_consecutive_digits()
    {
        $rule = new \App\Rules\CardNumberHasThreeConsecutiveDigits();
        $this->assertFalse($rule->passes('attribute', '00123456789'));
    }

    public function test_validation_is_false_on_empty_value()
    {
        $rule = new \App\Rules\CardNumberHasThreeConsecutiveDigits();
        $this->assertFalse($rule->passes('attribute', ''));
    }

    public function test_age_between_18_and_65_returns_true()
    {
        $rule = new \App\Rules\AgeBetween18and65orNull();
        $this->assertTrue($rule->passes('attribute', '1989-03-13T14:50:24-08:00'));
    }

    public function test_ages_younger_than_18_returns_false()
    {
        $rule = new \App\Rules\AgeBetween18and65orNull();
        $this->assertFalse($rule->passes('attribute', '2021-03-13T14:50:24-08:00'));
    }


    public function test_ages_older_than_65_returns_false()
    {
        $rule = new \App\Rules\AgeBetween18and65orNull();
        $this->assertFalse($rule->passes('attribute', '1941-03-13T14:50:24-08:00'));
    }


    public function test_null_age_returns_true()
    {
        $rule = new \App\Rules\AgeBetween18and65orNull();
        $this->assertTrue($rule->passes('attribute', null));
    }
}
