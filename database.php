<?php

require_once "config.php";

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if ($conn->connect_error) {
    die("Database Connection Failed");
}

$conn->set_charset("utf8");