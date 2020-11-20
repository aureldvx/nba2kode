<?php

namespace App\Utils;

class Database extends DatabaseInterface
{
    private ?string $sql;
    private ?array $sqlBuilder;

    public function __construct()
    {
        $this->sql = null;
        $this->sqlBuilder = null;
    }

    // SQL Select Option
    public function select()
    {
    }

    // SQL Insert Into Option
    public function insert()
    {
    }

    // SQL Update Option
    public function update()
    {
    }

    // SQL Delete Option
    public function delete()
    {
    }

    // Execute operation in specified table
    public function inTable()
    {
    }

    // Filter operation request
    public function where()
    {
    }

    // Add another mutual filter
    public function andWhere()
    {
    }

    // Add another choice filter
    public function orWhere()
    {
    }

    // Add results ordering
    public function orderBy()
    {
    }

    // Add another resultst ordering
    public function andOrderBy()
    {
    }

    // Limit results number
    public function limit()
    {
    }

    // Count results items
    public function count()
    {
    }

    // Transform request in SQL query
    public function getQuery()
    {
    }

    // Execute SQL Query
    public function getResult()
    {
        $this->connect();
    }
}
