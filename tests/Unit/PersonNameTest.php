<?php

declare(strict_types=1);

use HosmelQ\NameOfPerson\PersonName;

it('requires first name', function (): void {
    new PersonName('');
})->throws(InvalidArgumentException::class, 'First name is required.');

it('allows last name to be omitted', function (): void {
    $name = new PersonName('Baz');

    expect($name)
        ->first->toBe('Baz')
        ->last->toBeNull();
});

it('returns correct full name', function (): void {
    $nameOnly = new PersonName('Baz');

    expect($nameOnly->full())->toBe('Baz');

    $nameWithLast = new PersonName('Foo', 'Bar');

    expect($nameWithLast->full())->toBe('Foo Bar');
});

it('returns correct abbreviations', function (): void {
    $name = new PersonName('Foo', 'Bar');

    expect($name)
        ->familiar()->toBe('Foo B.')
        ->abbreviated()->toBe('F. Bar')
        ->sorted()->toBe('Bar, Foo');

    $nameOnly = new PersonName('Baz');

    expect($nameOnly)
        ->familiar()->toBe('Baz')
        ->abbreviated()->toBe('Baz')
        ->sorted()->toBe('Baz');
});

it('returns correct possessive form', function (): void {
    $nameEndingInS = new PersonName('Foo', 'Bars');

    expect($nameEndingInS->possessive())->toBe("Foo Bars'");

    $regularName = new PersonName('Foo', 'Bar');

    expect($regularName->possessive())->toBe("Foo Bar's");
});

it('returns correct possessive with different formats', function (): void {
    $name = new PersonName('Foo', 'Bar');

    expect($name)
        ->possessive('first')->toBe("Foo's")
        ->possessive('last')->toBe("Bar's")
        ->possessive('abbreviated')->toBe("F. Bar's");
});

it('returns correct mentionable name', function (): void {
    $name = PersonName::fromFull('Foo Bar');

    expect($name->mentionable())->toBe('foob');
});

it('returns correct initials', function (): void {
    $complex = PersonName::fromFull('David Heinemeier Hansson');

    expect($complex->initials())->toBe('DHH');

    $simple = PersonName::fromFull('Foo Bar');

    expect($simple->initials())->toBe('FB');

    $withBrackets = PersonName::fromFull('Conor Muirhead [Basecamp]');

    expect($withBrackets->initials())->toBe('CM');
});

it('creates person from full name', function (): void {
    $person = PersonName::fromFull('Will St. Clair');

    expect($person)
        ->first->toBe('Will')
        ->last->toBe('St. Clair');

    $emptyPerson = PersonName::fromFull('');

    expect($emptyPerson)->toBeNull();
});

it('handles spaces in full names correctly', function (): void {
    $name = PersonName::fromFull('  Will   St. Clair  ');

    expect($name)
        ->first->toBe('Will')
        ->last->toBe('St. Clair')
        ->full()->toBe('Will St. Clair');
});

it('treats blank last name same as null', function (): void {
    $name = new PersonName('Baz', '');
    $nameWithNull = new PersonName('Baz');

    expect($name)
        ->full()->toBe($nameWithNull->full())
        ->last->toBeNull();
});

it('compares equality correctly', function (): void {
    $name1 = new PersonName('Foo', 'Bar');
    $name2 = new PersonName('Foo', 'Bar');
    $name3 = new PersonName('Will', 'Bar');

    expect($name1)
        ->equals($name2)->toBeTrue()
        ->equals($name3)->toBeFalse();
});

it('serializes to JSON correctly', function (): void {
    $name = new PersonName('Foo', 'Bar');
    $json = json_encode($name);

    expect(json_decode($json))->toBe('Foo Bar');
});

it('throws exception for invalid possessive format', function (): void {
    $name = new PersonName('Foo', 'Bar');

    $name->possessive('invalid');
})->throws(InvalidArgumentException::class);

describe('Unicode support', function (): void {
    it('handles basic Unicode names', function (): void {
        $chinese = new PersonName('李明', '王');

        expect($chinese->full())->toBe('李明 王');

        $spanish = new PersonName('José', 'García');

        expect($spanish->full())->toBe('José García');
    });

    it('handles Unicode abbreviations', function (): void {
        $name = new PersonName('José', 'García');

        expect($name)
            ->abbreviated()->toBe('J. García')
            ->familiar()->toBe('José G.');
    });

    it('handles Unicode possessive forms', function (): void {
        $spanish = new PersonName('José', 'García');

        expect($spanish->possessive())->toBe("José García's");
    });
});
