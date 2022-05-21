<?php
include "../../config/config.php";
include "../classes/User.php";
include "../classes/Notification.php";

$limit = 5;

$notify = new Notification($con, $_REQUEST['user']);
echo $notify->getNotifications($_REQUEST, $limit);