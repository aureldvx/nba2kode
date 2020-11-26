<?php

namespace App\Utils;

use Exception;
use PDO;

class QueryBuilder extends DatabaseInterface
{
    public string $sql;
    private ?array $sqlBuilder;
    private array $parameters;
    private array $pdoTypes = [
        PDO::PARAM_BOOL,
        PDO::PARAM_NULL,
        PDO::PARAM_INT,
        PDO::PARAM_STR,
    ];
    public array $bindedParameters = [];
    private array $permittedOperators = ['<', '>', '>=', '<=', 'LIKE', '!=', 'NOT LIKE', '='];
    private array $permittedOrders = ['ASC', 'DESC'];


    /**
     * @return $this|Exception
     */
    private function selectQuery()
    {
        $this->sql = $this->sqlBuilder['operation'];
        if (isset($this->sqlBuilder['items'])) {
            $this->sql .= ' ' . $this->sqlBuilder['items'];
        }
        if (isset($this->sqlBuilder['table'])) {
            $this->sql .= ' FROM ' . $this->sqlBuilder['table'];
        } else {
            return new Exception('Une table doit être renseignée pour exécuter la query.');
        }
        if (isset($this->sqlBuilder['where'])) {
            $this->sql .= ' ' . $this->sqlBuilder['where'];
        }
        if (isset($this->sqlBuilder['groupBy'])) {
            $this->sql .= ' ' . $this->sqlBuilder['groupBy'];
        }
        if (isset($this->sqlBuilder['order'])) {
            $this->sql .= ' ' . $this->sqlBuilder['order'];
        }
        if (isset($this->sqlBuilder['limit'])) {
            $this->sql .= ' ' . $this->sqlBuilder['limit'];
        }
        $this->sql .= ';';
        // Services::dump($this->sql);

        return $this;
    }


    /**
     * @return $this|Exception
     */
    private function updateQuery()
    {
        $this->sql = $this->sqlBuilder['operation'];
        if (isset($this->sqlBuilder['table'])) {
            $this->sql .= ' ' . $this->sqlBuilder['table'];
        } else {
            return new Exception('Une table doit être renseignée pour exécuter la query.');
        }
        if (isset($this->sqlBuilder['params'])) {
            $this->sql .= ' ' . $this->sqlBuilder['params'];
        } else {
            return new Exception('Des changements doivent êtres renseignés pour exécuter la query.');
        }
        if (isset($this->sqlBuilder['where'])) {
            $this->sql .= ' ' . $this->sqlBuilder['where'];
        }
        $this->sql .= ';';
        // Services::dump($this->sql);

        return $this;
    }


    /**
     * @return $this|Exception
     */
    private function insertQuery()
    {
        $this->sql = $this->sqlBuilder['operation'];
        if (isset($this->sqlBuilder['table'])) {
            $this->sql .= ' ' . $this->sqlBuilder['table'];
        } else {
            return new Exception('Une table doit être renseignée pour exécuter la query.');
        }
        if (isset($this->sqlBuilder['params'])) {
            $this->sql .= ' (';
            $keysIndex = 0;
            foreach ($this->sqlBuilder['params'] as $key => $value) {
                if (0 === $keysIndex) {
                    $this->sql .= $key;
                } else {
                    $this->sql .= ", $key";
                }
                ++$keysIndex;
            }
            $this->sql .= ') VALUES (';
            $valuesIndex = 0;
            foreach ($this->sqlBuilder['params'] as $key => $value) {
                if (0 === $valuesIndex) {
                    $this->sql .= $value;
                } else {
                    $this->sql .= ", $value";
                }
                ++$valuesIndex;
            }
            $this->sql .= ')';
        }
        $this->sql .= ';';
        // Services::dump($this->sql);

        return $this;
    }


    /**
     * @return $this|Exception
     */
    private function deleteQuery()
    {
        $this->sql = $this->sqlBuilder['operation'];
        if (isset($this->sqlBuilder['table'])) {
            $this->sql .= ' ' . $this->sqlBuilder['table'];
        } else {
            return new Exception('Une table doit être renseignée pour exécuter la query.');
        }
        if (isset($this->sqlBuilder['where'])) {
            $this->sql .= ' ' . $this->sqlBuilder['where'];
        } else {
            return new Exception('Une recherche WHERE doit être renseignée pour exécuter la query.');
        }
        $this->sql .= ';';
        // Services::dump($this->sql);

        return $this;
    }


