# Upgrade Guide

## Upgrading to 1.0 from 0.3

### Runtime requirements

PHP 8.4 is now the minimum supported version, and the package requires the `mbstring` extension.

### Eloquent casts

Reading an attribute cast with `PersonNameCast` now returns a new `PersonName` each time, built from
the current column values. Compare names with `equals()` instead of `===`:

```php
$user->name->equals($other->name);
```

Before, Eloquent kept the first `PersonName` it read and wrote it back to the columns when saving,
which reverted changes made to `first_name` or `last_name` after that read.
