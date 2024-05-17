<?php

namespace Helpers;

class DB
{
    const HOST = "localhost";
    const USERNAME = "root";
    const PASSWORD = "";
    const DATABASENAME = "database";
    public static $host = "host";
    public static $apiToken = 'token';
    protected static $database;
    protected static $table;
    protected static $select = "*";
    protected static $whereRawKey;
    protected static $whereRawVal;
    protected static $whereKey;
    protected static $whereVal = array();
    protected static $orderBy = null;
    protected static $groupBy = null;
    protected static $limit = null;
    protected static $join = "";
    protected static $leftJoin = "";
    protected static $offset = null;

    function __construct()
    {
        self::__connnect();
    }
    protected static function __connnect()
    {
        try {
            self::$database = new \PDO("mysql:host=" . self::HOST . ";dbname=" . self::DATABASENAME . ";charset=UTF8", self::USERNAME, self::PASSWORD);
        } catch (\PDOException $e) {
            die($e);
        }
    }

    public static function table($tableName)
    {
        self::$table = $tableName;
        self::$select = "*";
        self::$whereRawKey = null;
        self::$whereRawVal = null;
        self::$whereKey = null;
        self::$whereVal = array();
        self::$orderBy = null;
        self::$limit = null;
        self::$join = "";
        self::$leftJoin = "";
        self::$groupBy = null;
        return new self;
    }

    public static function select($columns)
    {
        self::$select = $columns;
        return new self;
    }

    public static function whereRaw($whereRaw, $whereRawVal)
    {
        self::$whereRawKey = "(" . $whereRaw . ")";
        self::$whereRawVal = $whereRawVal;
        return new self;
    }

    public static function whereIn($column, $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        self::$whereKey = $column . " IN (" . $placeholders . ")";
        self::$whereVal = array_merge(self::$whereVal, $values);
        return new self;
    }

    public static function where($columns, $columnsTwo = null, $columnsTheree = null)
    {
        if (is_array($columns) != false) {
            $keyList = array();
            foreach ($columns as $key => $value) {
                self::$whereVal[] = $value;
                $keyList[] = $key;
            }
            self::$whereKey = implode("=? AND ", $keyList) . "=? ";
        } elseif ($columnsTwo != null && $columnsTheree == null) {
            self::$whereVal[] = $columnsTwo;
            self::$whereKey = $columns . "=? ";
        } elseif ($columnsTheree != null) {
            self::$whereVal[] = $columnsTheree;
            self::$whereKey =  $columns . $columnsTwo . "?";
        }
        return new self;
    }

    public static function orderBy($parameter)
    {
        self::$orderBy = $parameter[0] . " " . ((!empty($parameter[1])) ? $parameter[1] : "ASC");
        return new self;
    }

    public static function groupBy($paramater)
    {
        self::$groupBy = $paramater[0] . " " . ((!empty($paramater[1]))) ? $paramater[1] : " ";
        return new self;
    }

    public static function limit($start, $end = null)
    {
        self::$limit = $start . (($end != null) ? "," . $end : "");
        return new self;
    }

    public static function join($tableName, $thisColumn, $joinColumn)
    {
        self::$join .= " JOIN " . $tableName . " ON " . self::$table . "." . $thisColumn . "=" . $tableName . "." . $joinColumn . " ";
        return new self;
    }

    public static function leftJoin($tableName, $thisColumn, $joinColumn)
    {
        self::$leftJoin .= " LEFT JOIN " . $tableName . " ON " . self::$table . "." . $thisColumn . "=" . $tableName . "." . $joinColumn . " ";
        return new self;
    }

    public static function offset($start)
    {
        self::$offset = $start;
        return new self;
    }

    public static function get()
    {
        $SQL = "SELECT " . self::$select . " FROM " . self::$table . " ";
        $SQL .= (!empty(self::$join)) ? self::$join : "";
        $SQL .= (!empty(self::$leftJoin)) ? self::$leftJoin : "";
        $where = null;
        if (!empty(self::$whereKey) && !empty(self::$whereRawKey)) {
            $SQL .= " WHERE " . self::$whereKey . " AND " . self::$whereRawKey . " ";
            $where = array_merge(self::$whereVal, self::$whereRawVal);
        } else {
            if (!empty(self::$whereKey)) {
                $SQL .= " WHERE " . self::$whereKey . " ";
                $where = self::$whereVal;
            }
            if (!empty(self::$whereRawKey)) {
                $SQL .= " WHERE " . self::$whereRawKey . " ";
                $where = self::$whereRawVal;
            }
        };
        $SQL .= (!empty(self::$groupBy)) ? " GROUP BY " . self::$groupBy . " " : "";
        $SQL .= (!empty(self::$orderBy)) ? " ORDER BY " . self::$orderBy . " " : "";
        $SQL .= (!empty(self::$limit)) ? " LIMIT " . self::$limit . " " : "";
        $SQL .= (!empty(self::$offset)) ? " OFFSET " . self::$offset . " " : "";
        $Entity = self::$database->prepare($SQL);
        $Sync = ($where != null) ? $Entity->execute($where) : $Entity->execute();
        $Result = $Entity->fetchAll(\PDO::FETCH_OBJ);
        return $Result ? ($Result) : false;
    }

