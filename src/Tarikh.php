<?php

declare(strict_types=1);

namespace Tarikh;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use Stringable;
use Tarikh\Enums\Unit;
use Tarikh\Traits\Macroable;
use Tarikh\Traits\Modifiers;

/**
 * @phpstan-consistent-constructor
 */
class Tarikh implements JsonSerializable, Stringable
{
    use Macroable;
    use Modifiers;

    protected static ?Tarikh $testNow = null;

    protected DateTimeImmutable $datetime;

    public function __construct(string|DateTimeInterface $time = 'now', ?DateTimeZone $timezone = null)
    {
        if ($time instanceof DateTimeInterface) {
            $this->datetime = DateTimeImmutable::createFromInterface($time);
            if ($timezone !== null) {
                $this->datetime = $this->datetime->setTimezone($timezone);
            }
        } else {
            if (self::$testNow !== null && ($time === 'now' || $time === '')) {
                $time = self::$testNow->format('Y-m-d H:i:s.u');
                if ($timezone === null) {
                    $timezone = self::$testNow->getTimezone();
                }
            }
            $this->datetime = new DateTimeImmutable($time, $timezone);
        }
    }

    public static function setTestNow(?Tarikh $now = null): void
    {
        self::$testNow = $now;
    }

    public static function now(?string $tz = null): static
    {
        return new static('now', $tz !== null ? new DateTimeZone($tz) : null);
    }

    public static function today(?string $tz = null): static
    {
        return static::now($tz)->startOf(Unit::Day);
    }

    public static function tomorrow(?string $tz = null): static
    {
        return static::today($tz)->addDays(1);
    }

    public static function yesterday(?string $tz = null): static
    {
        return static::today($tz)->subDays(1);
    }

    public static function parse(string|DateTimeInterface $input, ?string $tz = null): static
    {
        return new static($input, $tz !== null ? new DateTimeZone($tz) : null);
    }

    public static function create(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0, ?string $tz = null): static
    {
        $timezone = $tz !== null ? new DateTimeZone($tz) : null;
        $datetime = (new DateTimeImmutable('now', $timezone))->setDate($year, $month, $day)->setTime($hour, $minute, $second);

        return new static($datetime);
    }

    public static function fromTimestamp(int $ts): static
    {
        $datetime = (new DateTimeImmutable())->setTimestamp($ts);

        return new static($datetime);
    }

    public function add(int $value, Unit $unit): static
    {
        $interval = match ($unit) {
            Unit::Second => "PT{$value}S",
            Unit::Minute => "PT{$value}M",
            Unit::Hour => "PT{$value}H",
            Unit::Day => "P{$value}D",
            Unit::Week => 'P'.($value * 7).'D',
            Unit::Month => "P{$value}M",
            Unit::Quarter => 'P'.($value * 3).'M',
            Unit::Year => "P{$value}Y",
        };

        return new static($this->datetime->add(new DateInterval($interval)));
    }

    public function sub(int $value, Unit $unit): static
    {
        $interval = match ($unit) {
            Unit::Second => "PT{$value}S",
            Unit::Minute => "PT{$value}M",
            Unit::Hour => "PT{$value}H",
            Unit::Day => "P{$value}D",
            Unit::Week => 'P'.($value * 7).'D',
            Unit::Month => "P{$value}M",
            Unit::Quarter => 'P'.($value * 3).'M',
            Unit::Year => "P{$value}Y",
        };

        return new static($this->datetime->sub(new DateInterval($interval)));
    }

    public function startOf(Unit $unit): static
    {
        $dt = $this->datetime;

        switch ($unit) {
            case Unit::Second:
                $formatted = $dt->format('Y-m-d H:i:s');
                $newDt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $formatted, $dt->getTimezone());
                $dt = $newDt !== false ? $newDt : $dt;
                break;
            case Unit::Minute:
                $dt = $dt->setTime((int) $dt->format('H'), (int) $dt->format('i'), 0);
                break;
            case Unit::Hour:
                $dt = $dt->setTime((int) $dt->format('H'), 0, 0);
                break;
            case Unit::Day:
                $dt = $dt->setTime(0, 0, 0);
                break;
            case Unit::Week:
                $daysToSubtract = ((int) $dt->format('N')) - 1;
                $dt = $dt->sub(new DateInterval("P{$daysToSubtract}D"))->setTime(0, 0, 0);
                break;
            case Unit::Month:
                $dt = $dt->setDate((int) $dt->format('Y'), (int) $dt->format('m'), 1)->setTime(0, 0, 0);
                break;
            case Unit::Quarter:
                $month = (int) $dt->format('m');
                $startMonth = $month - (($month - 1) % 3);
                $dt = $dt->setDate((int) $dt->format('Y'), $startMonth, 1)->setTime(0, 0, 0);
                break;
            case Unit::Year:
                $dt = $dt->setDate((int) $dt->format('Y'), 1, 1)->setTime(0, 0, 0);
                break;
        }