    /**
     * Create bindValue PDO method.
     *
     * @param string $parameter
     * @param mixed $value
     * @param int|null $type
     *
     * @return Exception|void
     */
    private function buildQueryParameter(string $parameter, $value, ?int $type = null)
    {
        $buildParam = [
            'param' => $parameter,
        ];
        if ($type && in_array($type, $this->pdoTypes)) {
            switch ($type) {
                case PDO::PARAM_STR:
                    if (is_string($value)) {
                        $buildParam['type'] = $type;
                        $buildParam['value'] = htmlspecialchars($value);
                    } else {
                        return new Exception("Le type du paramètre $parameter ne correspond pas au type attendu.");
                    }
                    break;
                case PDO::PARAM_INT:
                    if (is_int($value)) {
                        $buildParam['type'] = $type;
                        $buildParam['value'] = $value;
                    } else {
                        return new Exception("Le type du paramètre $parameter ne correspond pas au type attendu.");
                    }
                    break;
                case PDO::PARAM_BOOL:
                    if (is_bool($value)) {
                        $buildParam['type'] = $type;
                        $buildParam['value'] = $value;
                    } else {
                        return new Exception("Le type du paramètre $parameter ne correspond pas au type attendu.");
                    }
                    break;
                case PDO::PARAM_NULL:
                    if (is_null($value)) {
                        $buildParam['type'] = $type;
                        $buildParam['value'] = $value;
                    } else {
                        return new Exception("Le type du paramètre $parameter ne correspond pas au type attendu.");
                    }
                    break;
            }
        } else {
            $buildParam['value'] = $value;
        }
        $this->bindedParameters[] = $buildParam;
    }


    public function __construct()
    {
        $this->sql = '';
        $this->sqlBuilder = null;
        $this->parameters = [];
        $this->bindedParameters = [];
    }


    /**
     * SQL Select Option.
     *
     * @param ?string|?array $items
     *
     * @return QueryBuilder
     */
    public function select($items = null)
    {
        $this->sqlBuilder['operation'] = 'SELECT';
        if (!$items) {
            $this->sqlBuilder['items'] = '*';
        } elseif (is_string($items)) {
            $this->sqlBuilder['items'] = htmlspecialchars($items);
        } elseif (is_array($items)) {
            $this->sqlBuilder['items'] = htmlspecialchars(implode(', ', $items));
        } else {
            new Exception('Le paramètre $items de la fonction select doit être de type string ou array');
        }

        return $this;
    }


    /**
     * SQL Insert Into Option.
     *
     * @param array $items
     *
     * @return QueryBuilder
     */
    public function insert(array $items)
    {
        $this->sqlBuilder['operation'] = 'INSERT INTO';
        if (is_array($items)) {
            $this->sqlBuilder['params'] = [];
            foreach ($items as $key => $value) {
                $safeValue = htmlspecialchars($value);
                $this->parameters[] = ":$safeValue";
                $this->sqlBuilder['params'][htmlspecialchars($key)] = $safeValue;
            }
        } else {
            new Exception('Le paramètre $items de la fonction insert doit être de type string ou array');
        }

        return $this;
    }


    /**
     * SQL Update Option.
     *
     * @param array $items
     *
     * @return QueryBuilder
     */
    public function update(array $items)
    {
        $this->sqlBuilder['operation'] = 'UPDATE';
        if (is_array($items)) {
            $params = [];
            foreach ($items as $key => $value) {
                if (is_string($key) && is_string($value)) {
                    $safeValue = htmlspecialchars($value);
                    $this->parameters[] = ":$safeValue";
                    $params[] = htmlspecialchars($key) . ' = ' . $safeValue;
                } else {
                    new Exception('Les paramètres $key et $value de la fonction where doivent être de type string');
                }
            }
            if (count($params) > 0) {
                $this->sqlBuilder['params'] = 'SET ' . implode(', ', $params);
            }
        } else {
            new Exception('Le paramètre $items de la fonction update doit être de type array');
        }

        return $this;
    }


