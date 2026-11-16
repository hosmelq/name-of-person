---
title: "Formatting Names"
description: "Format a name as a full, familiar, abbreviated, or sorted name, as initials or a mention handle, or in its possessive form."
weight: 2
---

Each format is a method that returns a string. Without a last name, the full, familiar, abbreviated, and sorted forms are all the first name:

| Method | David Heinemeier Hansson | Cher |
| --- | --- | --- |
| `full()` | David Heinemeier Hansson | Cher |
| `familiar()` | David H. | Cher |
| `abbreviated()` | D. Heinemeier Hansson | Cher |
| `sorted()` | Heinemeier Hansson, David | Cher |
| `initials()` | DHH | C |
| `mentionable()` | davidh | cher |
| `possessive()` | David Heinemeier Hansson's | Cher's |

`familiar()` shortens the last name to its initial, and `abbreviated()` shortens the first name instead. `sorted()` puts the last name first, for lists ordered by last name.

## Initials

`initials()` takes the first letter of each word, and skips text in parentheses or square brackets, such as a company name:

```php
use HosmelQ\NameOfPerson\PersonName;

new PersonName('Conor', 'Muirhead [Basecamp]')->initials(); // "CM"
new PersonName('Mary Jane', 'Watson')->initials(); // "MJW"
```

## Mention handles

`mentionable()` lowercases the familiar name and removes its spaces and final period, for @-mentions:

```php
new PersonName('David', 'Heinemeier Hansson')->mentionable(); // "davidh"
```

## Possessives

`possessive()` adds `'s` to the full name, or only an apostrophe when the name ends in "s":

```php
new PersonName('David', 'Heinemeier Hansson')->possessive(); // "David Heinemeier Hansson's"
new PersonName('James')->possessive(); // "James'"
```

To make another form possessive, pass `full`, `first`, `last`, `abbreviated`, `sorted`, or `initials`:

```php
$name = new PersonName('David', 'Heinemeier Hansson');

$name->possessive('first'); // "David's"
$name->possessive('abbreviated'); // "D. Heinemeier Hansson's"
$name->possessive('initials'); // "DHH's"
```

Without a last name, `last` uses the first name. Any other value throws `InvalidArgumentException`.
