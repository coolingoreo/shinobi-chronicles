<?php
session_start();

if(!isset($_SESSION['user_id'])) {
	exit;
}

require("classes.php");

$system = new SystemFunctions();
$system->dbConnect();

$player = new User($_SESSION['user_id']);

if($player->staff_level < SystemFunctions::SC_HEAD_ADMINISTRATOR) {
	exit;
}

$result = $system->query("SELECT `bloodline_id`, `jutsu` FROM `bloodlines`");
$bloodlines = array();
while($row = $system->db_fetch($result)) {
	$bloodlines[$row['bloodline_id']] = json_decode($row['jutsu'], true);
}

$jutsu_costs[2] = 30;
$jutsu_costs[3] = 100;

foreach($bloodlines as $bloodline_id => $bloodline_jutsu) {
	foreach($bloodline_jutsu as $id => $jutsu) {
		$bloodlines[$bloodline_id][$id]['power'] = ($jutsu['rank'] + 0.5);
		$bloodlines[$bloodline_id][$id]['use_cost'] = $jutsu_costs[$jutsu['rank']];
	}
	$jutsu_string = json_encode($bloodlines[$bloodline_id]);
	$query = "UPDATE `bloodlines` SET `jutsu`='$jutsu_string' WHERE `bloodline_id`='$bloodline_id' LIMIT 1";
	$system->query($query);
}






?>