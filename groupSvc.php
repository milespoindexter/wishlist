<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-type: application/json');

require_once("GroupMgr.php");
$groupMgr = new GroupMgr();

$groups_json = json_encode($groupMgr->getAllGroups());
echo($groups_json);

?>

