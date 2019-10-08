<?php

namespace Stitch\DBAL\Syntax;

use Closure;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Schema\Table as TableSchema;
use Stitch\DBAL\Schema\Column as ColumnSchema;

class Select
{
    protected $crossDatabase;

    protected $crossTable;

    protected $methods;

    /**
     * Select constructor.
     */
    public function __construct()
    {
        $this->methods[] = Lexicon::in();
        $this->methods[] = Lexicon::notIn();
    }

    /**
     * @param TableBuilder $builder
     * @return $this
     */
    public function analyse(TableBuilder $builder)
    {
        $this->crossDatabase = $builder->crossDatabase();
        $this->crossTable = $builder->crossTable();

        return $this;
    }

    /**
     * @param $schema
     * @return array
     */
    protected function tablePathPieces(TableSchema $schema)
    {
        $pieces = [];

        if ($this->crossDatabase) {
            $pieces[] = $schema->getConnection()->getDatabase();
        }

        $pieces[] = $schema->getName();

        return $pieces;
    }

    /**
     * @param TableSchema $schema
     * @return string
     */
    public function tablePath(TableSchema $schema)
    {
        return Grammar::path(
            $this->tablePathPieces($schema)
        );
    }

    /**
     * @param TableSchema $schema
     * @return string
     */
    public function tableAlias(TableSchema $schema)
    {
        return Grammar::alias(
            $this->tablePathPieces($schema)
        );
    }

    /**
     * @param $schema
     * @return array
     */
    public function columnPathPieces(ColumnSchema $schema)
    {
        $pieces = $this->crossTable ? $this->tablePathPieces($schema->getTable()) : [];

        $pieces[] = $schema->getName();

        return $pieces;
    }

    /**
     * @param ColumnSchema $schema
     * @return string
     */
    public function columnPath(ColumnSchema $schema)
    {
        return Grammar::path(
            $this->columnPathPieces($schema)
        );
    }

    /**
     * @param ColumnSchema $schema
     * @return string
     */
    public function columnAlias(ColumnSchema $schema)
    {
        return Grammar::alias(
            $this->columnPathPieces($schema)
        );
    }

    /**
     * @param TableSchema $schema
     * @return string
     */
    public function primaryKeyAlias(TableSchema $schema)
    {
        return $this->columnAlias(
            $schema->getPrimaryKey()
        );
    }

    /**
     * @param TableSchema $schema
     * @return string
     */
    public function rowNumberColumn(TableSchema $schema)
    {
        return "{$this->tableAlias($schema)}_row_num";
    }

    /**
     * @return string
     */
    public function selectColumns(array $columns)
    {
        return $this->implode(
            Lexicon::select(),
            Grammar::list($columns)
        );
    }

    /**
     * @return string
     */
    public function selectAll()
    {
        return $this->implode(
            Lexicon::select(),
            Grammar::wildcard()
        );
    }

    /**
     * @return string
     */
    public function selectAllAnd()
    {
        return $this->selectAll() . Grammar::listDelimeter();
    }

    /**
     * @return string
     */
    public function selectSubquery()
    {
        return $this->implode(
            Lexicon::select(),
            Grammar::wildcard(),
            Lexicon::from()
        );
    }

    /**
     * @return string
     */
    public function from()
    {
        return Lexicon::from();
    }

    /**
     * @return string
     */
    public function where()
    {
        return Lexicon::where();
    }


    /**
     * @param string $type
     * @param TableSchema $table
     * @return string
     */
    public function join(string $type, TableSchema $table)
    {
        return $this->implode(
            $type,
            Lexicon::join(),
            $this->tablePath($table),
            Lexicon::on()
        );
    }

    /**
     * @param string $name
     * @return string
     */
    public function alias(string $name)
    {
        return $this->implode(
            Lexicon::alias(),
            $name
        );
    }

    /**
     * @param string $name
     * @return string
     */
    function variable(string $name)
    {
        return Grammar::variable() . $name;
    }

    /**
     * @param array $variables
     * @return string
     */
    public function list(array $variables)
    {
        return Grammar::list($variables);
    }

    /**
     * @param array $variables
     * @return string
     */
    public function setVariables(array $variables)
    {
        return $this->implode(
            Lexicon::set(),
            $this->list($variables) . Grammar::terminator()
        );
    }

    /**
     * @param mixed ...$arguments
     * @return string
     */
    public function ternary(...$arguments)
    {
        $arguments = array_map(function ($argument)
        {
            return $argument instanceof Closure ? $argument() : $argument;
        }, $arguments);

        return Grammar::ternary(...$arguments);
    }

    /**
     * @param $name
     * @param $value
     * @return string
     */
    public function assign($variable, $value)
    {
        switch (gettype($value)) {
            case 'NULL':
                $value = 'NULL';
        }

        return $this->implode(
            $variable,
            Grammar::assign(),
            $value
        );
    }

    /**
     * @param ColumnSchema $column
     * @param $operator
     * @param $value
     * @return string
     */
    public function condition(ColumnSchema $column, $operator, $value)
    {
        $pieces = [$this->columnPath($column)];

        switch (gettype($value)) {
            case 'array':
                if (in_array($operator, $this->methods)) {
                    $pieces[] = Grammar::method($operator, Grammar::placeholders($value));
                } else {
                    $pieces[] = $operator;
                    $pieces[] = implode(' ' . Lexicon::and() . ' ', $value);
                }
                break;

            case null:
                $pieces[] = $operator === Grammar::notEqual() ? Lexicon::notNull() : Lexicon::null();
                break;

            default:
                $pieces[] = $operator;
                $pieces[] = $value instanceOf ColumnSchema ? $this->columnPath($value) : Grammar::placeholder();
        }

        return $this->implode(...$pieces);
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public function equal($left, $right)
    {
        return $this->implode(
            $left,
            Grammar::equal(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public function lessThanOrEqual($left, $right)
    {
        return $this->implode(
            $left,
            Grammar::lessThanOrEqual(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public function greaterThan($left, $right)
    {
        return $this->implode(
            $left,
            Grammar::greaterThan(),
            $right
        );
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    public function add($left, $right)
    {
        return $this->implode(
            $left,
            Grammar::add(),
            $right
        );
    }

    /**
     * @param string $value
     * @return string
     */
    public function isolate(string $value)
    {
        return Grammar::parentheses($value);
    }

    /**
     * @return string
     */
    public function orderBy()
    {
        return Lexicon::orderBy();
    }

    /**
     * @param int $quantity
     * @return string
     */
    public function limit(int $quantity)
    {
        return $this->implode(
            Lexicon::limit(),
            $quantity
        );
    }

    /**
     * @param array ...$arguments
     * @return string
     */
    protected function implode(...$arguments)
    {
        return implode(' ', $arguments);
    }
}
