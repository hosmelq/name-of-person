<?php

declare(strict_types=1);

namespace HosmelQ\NameOfPerson;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PersonNameCast implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param string[] $arguments
     *
     * @return CastsAttributes<null|PersonName, null|string|PersonName>
     */
    public static function castUsing(array $arguments): CastsAttributes // @phpstan-ignore-line - @see https://github.com/laravel/framework/pull/56177
    {
        /** @var array{null|string, null|string} $config */
        $config = array_pad(array_map(trim(...), array_values($arguments)), 2, null);

        return new class ($config[0] ?? 'first_name', $config[1] ?? 'last_name') implements CastsAttributes {
            public function __construct(protected string $firstNameColumn, protected string $lastNameColumn)
            {
            }

            /**
             * Cast the database value to PersonName.
             */
            public function get(Model $model, string $key, mixed $value, array $attributes): null|PersonName
            {
                /** @var null|string $firstName */
                $firstName = $attributes[$this->firstNameColumn] ?? null;
                /** @var null|string $lastName */
                $lastName = $attributes[$this->lastNameColumn] ?? null;

                if (is_null($firstName) || mb_trim($firstName) === '') {
                    return null;
                }

                return new PersonName($firstName, $lastName);
            }

            /**
             * Prepare the PersonName for database storage.
             *
             * @return non-empty-array<string, string|null>
             */
            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                if (is_null($value)) {
                    return [
                        $this->firstNameColumn => null,
                        $this->lastNameColumn => null,
                    ];
                }

                if (is_string($value)) {
                    $personName = PersonName::fromFull($value);

                    if (! $personName instanceof PersonName) {
                        return [
                            $this->firstNameColumn => null,
                            $this->lastNameColumn => null,
                        ];
                    }

                    return [
                        $this->firstNameColumn => $personName->first,
                        $this->lastNameColumn => $personName->last,
                    ];
                }

                if ($value instanceof PersonName) {
                    return [
                        $this->firstNameColumn => $value->first,
                        $this->lastNameColumn => $value->last,
                    ];
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'Value must be null, string, or PersonName instance, got: %s',
                        get_debug_type($value)
                    )
                );
            }
        };
    }

    /**
     * Specify custom column names for the cast.
     *
     * Usage: PersonNameCast::using('author_first', 'author_last')
     */
    public static function using(string $firstNameColumn, string $lastNameColumn): string
    {
        return static::class.':'.$firstNameColumn.','.$lastNameColumn;
    }
}
