<?php
require_once 'data/od_db.php';
function db_connect($dbid)
{
    switch ($dbid) {
    case "CCDB":
        $dbname = CommonDBName;
        $dbhost = CommonDBhost;
        $dbuser = CommonDBuser;
        $dbpass = CommonDBpass;
        $db_index = 'dbh';
        break;
    case "CIDB":
        $dbname = ExternalDBName;
        $dbhost = ExternalDBhost;
        $dbuser = ExternalDBuser;
        $dbpass = ExternalDBpass;
        $db_index = 'ci_dbh';
        break;
    default:
        break;
    }
    if (isset($_SESSION[$db_index])) {
        return;
    }
    try {
        $_SESSION[$db_index] = new PDO("mysql:host=$dbhost;dbname=$dbname", 
                                   $dbuser, $dbpass);
        $_SESSION[$db_index]->setAttribute(PDO::ATTR_ERRMODE, 
                                           PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
        die();
    }

}

function db_close($dbid)
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $_SESSION[$db_index] = null;
    unset($_SESSION[$db_index]);
}

function db_fetch_all($dbid, $query) 
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $result_rows = array();
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->query($query);
        $result_rows = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . "<br/>Query is: $query";
    }
    return $result_rows;
}

function db_result($result_rows, $index, $col)
{
    if (isset($result_rows[$index][$col])) {
        return $result_rows[$index][$col];
    }
    return null;
}

function db_fetch_key_pair($dbid, $query) 
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $result_rows = array();
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->query($query);
        $result_rows = $sth->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return $result_rows;
}

function db_fetch_column($dbid, $query) 
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $result_rows = array();
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->query($query);
        $result_rows = $sth->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return $result_rows;
}



function db_num_rows($result_rows)
{
    return count($result_rows);
}

function db_fetch_one($dbid, $query) 
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $result_row = array();
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->query($query);
        $result_row = $sth->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return $result_row;
}

function db_fetch_class_array($dbid, $query, $class_inst)
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    $result_classes =  array($class_inst);
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->query($query);
        $result_classes = $sth->fetchAll(PDO::FETCH_CLASS, $class_name);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return $result_classes;

}

function db_prepare($dbid, $query)
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        return $dbh->prepare($query);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return null;
}

function db_execute($sth, $bind_var_array)
{
    if ($sth == null) {
        return 0;
    }
    try {
        $sth->execute($bind_var_array);
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
        return 0;
    }
    return $sth->rowCount();
}

function db_stmt_fetchall($sth)
{
    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function db_get($result, $index)
{
    if (isset($result[$index])) {
        return $result[$index];
    }
    return null;
}

function db_pexecute($dbid, $query, $bind_var_array)
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $sth = $dbh->prepare($query);
        $sth->execute($bind_var_array);
        return $sth->rowCount();
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return 0;
}

function db_exec($dbid, $query)
{
    if ($dbid == "CCDB") {
        $db_index = 'dbh';
    } else {
        $db_index = 'ci_dbh';
    }
    if (!isset($_SESSION[$db_index])) {
        db_connect($dbid);
    }
    $dbh = $_SESSION[$db_index];
    try {
        $dbh->exec($query);
        return 1;
    } catch (PDOException $e) {
        print 'Error: ' . $e->getMessage() . '<br />';
    }
    return 0;
}
?>
