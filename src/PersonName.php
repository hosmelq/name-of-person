<?php

declare(strict_types=1);

namespace HosmelQ\NameOfPerson;

use function Safe\preg_match_all;
use function Safe\preg_replace;
use function Safe\preg_split;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class PersonName implements JsonSerializable, Stringable
{
    /**
     * The first name.
     */
    public readonly string $first;

    /**
     * The last name.
     */
    public readonly null|string $last;

    /**
     * Cached abbreviated name.
     */
    private null|string $abbreviated = null;

    /**
     * Cached familiar name.
     */
    private null|string $familiar = null;

    /**
     * Cached full name.
     */
    private null|string $full = null;

    /**
     * Cached initials.
     */
    private null|string $initials = null;

    /**
     * Cached mentionable name.
     */
    private null|string $mentionable = null;

    /**
     * Cached sorted name.
     */
    private null|string $sorted = null;

    /**
     * Create a new PersonName instance.
     */
    public function __construct(string $firstName, null|string $lastName = null)
    {
        if (mb_trim($firstName) === '') {
            throw new InvalidArgumentException('First name is required.');
        }

        $this->first = mb_trim($firstName);
        $this->last = (! is_null($lastName) && mb_trim($lastName) !== '') ? mb_trim($lastName) : null;
    }

    /**
     * Create a PersonName from a full name string.
     */
    public static function fromFull(string $fullName): null|self
    {
        if (mb_trim($fullName) === '') {
            return null;
        }

        /** @var array{string, null|string} $parts */
        $parts = preg_split('/\s+/u', preg_replace('/\s+/u', ' ', mb_trim($fullName)), 2, PREG_SPLIT_NO_EMPTY);

        return new self($parts[0], $parts[1] ?? null);
    }

    /**
     * Returns first initial + last, such as "J. Fried".
     */
    public function abbreviated(): string
    {
        return $this->abbreviated ??= is_null($this->last)
            ? $this->first
            : sprintf('%s. %s', mb_substr($this->first, 0, 1), $this->last);
    }

    /**
     * Check if two PersonName objects are equal.
     */
    public function equals(self $other): bool
    {
        return $this->first === $other->first && $this->last === $other->last;
    }

    /**
     * Returns first + last initial, such as "Jason F.".
     */
    public function familiar(): string
    {
        return $this->familiar ??= is_null($this->last)
            ? $this->first
            : sprintf('%s %s.', $this->first, mb_substr($this->last, 0, 1));
    }

    /**
     * Returns first + last, such as "Jason Fried".
     */
    public function full(): string
    {
        return $this->full ??= is_null($this->last)
            ? $this->first
            : sprintf('%s %s', $this->first, $this->last);
    }

    /**
     * Returns just the initials.
     */
    public function initials(): string
    {
        if (is_null($this->initials)) {
            $cleaned = preg_replace('/\([^)]*\)|\[[^]]*]/u', '', $this->full());

            preg_match_all('/\b(\w)\w*/u', $cleaned, $matches);

            $this->initials = implode('', $matches[1]);
        }

        return $this->initials;
    }

    /**
     * Returns a mentionable version of the familiar name.
     */
    public function mentionable(): string
    {
        if (is_null($this->mentionable)) {
            $familiar = $this->familiar();

            $withoutDot = is_null($this->last) ? $familiar : mb_substr($familiar, 0, -1);

            $this->mentionable = mb_strtolower(str_replace(' ', '', $withoutDot));
        }

        return $this->mentionable;
    }

    /**
     * Returns full name with trailing's or ' if name ends in s.
     */
    public function possessive(string $method = 'full'): string
    {
        $allowedMethods = ['full', 'first', 'last', 'abbreviated', 'sorted', 'initials'];

        if (! in_array($method, $allowedMethods, true)) {
            throw new InvalidArgumentException('Please provide a valid method');
        }

        $name = match ($method) {
            'abbreviated' => $this->abbreviated(),
            'first' => $this->first,
            'full' => $this->full(),
            'initials' => $this->initials(),
            'last' => $this->last ?? $this->first,
            'sorted' => $this->sorted(),
        };

        $suffix = mb_strtolower(mb_substr($name, -1)) === 's' ? "'" : "'s";

        return $name.$suffix;
    }

    /**
     * Returns last + first for sorting.
     */
    public function sorted(): string
    {
        return $this->sorted ??= is_null($this->last)
            ? $this->first
            : sprintf('%s, %s', $this->last, $this->first);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): string
    {
        return $this->full();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->full();
    }
}