    /**
     * SQL Delete Option.
     *
     * @return QueryBuilder
     */
    public function delete()
    {
        $this->sqlBuilder['operation'] = 'DELETE FROM';

        return $this;
    }


    /**
     * Execute operation in specified table.
     *
     * @param string $table
     *
     * @return QueryBuilder
     */
    public function inTable(string $table)
    {
        if (is_string($table)) {
            $safeTable = htmlspecialchars($table);
            $this->sqlBuilder['table'] = "`$safeTable`";
        } else {
            new Exception('Le paramètre $table de la fonction inTable doit être de type string');
        }

        return $this;
    }


    /**
     * Filter operation request.
     *
     * @param $key
     * @param $operator
     * @param $parameter
     *
     * @return QueryBuilder
     */
    public function where($key, $operator, $parameter)
    {
        if (in_array($operator, $this->permittedOperators)) {
            if (is_string($key) && is_string($parameter)) {
                $safeValue = htmlspecialchars($parameter);
                $this->parameters[] = ":$safeValue";
                $this->sqlBuilder['where'] = "WHERE $key $operator :$safeValue";
            } else {
                new Exception('Les paramètres $key et $parameter de la fonction where doivent être de type string');
            }
        } else {
            new Exception('Le paramètre $operator de la fonction where doit être un opérateur SQL valide.');
        }

        return $this;
    }


    /**
     * Add another mutual filter.
     *
     * @param $key
     * @param $operator
     * @param $parameter
     *
     * @return QueryBuilder
     */
    public function andWhere($key, $operator, $parameter)
    {
        if (in_array($operator, $this->permittedOperators)) {
            if (is_string($key) && is_string($parameter)) {
                $safeValue = htmlspecialchars($parameter);
                if (!isset($this->sqlBuilder['where'])) {
                    $this->sqlBuilder['where'] = "WHERE $key $operator :$safeValue";
                } else {
                    $previousWhere = $this->sqlBuilder['where'];
                    $this->sqlBuilder['where'] = [];
                    $this->sqlBuilder['where'][] = " AND $key $operator :$safeValue";
                    $this->sqlBuilder['where'] = $previousWhere . implode($this->sqlBuilder['where']);
                }
                $this->parameters[] = ":$safeValue";
            } else {
                new Exception('Les paramètres $key et $parameter de la fonction andWhere doivent être de type string');
            }
        } else {
            new Exception('Le paramètre $operator de la fonction andWhere doit être un opérateur SQL valide.');
        }

        return $this;
    }


    /**
     * Add another choice filter.
     *
     * @param $key
     * @param $operator
     * @param $parameter
     *
     * @return QueryBuilder
     */
    public function orWhere($key, $operator, $parameter)
    {
        if (in_array($operator, $this->permittedOperators)) {
            if (is_string($key) && is_string($parameter)) {
                $safeValue = htmlspecialchars($parameter);
                if (!isset($this->sqlBuilder['where'])) {
                    $this->sqlBuilder['where'] = "WHERE $key $operator :$safeValue";
                } else {
                    $previousWhere = $this->sqlBuilder['where'];
                    $this->sqlBuilder['where'] = [];
                    $this->sqlBuilder['where'][] = " OR $key $operator :$safeValue";
                    $this->sqlBuilder['where'] = $previousWhere . implode($this->sqlBuilder['where']);
                }
                $this->parameters[] = ":$safeValue";
            } else {
                new Exception('Les paramètres $key et $parameter de la fonction orWhere doivent être de type string');
            }
        } else {
            new Exception('Le paramètre $operator de la fonction orderBy doit être un opérateur SQL valide.');
        }

        return $this;
    }


    /**
     * Add results ordering.
     *
     * @param $key
     * @param $order
     *
     * @return QueryBuilder
     */
    public function orderBy($key, $order)
    {
        if (in_array($order, $this->permittedOrders)) {
            if (is_string($key)) {
                $safeKey = htmlspecialchars($key);
                $this->sqlBuilder['order'] = "ORDER BY $safeKey $order";
            } else {
                new Exception('Le paramètre $key de la fonction orderBy doit être de type string');
            }
        } else {
            new Exception('Le paramètre $order de la fonction orderBy doit être "ASC" ou "DESC".');
        }

        return $this;
    }


