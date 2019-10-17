<?php

session_start();

include("db-connection.php");

/*
 * Check if data was already loaded into database, otherwise - parse JSON data once:
 */
$query = "SELECT family_name FROM politicians LIMIT 1;";
$result = mysqli_query($conn, $query);
$isTablePopulated = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$isTablePopulated) {
  $jsonData = file_get_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'list.json');
  $list = json_decode($jsonData, true);

  foreach ($list["persons"] as $value) {
    $familyName = $value["family_name"];
    $givenName = $value["given_name"];
    $imageUrl = $value["image"];

    if ($value["contact_details"]) {
      $contactDetailsType = $value["contact_details"][0]["type"];
      $contactDetailsValue = $value["contact_details"][0]["value"];
      $contactDetails = "type=$contactDetailsType&value=$contactDetailsValue";
    } else {
      $contactDetails = "";
    }

    $sql = "INSERT INTO politicians (family_name, given_name, image_url, contact_details)
    VALUES ('{$familyName}', '{$givenName}', '{$imageUrl}', '{$contactDetails}')";

    if (!mysqli_query($conn, $sql)) {
      echo "Error: ". mysqli_error($conn);
    }
  }
}

/*
 * Prepare pagination:
 */
if($_GET["search_keyword"]) {
  $q = $_GET["search_keyword"];
  $_SESSION["search_keyword"] = $q;
  $query = "SELECT COUNT(*) FROM politicians WHERE family_name LIKE '%$q%' OR given_name LIKE '%$q%';";
} else {
  $query = "SELECT COUNT(*) FROM politicians;";
}

$result = mysqli_query($conn, $query);

// find out how many items are in the table:
$total = (int)mysqli_fetch_row($result)[0];

// how many items to list per page:
$limit = 20;

// how many pages will there be:
$pages = ceil($total / $limit);

// what page are we currently on:
$page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
  'options' => array(
      'default'   => 1,
      'min_range' => 1,
  ),
)));

// calculate the offset for the query:
$offset = ($page - 1)  * $limit;

// some information to display to the user:
$start = $offset + 1;
$end = min(($offset + $limit), $total);

// the "back" link:
$prevlink = ($page > 1) ? '<a href="?page=1" title="First page">&laquo;</a> <a href="?page=' . ($page - 1) . '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

// the "forward" link:
$nextlink = ($page < $pages) ? '<a href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

/*
 * Insert page content:
 */
if($_GET["search_keyword"]) {
  $q = $_GET["search_keyword"];
  $query = "SELECT family_name, given_name, image_url, contact_details FROM politicians
  WHERE family_name LIKE '%$q%' OR given_name LIKE '%$q%' LIMIT $limit OFFSET $offset;";
} else {
  $query = "SELECT family_name, given_name, image_url, contact_details FROM politicians LIMIT $limit OFFSET $offset;";
}
$result = mysqli_query($conn, $query);

$politicians = '';
while ($row = mysqli_fetch_array($result)) {
  $fullName = $row['given_name'] . " " . $row['family_name'];
  $imageUrl = $row['image_url'];

  $politicians .= '
    <li class="politician">
      <p class="name">' . $fullName . '</p>
      <img class="photo" src="' . $imageUrl .'" alt="' . $fullName .' portrait" />
  ';

  if ($row['contact_details']) {
    parse_str($row['contact_details'], $contactDetails);

    if ($contactDetails["type"] === "twitter") {
      $contactLink = "https://twitter.com/" . $contactDetails["value"];
    } else {
      $contactLink = "#";
    }

    $politicians .= '
      <a class="contact" href="' . $contactLink . '">' . $contactDetails["type"] . '</a>
    ';
  }
  $politicians .= '</li>';
}

if ($_GET["search_keyword"] || $_GET["reset"] === "true") {
  $response = array();

  $response["politicians"] = $politicians;
  $response["prevlink"] = $prevlink;
  $response["page"] = $page;
  $response["pages"] = $pages;
  $response["nextlink"] = $nextlink;

  echo json_encode($response);
}

if ($_GET["reset"] === "true") unset($_SESSION["search_keyword"]);

mysqli_free_result($result);
mysqli_close($conn);