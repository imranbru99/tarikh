# ⏱️ Tarikh — Modern, Immutable DateTime Toolkit for PHP & Laravel

<p align="center">
  <a href="https://packagist.org/packages/imrandevbd/tarikh"><img src="https://img.shields.io/packagist/v/imrandevbd/tarikh.svg?style=flat-square&color=blue" alt="Latest Version on Packagist"></a>
  <a href="https://packagist.org/packages/imrandevbd/tarikh"><img src="https://img.shields.io/packagist/dt/imrandevbd/tarikh.svg?style=flat-square&color=green" alt="Total Downloads"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-^8.3-777bb4.svg?style=flat-square&logo=php" alt="PHP Version"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x%20|%2011.x-ff2d20.svg?style=flat-square&logo=laravel" alt="Laravel Support"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
</p>

---

**Tarikh** (*تاریخ / তারিখ*) is an ultra-fast, modern, and immutable DateTime toolkit engineered from the ground up for **PHP 8.3+** and **Laravel**. Designed as an elegant, lightweight alternative to legacy date libraries, Tarikh guarantees zero unwanted side-effects through strict immutability, microsecond precision, macroable extensibility, and seamless developer ergonomics.

---

## ⚡ Highlights & Key Features

- 🔒 **100% Immutable by Design:** Every modification returns a pristine new instance. Say goodbye to accidental mutation bugs in loops and queries.
- 🚀 **Built for Modern PHP 8.3+:** Type-safe backed enums (`Unit`, `Month`, `Weekday`), strict types, and zero legacy bloat.
- 🧪 **Time Travel & Testing Mock:** Built-in `setTestNow()` for deterministic testing across suites.
- 🎯 **Fluent Sugar Modifiers:** Intuitive arithmetic (`addDays()`, `subMonths()`, `addHours()`, etc.).
- 📐 **Boundary Precision:** Precise `startOf()` and `endOf()` boundaries down to the microsecond (`Second`, `Minute`, `Hour`, `Day`, `Week`, `Month`, `Quarter`, `Year`).
- ⚖️ **Rich Comparisons:** `isBefore()`, `isAfter()`, `isBetween()`, `isWeekend()`, `isLeapYear()`, and more.
- 📊 **Detailed Diff Engine:** High-precision difference metrics (`diffInSeconds`, `diffInMinutes`, `diffInHours`, `diffInDays`, `diffInWeeks`, `diffInMonths`, `diffInYears`).
- 🔌 **Macroable & Extensible:** Add custom domain logic or regional calendar rules at runtime via `Tarikh::macro()` and `Tarikh::mixin()`.
- 📦 **First-Class Laravel Integration:** Auto-discovered Service Provider and Facade ready out of the box.

---

## 📥 Installation

Install via Composer:

```bash
composer require imrandevbd/tarikh
```

*Requirements: PHP 8.3 or higher with the `ext-intl` extension.*

---

## 🚀 Quick Start

```php
use Tarikh\Tarikh;
use Tarikh\Enums\Unit;

// Create instances
$now = Tarikh::now();
$today = Tarikh::today('Asia/Dhaka');
$event = Tarikh::parse('2025-12-31 23:59:59', 'UTC');

// Immutable arithmetic
$nextWeek = $now->addWeeks(1);
$prevQuarter = $now->sub(1, Unit::Quarter);

// Boundaries
$monthStart = $now->startOf(Unit::Month); // YYYY-MM-01 00:00:00
$monthEnd   = $now->endOf(Unit::Month);   // YYYY-MM-31 23:59:59.999999

// Human-friendly formats
echo $now->toDateString();     // "2025-05-15"
echo $now->toDateTimeString(); // "2025-05-15 14:30:00"
echo $now->toIso8601String();  // "2025-05-15T14:30:00+00:00"
```

---

## 📖 Complete Feature Guide

### 1. Creation & Parsing