    /**
     * Add another results ordering.
     *
     * @param $key
     * @param $order
     *
     * @return QueryBuilder
     */
    public function addOrderBy($key, $order)
    {
        if (in_array($order, $this->permittedOrders)) {
            if (is_string($key)) {
                $safeKey = htmlspecialchars($key);
                if (!isset($this->sqlBuilder['order'])) {
                    $this->sqlBuilder['order'] = "ORDER BY $safeKey $order";
                } else {
                    $previousWhere = $this->sqlBuilder['order'];
                    $this->sqlBuilder['order'] = [];
                    $this->sqlBuilder['order'][] = "$safeKey $order";
                    $this->sqlBuilder['order'] = $previousWhere . ', ' . implode(', ', $this->sqlBuilder['order']);
                }
            } else {
                new Exception('Les paramètres $key et $value de la fonction orWhere doivent être de type string');
            }
        } else {
            new Exception('Le paramètre $operator de la fonction orderBy doit être un opérateur SQL valide.');
        }

        return $this;
    }


    /**
     * Limit results number.
     *
     * @param int      $count
     * @param int|null $offset
     *
     * @return QueryBuilder
     */
    public function limit(int $count, ?int $offset = null)
    {
        if (is_int($count)) {
            if ($offset && is_int($offset)) {
                $this->sqlBuilder['limit'] = "LIMIT $offset, $count";
            } else {
                $this->sqlBuilder['limit'] = "LIMIT $count";
            }
        } else {
            new Exception('Les paramètres $offset et $count de la fonction limit doivent être de type int.');
        }

        return $this;
    }


    /**
     * Group results items.
     *
     * @param string|array $keys
     *
     * @return QueryBuilder
     */
    public function groupBy($keys)
    {
        if (is_string($keys)) {
            $this->sqlBuilder['groupBy'] = "GROUP BY $keys";
        } elseif (is_array($keys)) {
            $ordering = implode(', ', $keys);
            $this->sqlBuilder['groupBy'] = "GROUP BY $ordering";
        } else {
            new Exception('Le paramètre $keys de la fonction groupBy doit être de type string ou array.');
        }

        return $this;
    }


    /**
     * Assign value to a defined parameter.
     *
     * @param array $parameters
     *
     * @return $this|Exception
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (isset($parameter[2])) {
                $this->buildQueryParameter($parameter[0], $parameter[1], $parameter[2]);
            } else {
                $this->buildQueryParameter($parameter[0], $parameter[1]);
            }
        }

        return $this;
    }


    /**
     * Create a raw query for out of possibilities contexts.
     *
     * @param string $query
     *
     * @return $this
     */
    public function raw(string $query)
    {
        $this->sqlBuilder['operation'] = 'RAW';
        $this->sql = $query;

        return $this;
    }


    /**
     * Transform SQL object in a raw query.
     *
     * @return QueryBuilder|Exception
     */
    public function getQuery()
    {
        switch ($this->sqlBuilder['operation']) {
            case 'SELECT':
                return $this->selectQuery();
            case 'UPDATE':
                return $this->updateQuery();
            case 'DELETE FROM':
                return $this->deleteQuery();
            case 'INSERT INTO':
                return $this->insertQuery();
            case 'RAW':
                return $this;
            default:
                return new Exception("L'opération demandée est impossible à réaliser.");
        }
    }


    /**
     * Execute SQL Query.
     *
     * @return array|bool
     */
    public function getResult()
    {
        try {
            $pdo = $this->connect();
            $query = $pdo->prepare($this->sql);

            if (count($this->bindedParameters) > 0) {
                foreach ($this->bindedParameters as $param) {
                    if (isset($param['type'])) {
                        $query->bindValue($param['param'], $param['value'], $param['type']);
                    } else {
                        $query->bindValue($param['param'], $param['value']);
                    }
                }
            }

            if (false !== strpos($this->sql, 'SELECT')) {
                return $query->fetchAll();
            }

            return $query->execute();
        } catch (\PDOException $exception) {
            Services::dump($exception);
        }
    }


    /**
     * Indicate how to render this object in string format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->sql;
    }
}
