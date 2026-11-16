---
title: "Introduction"
description: "Name of Person turns a first and last name into the forms an application shows, from full and familiar names to initials and possessives."
weight: 1
---

Name of Person turns a first and last name into the forms an application shows: the full name, a familiar name such as "David H.", abbreviated and sorted names, initials, a mention handle, and possessives. It works in any PHP application, and its Eloquent cast reads and writes the name through a model's `first_name` and `last_name` columns.

## Quick start

After [installing the package](installation), create a name and format it:

```php
use HosmelQ\NameOfPerson\PersonName;

$name = new PersonName('David', 'Heinemeier Hansson');

$name->full(); // "David Heinemeier Hansson"
$name->familiar(); // "David H."
$name->initials(); // "DHH"
$name->possessive(); // "David Heinemeier Hansson's"
```

## Next steps

- [Creating Names](basic-usage/creating-names) builds names from their parts or from a full name.
- [Formatting Names](basic-usage/formatting-names) lists every format and what it returns.
- [Eloquent Casts](basic-usage/eloquent-casts) stores names in a model's first and last name columns.

## Credits

Name of Person is based on Basecamp's [name_of_person](https://github.com/basecamp/name_of_person) gem.
