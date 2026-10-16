<?php

declare(strict_types=1);

namespace Tarikh\Traits;

use Tarikh\Enums\Unit;

/**
 * @mixin \Tarikh\Tarikh
 */
trait Modifiers
{
    public function addDays(int $value = 1): static
    {
        return $this->add($value, Unit::Day);
    }

    public function subDays(int $value = 1): static
    {
        return $this->sub($value, Unit::Day);
    }

    public function addMonths(int $value = 1): static
    {
        return $this->add($value, Unit::Month);
    }

    public function subMonths(int $value = 1): static
    {
        return $this->sub($value, Unit::Month);
    }

    public function addYears(int $value = 1): static
    {
        return $this->add($value, Unit::Year);
    }

    public function subYears(int $value = 1): static
    {
        return $this->sub($value, Unit::Year);
    }

    public function addWeeks(int $value = 1): static
    {
        return $this->add($value, Unit::Week);
    }

    public function subWeeks(int $value = 1): static
    {
        return $this->sub($value, Unit::Week);
    }

    public function addHours(int $value = 1): static
    {
        return $this->add($value, Unit::Hour);
    }

    public function subHours(int $value = 1): static
    {
        return $this->sub($value, Unit::Hour);
    }

    public function addMinutes(int $value = 1): static
    {
        return $this->add($value, Unit::Minute);
    }

    public function subMinutes(int $value = 1): static
    {
        return $this->sub($value, Unit::Minute);
    }

    public function addSeconds(int $value = 1): static
    {
        return $this->add($value, Unit::Second);
    }

    public function subSeconds(int $value = 1): static
    {
        return $this->sub($value, Unit::Second);
    }
}