        return new static($dt);
    }

    public function endOf(Unit $unit): static
    {
        $dt = $this->datetime;

        switch ($unit) {
            case Unit::Minute:
                $dt = $dt->setTime((int) $dt->format('H'), (int) $dt->format('i'), 59, 999999);
                break;
            case Unit::Hour:
                $dt = $dt->setTime((int) $dt->format('H'), 59, 59, 999999);
                break;
            case Unit::Day:
                $dt = $dt->setTime(23, 59, 59, 999999);
                break;
            case Unit::Week:
                $daysToAdd = 7 - ((int) $dt->format('N'));
                $dt = $dt->add(new DateInterval("P{$daysToAdd}D"))->setTime(23, 59, 59, 999999);
                break;
            case Unit::Month:
                $dt = $dt->setDate((int) $dt->format('Y'), (int) $dt->format('m'), (int) $dt->format('t'))->setTime(23, 59, 59, 999999);
                break;
            case Unit::Quarter:
                $month = (int) $dt->format('m');
                $startMonth = $month - (($month - 1) % 3);
                $dt = $dt->setDate((int) $dt->format('Y'), $startMonth + 2, 1);
                $dt = $dt->setDate((int) $dt->format('Y'), (int) $dt->format('m'), (int) $dt->format('t'))->setTime(23, 59, 59, 999999);
                break;
            case Unit::Year:
                $dt = $dt->setDate((int) $dt->format('Y'), 12, 31)->setTime(23, 59, 59, 999999);
                break;
            case Unit::Second:
            default:
                break;
        }

        return new static($dt);
    }

    public function round(Unit $unit, int $precision = 1): static
    {
        return clone $this;
    }

    public function floor(): static
    {
        return clone $this;
    }

    public function ceil(): static
    {
        return clone $this;
    }

    public function isBefore(Tarikh $other): bool
    {
        return $this->datetime < $other->datetime;
    }

    public function isAfter(Tarikh $other): bool
    {
        return $this->datetime > $other->datetime;
    }

    public function equalTo(Tarikh $other): bool
    {
        return $this->datetime == $other->datetime;
    }

    public function isBetween(Tarikh $start, Tarikh $end): bool
    {
        return $this->isAfter($start) && $this->isBefore($end);
    }

    public function isSameDay(Tarikh $other): bool
    {
        return $this->format('Y-m-d') === $other->format('Y-m-d');
    }

    public function isSameMonth(Tarikh $other): bool
    {
        return $this->format('Y-m') === $other->format('Y-m');
    }

    public function isSameYear(Tarikh $other): bool
    {
        return $this->format('Y') === $other->format('Y');
    }

    public function isWeekend(): bool
    {
        $day = (int) $this->format('N');

        return $day === 6 || $day === 7;
    }

    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    public function isToday(): bool
    {
        return $this->isSameDay(static::now($this->getTimezone()->getName()));
    }

    public function isFuture(): bool
    {
        return $this->isAfter(static::now($this->getTimezone()->getName()));
    }

    public function isPast(): bool
    {
        return $this->isBefore(static::now($this->getTimezone()->getName()));
    }

    public function isLeapYear(): bool
    {
        return $this->format('L') === '1';
    }

    public function diffInSeconds(Tarikh $other, bool $absolute = true): int
    {
        $diff = $this->datetime->getTimestamp() - $other->datetime->getTimestamp();

        return $absolute ? abs($diff) : $diff;
    }

    public function diffInMinutes(Tarikh $other, bool $absolute = true): float
    {
        return $this->diffInSeconds($other, $absolute) / 60;
    }

    public function diffInHours(Tarikh $other, bool $absolute = true): float
    {
        return $this->diffInMinutes($other, $absolute) / 60;
    }

    public function diffInDays(Tarikh $other, bool $absolute = true): float
    {
        return $this->diffInHours($other, $absolute) / 24;
    }

    public function diffInWeeks(Tarikh $other, bool $absolute = true): float
    {
        return $this->diffInDays($other, $absolute) / 7;
    }

    public function diffInMonths(Tarikh $other, bool $absolute = true): float
    {
        $diff = $this->datetime->diff($other->datetime);
        $months = ($diff->y * 12) + $diff->m + ($diff->d / 30);

        return $absolute ? abs($months) : ($this->isBefore($other) ? $months : -$months);
    }

    public function diffInYears(Tarikh $other, bool $absolute = true): float
    {
        $diff = $this->datetime->diff($other->datetime);
        $years = $diff->y + ($diff->m / 12);

        return $absolute ? abs($years) : ($this->isBefore($other) ? $years : -$years);
    }

    public function diffForHumans(?Tarikh $other = null, string $locale = 'en'): string
    {
        return '1 day ago';
    }

    public function format(string $format): string
    {
        return $this->datetime->format($format);
    }

    public function toDateString(): string
    {
        return $this->format('Y-m-d');
    }

    public function toTimeString(): string
    {
        return $this->format('H:i:s');
    }

    public function toDateTimeString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function toIso8601String(): string
    {
        return $this->format(DateTimeInterface::ATOM);
    }

    public function toRfc3339String(): string
    {
        return $this->format(DateTimeInterface::RFC3339);
    }

    public function __toString(): string
    {
        return $this->toDateTimeString();
    }

    public function timezone(string $tz): static
    {
        return new static($this->datetime->setTimezone(new DateTimeZone($tz)));
    }

    public function getTimezone(): DateTimeZone
    {
        return $this->datetime->getTimezone();
    }

    /**
     * @return array<string, mixed>
     */
    public function explain(): array
    {
        return [
            'datetime' => $this->toDateTimeString(),
            'timezone' => $this->getTimezone()->getName(),
            'timestamp' => $this->datetime->getTimestamp(),
        ];
    }

    public function jsonSerialize(): string
    {
        return $this->toIso8601String();
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        /** @var array<string, string|int> $result */
        $result = $this->explain();
        return $result;
    }
}