    public static function first()
    {
        $SQL = "SELECT " . self::$select . " FROM " . self::$table . " ";
        $SQL .= (!empty(self::$join)) ? self::$join : "";
        $SQL .= (!empty(self::$leftJoin)) ? self::$leftJoin : "";
        $where = null;
        if (!empty(self::$whereKey) && !empty(self::$whereRawKey)) {
            $SQL .= " WHERE " . self::$whereKey . " AND " . self::$whereRawKey . " ";
            $where = array_merge(self::$whereVal, self::$whereRawVal);
        } else {
            if (!empty(self::$whereKey)) {
                $SQL .= " WHERE " . self::$whereKey . " ";
                $where = self::$whereVal;
            }
            if (!empty(self::$whereRawKey)) {
                $SQL .= " WHERE " . self::$whereRawKey . " ";
                $where = self::$whereRawVal;
            }
        };
        $SQL .= (!empty(self::$groupBy)) ? " GROUP BY " . self::$groupBy . " " : "";
        $SQL .= (!empty(self::$orderBy)) ? " ORDER BY " . self::$orderBy . " " : "";
        $SQL .= (!empty(self::$limit)) ? " LIMIT " . self::$limit . " " : "";
        $SQL .= (!empty(self::$offset)) ? " OFFSET " . self::$offset . " " : "";
        $Entity = self::$database->prepare($SQL);
        $Sync = ($where != null) ? $Entity->execute($where) : $Entity->execute();
        $Result = $Entity->fetch(\PDO::FETCH_OBJ);
        return $Result ? ($Result) : false;
    }

    public static function create($arrayColumns)
    {
        $columns = array_keys($arrayColumns);
        $columnsValue = array_values($arrayColumns);
        $SQL = "INSERT INTO " . self::$table . " SET " . implode("=?,", $columns) . "=? ";
        $Entity = self::$database->prepare($SQL);
        $Sync = $Entity->execute($columnsValue);
        return ($Sync !== false);
    }

    public static function update($arrayColumns)
    {
        $columns = array_keys($arrayColumns);
        $columnsValue = array_values($arrayColumns);
        $SQL = "UPDATE " . self::$table . " SET " . implode("=?,", $columns) . "=? ";
        $where = null;
        if (!empty(self::$whereKey) && !empty(self::$whereRawKey)) {
            $SQL .= " WHERE " . self::$whereKey . " AND " . self::$whereRawKey . " ";
            $where = array_merge(self::$whereVal, self::$whereRawVal);
        } else {
            if (!empty(self::$whereKey)) {
                $SQL .= " WHERE " . self::$whereKey . " ";
                $where = self::$whereVal;
            }
            if (!empty(self::$whereRawKey)) {
                $SQL .= " WHERE " . self::$whereRawKey . " ";
                $where = self::$whereRawVal;
            }
        };
        if ($where != null) {
            $arrayColumns = array_merge($columnsValue, $where);
        }
        $Entity = self::$database->prepare($SQL);
        $Sync = $Entity->execute($arrayColumns);
        return ($Sync !== false);
    }

    public static function delete()
    {
        $SQL = 'DELETE FROM ' . self::$table . ' ';
        $where = null;
        if (!empty(self::$whereKey) && !empty(self::$whereRawKey)) {
            $SQL .= " WHERE " . self::$whereKey . " AND " . self::$whereRawKey . " ";
            $where = array_merge(self::$whereVal, self::$whereRawVal);
        } else {
            if (!empty(self::$whereKey)) {
                $SQL .= " WHERE " . self::$whereKey . " ";
                $where = self::$whereVal;
            }
            if (!empty(self::$whereRawKey)) {
                $SQL .= " WHERE " . self::$whereRawKey . " ";
                $where = self::$whereRawVal;
            }
        };
        $Entity = self::$database->prepare($SQL);
        $Sync = $Entity->execute($where);
        return ($Sync !== false);
    }

    public static function settings()
    {
        $settings = self::table('isoft_settings')->where('set_id', 1)->limit(1)->first();
        return $settings;
    }

    public static function primaryKey($tableName)
    {
        self::__connnect();
        $SQL = "SHOW TABLE STATUS FROM " . self::DATABASENAME . " WHERE Name = '" . $tableName . "'";
        $Entity = self::$database->prepare($SQL);
        $Sync = $Entity->execute();
        $Result = $Entity->fetchAll(\PDO::FETCH_OBJ);
        return ($Result[0]->Auto_increment);
    }

    public static function productCall($sql)
    {
        $Entity = self::$database->prepare($sql);
        $Sync = $Entity->execute();
        $Result = $Entity->fetchAll(\PDO::FETCH_OBJ);
        return ($Result ? $Result : false);
    }
    
    public static function pluck($collection, $key)
    {
        return array_column($collection, $key);
    }
}