```php
use Tarikh\Tarikh;

// Current timestamp
$now = Tarikh::now();
$nowDhaka = Tarikh::now('Asia/Dhaka');

// Start of today, tomorrow, yesterday
$today     = Tarikh::today();
$tomorrow  = Tarikh::tomorrow();
$yesterday = Tarikh::yesterday();

// Parse strings or DateTimeInterface
$date = Tarikh::parse('2025-06-15 18:30:00', 'America/New_York');

// Explicit constructor
$created = Tarikh::create(year: 2025, month: 12, day: 25, hour: 10, minute: 0, second: 0, tz: 'UTC');

// From UNIX timestamp
$fromEpoch = Tarikh::fromTimestamp(1735689600);
```

---

### 2. Time Travel & Testing (`setTestNow`)

Freeze or shift time during unit tests effortlessly:

```php
use Tarikh\Tarikh;

// Freeze time for tests
Tarikh::setTestNow(Tarikh::parse('2024-01-01 12:00:00'));

echo Tarikh::now()->toDateTimeString(); // "2024-01-01 12:00:00"
echo Tarikh::today()->toDateTimeString(); // "2024-01-01 00:00:00"

// Reset back to system time
Tarikh::setTestNow(null);
```

---

### 3. Date Arithmetic & Modifiers

All operations return a **new instance** without mutating the original object:

```php
use Tarikh\Tarikh;
use Tarikh\Enums\Unit;

$tarikh = Tarikh::parse('2025-01-15 12:00:00');

// Enums-based manipulation
$modified = $tarikh
    ->add(2, Unit::Day)
    ->sub(1, Unit::Month)
    ->add(3, Unit::Hour);

// Convenient fluent modifiers
$tarikh->addDays(5);
$tarikh->subDays(2);
$tarikh->addWeeks(3);
$tarikh->subWeeks(1);
$tarikh->addMonths(6);
$tarikh->subMonths(2);
$tarikh->addYears(1);
$tarikh->subYears(5);
$tarikh->addHours(4);
$tarikh->subHours(2);
$tarikh->addMinutes(30);
$tarikh->subMinutes(15);
$tarikh->addSeconds(45);
$tarikh->subSeconds(10);
```

---

### 4. Boundary Calculation (`startOf` & `endOf`)

Accurately calculate periods down to the microsecond:

```php
use Tarikh\Tarikh;
use Tarikh\Enums\Unit;

$date = Tarikh::parse('2025-05-15 14:30:45');

// Start of periods
$startOfDay     = $date->startOf(Unit::Day);     // 2025-05-15 00:00:00
$startOfWeek    = $date->startOf(Unit::Week);    // Monday of the week 00:00:00
$startOfMonth   = $date->startOf(Unit::Month);   // 2025-05-01 00:00:00
$startOfQuarter = $date->startOf(Unit::Quarter); // 2025-04-01 00:00:00
$startOfYear   = $date->startOf(Unit::Year);    // 2025-01-01 00:00:00

// End of periods (with microsecond precision: 23:59:59.999999)
$endOfDay   = $date->endOf(Unit::Day);
$endOfWeek  = $date->endOf(Unit::Week);          // Sunday 23:59:59.999999
$endOfMonth = $date->endOf(Unit::Month);
$endOfYear  = $date->endOf(Unit::Year);
```

---

### 5. Comparisons & State Checks

```php
use Tarikh\Tarikh;

$t1 = Tarikh::parse('2025-01-01');
$t2 = Tarikh::parse('2025-01-15');
$t3 = Tarikh::parse('2025-02-01');

// Relative comparisons
$t1->isBefore($t2);              // true
$t2->isAfter($t1);               // true
$t1->equalTo($t1);               // true
$t2->isBetween($t1, $t3);        // true

// Same unit checks
$t1->isSameDay($t2);             // false
$t1->isSameMonth($t2);           // true
$t1->isSameYear($t3);            // true

// State predicates
$t1->isWeekend();                // true/false
$t1->isWeekday();                // true/false
$t1->isToday();                  // true/false
$t1->isFuture();                 // true/false
$t1->isPast();                   // true/false
$t1->isLeapYear();               // true/false
```

---

### 6. Differences & Intervals

Calculate precise differences between two dates:

```php
use Tarikh\Tarikh;

$start = Tarikh::parse('2025-01-01 00:00:00');
$end   = Tarikh::parse('2025-01-05 12:00:00');

$start->diffInSeconds($end); // 388800
$start->diffInMinutes($end); // 6480.0
$start->diffInHours($end);   // 108.0
$start->diffInDays($end);    // 4.5
$start->diffInWeeks($end);   // ~0.64
$start->diffInMonths($end);  // Float month difference
$start->diffInYears($end);   // Float year difference
```

---

### 7. Formatting & JSON Serialization

```php
$tarikh = Tarikh::parse('2025-07-20 18:30:45', 'Asia/Tokyo');

// Standard outputs
$tarikh->toDateString();       // "2025-07-20"
$tarikh->toTimeString();       // "18:30:45"
$tarikh->toDateTimeString();   // "2025-07-20 18:30:45"
$tarikh->toIso8601String();    // "2025-07-20T18:30:45+09:00"
$tarikh->toRfc3339String();    // "2025-07-20T18:30:45+09:00"
$tarikh->format('l, F j, Y');  // "Sunday, July 20, 2025"

// Debug inspection
$tarikh->explain();
// [
//   'datetime'  => '2025-07-20 18:30:45',
//   'timezone'  => 'Asia/Tokyo',
//   'timestamp' => 1753003845,
// ]

// Automatic JSON serialization (implements JsonSerializable)
json_encode(['event_at' => $tarikh]);
```

---

### 8. Macroable & Custom Extensibility

Extend Tarikh at runtime using the `Macroable` trait:

```php
use Tarikh\Tarikh;

// Define a macro
Tarikh::macro('isIndependenceDay', function () {
    return $this->format('m-d') === '03-26';
});

$day = Tarikh::parse('2025-03-26');
echo $day->isIndependenceDay(); // true
```

---

### 9. Laravel Integration

Tarikh automatically hooks into Laravel. If you prefer using the Facade:

```php
use Tarikh\Laravel\Facades\Tarikh;

$now = Tarikh::now();
$deadline = Tarikh::parse($request->input('due_date'))->endOfDay();
```

---

## 🧪 Testing

Run test suite via Pest:

```bash
composer test
```

Or run static analysis and code styling:

```bash
vendor/bin/phpstan analyse
vendor/bin/pint --test
```

---

## 🤝 Let's Connect & Collaborate

I am open to **Senior Remote Full-Stack Roles**, **AI Platform Architecture Contracts**, and **Enterprise Technical Advisory**.

- **Timezone:** UTC+6 (Dhaka / Rangpur, Bangladesh) — Flexible overlap with US, UK, and European business hours.
- **Delivery Mode:** Async-ready, Slack, Discord, Jira, GitHub, and production-first accountability.

| Channel | Address / Handle | Quick Action |
| :--- | :--- | :--- |
| 🌐 **Primary Portfolio** | [imrandev.bd](https://imrandev.bd/) | [Visit Site ↗](https://imrandev.bd/) |
| 📦 **Packagist Packages** | [packagist.org/packages/imrandevbd/](https://packagist.org/packages/imrandevbd/) | [View Packages ↗](https://packagist.org/packages/imrandevbd/) |
| 💼 **LinkedIn Profile** | [linkedin.com/in/imranbru99](https://linkedin.com/in/imranbru99) | [Connect ↗](https://linkedin.com/in/imranbru99) |
| 🐙 **GitHub Profile** | [github.com/imranbru99](https://github.com/imranbru99) | [Follow ↗](https://github.com/imranbru99) |
| 💬 **WhatsApp Direct** | [+880 1576-918420](http://wa.me/+8801576918420) | [Chat Now ↗](http://wa.me/+8801576918420) |
| 📧 **Direct Email** | [me@imrandev.bd](mailto:me@imrandev.bd) | [Send Email ↗](mailto:me@imrandev.bd) |
| 🐦 **X (Twitter)** | [@imrandev_bd](https://x.com/imrandev_bd) | [Follow ↗](https://x.com/imrandev_bd) |
| 📺 **YouTube Tech** | [@ImranDevBD](https://youtube.com/@ImranDevBD) | [Subscribe ↗](https://youtube.com/@ImranDevBD) |

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
