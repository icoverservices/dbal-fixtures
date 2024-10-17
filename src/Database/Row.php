<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
 namespace ComPHPPuebla\Fixtures\Database;

use DateTime;

class Row
{
    /** @var string */
    private string $primaryKeyColumn;

    /** @var string */
    private string $identifier;

    /** @var array $values */
    private array $values;

    public function __construct(string $primaryKeyColumn, string $identifier, array $values)
    {
        $this->primaryKeyColumn = $primaryKeyColumn;
        $this->identifier = $identifier;
        $this->values = $values;
    }

    /**
     * It will skip the assignment if there's already a value for the primary key column
     */
    public function assignId(int $id): void
    {
        if (isset($this->values[$this->primaryKeyColumn])) {
            return; // This is not an auto-generated key
        }

        $this->values[$this->primaryKeyColumn] = $id;
    }

    /**
     * @return mixed Most common types are: int (auto_increment) and string (uuid)
     */
    public function id(): mixed
    {
        return $this->values[$this->primaryKeyColumn];
    }

    public function values(): array
    {
        return $this->values;
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function processDynamicDatesIfNeeded($value)
    {
        if (str_contains($value, 'FIXTURE_DATE_NOW')) {
            $intervalSpec = str_replace('FIXTURE_DATE_NOW', '', $value);
            $date = new DateTime();
            $date->modify(trim($intervalSpec));
            $value = $date->format('Y-m-d H:i:s');
        }

        return $value;
    }

    public function changeColumnValue(string $column, $value): void
    {
        $value = $this->processDynamicDatesIfNeeded($value);
        $this->values[$column] = $value;
    }

    /**
     * It will return `null` if the column does not exist
     */
    public function valueOf($column)
    {
        return $this->values[$column] ?? null;
    }

    public function columns(): array
    {
        return array_keys($this->values);
    }

    public function placeholders(): array
    {
        $placeholders = [];
        foreach ($this->values as $column => $value) {
            if (is_array($value) || is_numeric($value) || trim($value ?? '', '`') === $value) {
                $placeholders[$column] = '?';
            } else {
                $placeholders[$column] = $value === null ? 'null' : trim($value, '`');
            }
        }
        return $placeholders;
    }
}
