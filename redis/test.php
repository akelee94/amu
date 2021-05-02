<?php
require_once  './Bitmap.php';

$user_id = 1001;

$date = "2021-04-24";

$bitmap = new Bitmap($user_id);

$result = $bitmap->rebuildSign($date);

var_dump($result);