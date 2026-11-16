---
title: "Creating Names"
description: "Create a PersonName from a first and last name or from a full name, then compare it or print it."
weight: 1
---

A `PersonName` holds a first name and an optional last name in read-only properties:

```php
use HosmelQ\NameOfPerson\PersonName;

$name = new PersonName('David', 'Heinemeier Hansson');

$name->first; // "David"
$name->last; // "Heinemeier Hansson"
```

The constructor trims both parts. A missing or blank last name becomes `null`:

```php
$name = new PersonName('Cher');

$name->last; // null
```

A blank first name throws `InvalidArgumentException`.

## From a full name

`fromFull()` splits a full name at its first space: the first word becomes the first name, and the rest becomes the last name. Runs of whitespace collapse to a single space:

```php
$name = PersonName::fromFull('  Will   St. Clair  ');

$name->first; // "Will"
$name->last; // "St. Clair"
```

It returns `null` for an empty or blank string instead of throwing:

```php
PersonName::fromFull(''); // null
```

A first name with more than one word, such as "Mary Jane Watson", splits into "Mary" and "Jane Watson". Pass the parts to the constructor when you know them.

## Comparing names

`equals()` compares the first and last names:

```php
$name->equals(new PersonName('Will', 'St. Clair')); // true
```

## Strings and JSON

A `PersonName` converts to its full name in strings and in JSON:

```php
echo $name; // Will St. Clair

json_encode(['author' => $name]); // {"author":"Will St. Clair"}
```
