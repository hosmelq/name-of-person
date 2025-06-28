<?php

declare(strict_types=1);

use HosmelQ\NameOfPerson\PersonName;
use HosmelQ\NameOfPerson\PersonNameCast;
use Illuminate\Database\Eloquent\Model;

it('builds cast configuration string via using method', function (): void {
    $string = PersonNameCast::using('custom_first', 'custom_last');

    expect($string)->toBe(PersonNameCast::class.':custom_first,custom_last');
});

describe('Casting from database to object', function (): void {
    it('creates PersonName from default columns', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};

        $name = $caster->get(
            $model,
            'name',
            null,
            ['first_name' => 'Foo', 'last_name' => 'Bar']
        );

        expect($name)
            ->toBeInstanceOf(PersonName::class)
            ->first->toBe('Foo')
            ->last->toBe('Bar')
            ->full()->toBe('Foo Bar');
    });

    it('creates PersonName from custom columns', function (): void {
        $caster = PersonNameCast::castUsing(['given_name', 'family_name']);
        $model = new class () extends Model {};

        $name = $caster->get(
            $model,
            'name',
            null,
            ['given_name' => 'Will', 'family_name' => 'St. Clair']
        );

        expect($name)
            ->toBeInstanceOf(PersonName::class)
            ->first->toBe('Will')
            ->last->toBe('St. Clair');
    });

    it('returns null when first name is missing', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};

        $nullResult = $caster->get($model, 'name', null, ['last_name' => 'Bar']);
        $emptyResult = $caster->get($model, 'name', null, ['first_name' => '', 'last_name' => 'Bar']);

        expect($nullResult)->toBeNull()
            ->and($emptyResult)->toBeNull();
    });
});

describe('Casting from object to database', function (): void {
    it('stores PersonName object to default columns', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};
        $name = new PersonName('Jason', 'Fried');

        $result = $caster->set($model, 'name', $name, []);

        expect($result)->toBe([
            'first_name' => 'Jason',
            'last_name' => 'Fried',
        ]);
    });

    it('stores string values to default columns', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};

        $fullName = $caster->set($model, 'name', 'Will St. Clair', []);
        $singleName = $caster->set($model, 'name', 'Will', []);
        $nullValue = $caster->set($model, 'name', null, []);
        $emptyValue = $caster->set($model, 'name', '', []);

        expect($fullName)->toBe(['first_name' => 'Will', 'last_name' => 'St. Clair'])
            ->and($singleName)->toBe(['first_name' => 'Will', 'last_name' => null])
            ->and($nullValue)->toBe(['first_name' => null, 'last_name' => null])
            ->and($emptyValue)->toBe(['first_name' => null, 'last_name' => null]);
    });

    it('stores PersonName object to custom columns', function (): void {
        $caster = PersonNameCast::castUsing(['given_name', 'family_name']);
        $model = new class () extends Model {};
        $name = new PersonName('Foo', 'Bar');

        $result = $caster->set($model, 'name', $name, []);

        expect($result)->toBe([
            'given_name' => 'Foo',
            'family_name' => 'Bar',
        ]);
    });

    it('throws exception when storing invalid type', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};

        $caster->set($model, 'name', 123, []);
    })->throws(InvalidArgumentException::class, 'Value must be null, string, or PersonName instance, got: int');

    it('preserves data through round trip conversion', function (): void {
        $caster = PersonNameCast::castUsing([]);
        $model = new class () extends Model {};
        $originalName = new PersonName('Foo', 'Bar');

        $dbData = $caster->set($model, 'name', $originalName, []);
        $retrievedName = $caster->get($model, 'name', null, $dbData);

        expect($retrievedName)
            ->toBeInstanceOf(PersonName::class)
            ->first->toBe($originalName->first)
            ->last->toBe($originalName->last)
            ->full()->toBe($originalName->full());
    });
});

describe('Model integration', function (): void {
    it('retrieves PersonName via model attribute accessor', function (): void {
        $model = new class () extends Model {
            protected $attributes = [
                'first_name' => 'David',
                'last_name' => 'Heinemeier Hansson',
            ];

            protected function casts(): array
            {
                return [
                    'name' => PersonNameCast::class,
                ];
            }
        };

        expect($model->name)
            ->toBeInstanceOf(PersonName::class)
            ->full()->toBe('David Heinemeier Hansson');
    });

    it('stores PersonName via model attribute mutator', function (): void {
        $model = new class () extends Model {
            protected function casts(): array
            {
                return [
                    'name' => PersonNameCast::class,
                ];
            }
        };

        $model->name = 'Jason Fried';

        expect($model)
            ->first_name->toBe('Jason')
            ->last_name->toBe('Fried');
    });

    it('handles null assignment via model mutator', function (): void {
        $model = new class () extends Model {
            protected function casts(): array
            {
                return [
                    'name' => PersonNameCast::class,
                ];
            }
        };

        $model->name = null;

        expect($model)
            ->first_name->toBeNull()
            ->last_name->toBeNull();
    });
});
