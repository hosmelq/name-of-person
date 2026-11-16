---
title: "Eloquent Casts"
description: "Read and write a model's first and last name columns as one PersonName attribute with an Eloquent cast."
weight: 3
---

`PersonNameCast` gives a model one name attribute backed by its `first_name` and `last_name` columns:

```php
use HosmelQ\NameOfPerson\PersonNameCast;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'name' => PersonNameCast::class,
        ];
    }
}
```

The attribute needs no column of its own. Reading it returns a `PersonName`, or `null` when the first name column is empty:

```php
$user->name->familiar(); // "David H."
```

The cast builds the name from the columns each time you read it, so it reflects changes you make to `first_name` or `last_name` directly.

## Assigning names

Assign a full name, a `PersonName`, or `null`:

```php
use HosmelQ\NameOfPerson\PersonName;

$user->name = 'Jason Fried'; // first_name "Jason", last_name "Fried"
$user->name = new PersonName('Jason', 'Fried');
$user->name = null; // Clears both columns
```

Strings split the way [`fromFull()`](creating-names#from-a-full-name) splits them, and a blank string clears both columns. Other values throw `InvalidArgumentException`.

## Custom columns

Pass the first and last name columns to `using()`:

```php
protected function casts(): array
{
    return [
        'author' => PersonNameCast::using('author_first_name', 'author_last_name'),
    ];
}
```

## Arrays and JSON

A model's array and JSON forms include the name only when you append it:

```php
class User extends Model
{
    protected $appends = ['name'];

    // ...
}

$user->toJson(); // {"first_name":"David","last_name":"Heinemeier Hansson","name":"David Heinemeier Hansson"}
```
