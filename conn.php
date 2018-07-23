<?php
/**
 * Created by PhpStorm.
 * User: kyle
 * Date: 7/22/18
 * Time: 12:33 AM
 */

$mysqli = new mysqli("localhost", "root", "hello", "todo");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

