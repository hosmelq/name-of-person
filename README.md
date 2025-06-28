# Name of Person

> Inspired by Basecamp's [name_of_person](https://github.com/basecamp/name_of_person) Ruby gem, but built for modern PHP applications.

## Introduction

Handle person names in your PHP applications with elegant formatting options. Transform names between multiple presentation formats. This package provides a clean, type-safe way to parse, store, manipulate, and display person names consistently across your application.

**Key Features:**
- 🎯 **Multiple Format Options**: nine different ways to display names (full, familiar, abbreviated, initials, sorted, possessive, mentionable)
- 🎨 **Smart Parsing**: Intelligently handles full name strings and edge cases
- 🌍 **Unicode Support**: Full international name support with proper multibyte handling
- 🔧 **Pure PHP**: Core functionality works in any PHP project
- ⚡ **Laravel Integration**: Native Eloquent casting for seamless database integration

## Requirements

This package requires PHP 8.2 or higher. Laravel integration requires Laravel 11 or higher.

## Installation

You can install the package via composer:

```bash
composer require hosmelq/name-of-person
```

## Basic Usage

### Creating PersonName Objects

The core `PersonName` class works in any PHP application:

```php
use HosmelQ\NameOfPerson\PersonName;

// Direct instantiation with first and last name.
$name = new PersonName('David', 'Heinemeier Hansson');

// From full name strings.
$parsed = PersonName::fromFull('Jason Fried');

echo $parsed->first; // "Jason"
echo $parsed->last;  // "Fried"

// Handles single names.
$single = PersonName::fromFull('Cher');

echo $single->first; // "Cher"
echo $single->last;  // null
```

### Available Methods

#### Basic Properties

##### `first` (readonly property)
Returns the first name as a string.

```php
$name = new PersonName('David', 'Heinemeier Hansson');

echo $name->first; // "David"
```

##### `last` (readonly property)
Returns the last name as a string, or `null` if no last name was provided.

```php
$name = new PersonName('David', 'Heinemeier Hansson');

echo $name->last; // "Heinemeier Hansson"

$single = PersonName::fromFull('Cher');

echo $single->last; // null
```

#### Display Methods

```php
$name = new PersonName('David', 'Heinemeier Hansson');

$single = PersonName::fromFull('Cher');
```

##### `abbreviated()`
Returns the first initial plus full last name: "F. Last"

```php
echo $name->abbreviated();   // "D. Heinemeier Hansson"
echo $single->abbreviated(); // "Cher"
```

##### `familiar()`
Returns the first name plus last initial with a period: "First L."

```php
echo $name->familiar();   // "David H."
echo $single->familiar(); // "Cher"
```

##### `full()`
Returns the complete name in "First Last" format.

```php
echo $name->full(); // "David Heinemeier Hansson"
```

##### `initials()`
Returns all initials from the name, excluding parentheses and brackets.

```php
echo $name->initials(); // "DHH"

$complex = new PersonName('Mary Jane', 'Watson');

echo $complex->initials(); // "MJW"
```

##### `mentionable()`
Returns lowercase, space-free version of the familiar name for mentions.

```php
echo $name->mentionable();   // "davidh"
echo $single->mentionable(); // "cher"
```

##### `possessive()`
Returns the possessive form of the name with appropriate apostrophe placement.

```php
echo $name->possessive(); // "David Heinemeier Hansson's"

$james = new PersonName('James', null);

echo $james->possessive(); // "James'"
```

You can also specify which format to make possessive:

```php
echo $name->possessive('first');       // "David's"
echo $name->possessive('familiar');    // "David H.'s"
echo $name->possessive('abbreviated'); // "D. Heinemeier Hansson's"
echo $name->possessive('initials');    // "DHH's"
echo $name->possessive('sorted');      // "Heinemeier Hansson, David's"
```

##### `sorted()`
Returns the name in "Last, First" format suitable for sorting.

```php
echo $name->sorted();   // "Heinemeier Hansson, David"
echo $single->sorted(); // "Cher"
```

#### Utility Methods

##### `equals()`
Compares two PersonName objects for equality.

```php
$name1 = new PersonName('David', 'Heinemeier Hansson');
$name2 = new PersonName('David', 'Heinemeier Hansson');
$name3 = new PersonName('Jason', 'Fried');

echo $name1->equals($name2); // true
echo $name1->equals($name3); // false
```

#### String and JSON Conversion

PersonName implements both `Stringable` and `JsonSerializable` interfaces:

```php
$name = new PersonName('David', 'Heinemeier Hansson');

// String conversion returns full name
echo (string) $name; // "David Heinemeier Hansson"

// JSON serialization returns full name
echo json_encode($name); // "David Heinemeier Hansson"
```

> **Performance Note**: All computed properties (familiar, abbreviated, sorted, etc.) are cached for performance. The first call computes the value, later calls return the cached result.

### Error Handling

The package throws `InvalidArgumentException` in these cases:

```php
// Empty first name
new PersonName(''); // throws InvalidArgumentException

// Invalid possessive method
$name->possessive('invalid'); // throws InvalidArgumentException
```

### Unicode Support

The package fully supports international names:

```php
$name = new PersonName('José', 'García');

echo $name->familiar(); // "José G."
```

## Laravel Integration

When using Laravel, you can leverage the included cast for seamless Eloquent integration:

### Configuration

The package works out of the box with Laravel's casting system. By default, it expects `first_name` and `last_name` columns in your database, but you can customize this:

```php
use HosmelQ\NameOfPerson\PersonNameCast;

// Default configuration - uses first_name and last_name columns
class User extends Model
{
    protected function casts(): array
    {
        return [
            'name' => PersonNameCast::class,
        ];
    }
}

// Custom column names
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'author_name' => PersonNameCast::class.':author_first,author_last',
        ];
    }
}
```

Alternatively, you can use the fluent helper method:

```php
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'author_name' => PersonNameCast::using('author_first', 'author_last'),
        ];
    }
}
```

#### Usage Examples

#### Basic Usage

```php
$user = new User();

$user->name = 'David Heinemeier Hansson';

echo $user->name->familiar(); // "David H."
```

#### JSON Serialization

```php
$user = User::find(1);

return response()->json([
    'user' => $user->name, // "David Heinemeier Hansson"
]);
```

## Testing

```bash
composer test
```

## Credits

- [Hosmel Quintana](https://github.com/hosmelq)
- Inspired by [Basecamp's name_of_person](https://github.com/basecamp/name_of_person) Ruby gem
- All Contributors

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
