--TEST--
Data type precedence: conversion NVARCHAR(n)
--SKIPIF--
--FILE--
<?php

require_once("autonomous_setup.php");

// Connect
$connectionInfo = array("UID"=>$username, "PWD"=>$password, "CharacterSet"=>"UTF-8");
$conn = sqlsrv_connect($serverName, $connectionInfo) ?: die();

// Create database
sqlsrv_query($conn,"CREATE DATABASE ". $dbName) ?: die();

// Create table. Column names: passport
$sql = "CREATE TABLE $tableName (c1 NVARCHAR(8))";
$stmt = sqlsrv_query($conn, $sql);

// Insert data. The data type with the lower precedence
// is converted to the data type with the higher precedence
$sql = "INSERT INTO $tableName VALUES (3.1415),(-32),(null)";
$stmt = sqlsrv_query($conn, $sql);

// Insert more data
$sql = "INSERT INTO $tableName VALUES (''),('Galaxy'),('-- GO'),(N'银河系')";
$stmt = sqlsrv_query($conn, $sql);

// Read data from the table
$sql = "SELECT * FROM $tableName";
$stmt = sqlsrv_query($conn, $sql);

while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC)) {
	var_dump($row[0]);
}

// DROP database
sqlsrv_query($conn,"DROP DATABASE ". $dbName);

// Free statement and connection resources
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

print "Done"
?>

--EXPECT--
string(6) "3.1415"
string(8) "-32.0000"
NULL
string(0) ""
string(6) "Galaxy"
string(5) "-- GO"
string(9) "银河系"
Done
