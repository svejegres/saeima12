<?php

/*
 * CONNECT to the database
 */
function db() {
  static $conn;
  if ($conn === NULL) {
    $conn = mysqli_connect('localhost', 'root', 'parole', 'g2test');
    if (!$conn) {
      die('Connection failed ' . mysqli_error($conn));
    }
  }
  return $conn;
}
$conn = db();

// MySQL used to create table 'politicians':
// create table politicians (id int auto_increment primary key, family_name varchar(255) not null, given_name varchar(255) not null, image_url varchar(255) not null, contact_details varchar(255) not null);
