<?php

declare(strict_types=1);

use Tarikh\Enums\Unit;
use Tarikh\Tarikh;

it('creates a tarikh instance from now', function () {
    $tarikh = Tarikh::now();
    expect($tarikh)->toBeInstanceOf(Tarikh::class);
});

it('creates a tarikh instance from parse', function () {
    $tarikh = Tarikh::parse('2024-01-01 12:00:00');
    expect($tarikh->format('Y-m-d H:i:s'))->toBe('2024-01-01 12:00:00');
});

it('can freeze time', function () {
    $now = Tarikh::parse('2024-01-01 12:00:00');
    Tarikh::setTestNow($now);

    $tarikh = Tarikh::now();
    expect($tarikh->format('Y-m-d H:i:s'))->toBe('2024-01-01 12:00:00');

    Tarikh::setTestNow(null);
});

it('can add and sub units', function () {
    $tarikh = Tarikh::parse('2024-01-01 12:00:00');
    
    $added = $tarikh->add(1, Unit::Day);
    expect($added->format('Y-m-d H:i:s'))->toBe('2024-01-02 12:00:00');
    
    $subbed = $tarikh->sub(1, Unit::Month);
    expect($subbed->format('Y-m-d H:i:s'))->toBe('2023-12-01 12:00:00');
});

it('can use sugar modifier methods', function () {
    $tarikh = Tarikh::parse('2024-01-01 12:00:00');
    
    $added = $tarikh->addDays(2);
    expect($added->format('Y-m-d H:i:s'))->toBe('2024-01-03 12:00:00');
    
    $subbed = $tarikh->subMonths(1);
    expect($subbed->format('Y-m-d H:i:s'))->toBe('2023-12-01 12:00:00');
});

it('can get start and end of unit', function () {
    $tarikh = Tarikh::parse('2024-05-15 14:30:45');
    
    $startOfMonth = $tarikh->startOf(Unit::Month);
    expect($startOfMonth->format('Y-m-d H:i:s'))->toBe('2024-05-01 00:00:00');
    
    $endOfMonth = $tarikh->endOf(Unit::Month);
    expect($endOfMonth->format('Y-m-d H:i:s'))->toBe('2024-05-31 23:59:59');
});

it('can compare dates', function () {
    $t1 = Tarikh::parse('2024-01-01');
    $t2 = Tarikh::parse('2024-01-02');
    
    expect($t1->isBefore($t2))->toBeTrue();
    expect($t2->isAfter($t1))->toBeTrue();
    expect($t1->equalTo($t1))->toBeTrue();
    
    expect($t1->isSameMonth($t2))->toBeTrue();
    expect($t1->isSameYear($t2))->toBeTrue();
});

it('can calculate differences', function () {
    $t1 = Tarikh::parse('2024-01-01 12:00:00');
    $t2 = Tarikh::parse('2024-01-02 12:00:00');
    
    expect($t1->diffInDays($t2))->toBe(1.0);
    expect($t1->diffInHours($t2))->toBe(24.0);
});

it('can format outputs', function () {
    $tarikh = Tarikh::parse('2024-01-01 12:30:45');
    
    expect($tarikh->toDateString())->toBe('2024-01-01');
    expect($tarikh->toTimeString())->toBe('12:30:45');
    expect($tarikh->toDateTimeString())->toBe('2024-01-01 12:30:45');
});

it('can explain itself', function () {
    $tarikh = Tarikh::parse('2024-01-01 12:00:00', 'UTC');
    $explain = $tarikh->explain();
    
    expect($explain['datetime'])->toBe('2024-01-01 12:00:00');
    expect($explain['timezone'])->toBe('UTC');
});
