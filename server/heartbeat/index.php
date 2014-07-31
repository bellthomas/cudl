<?php
// not a valid request
//if( !isset($_REQUEST['emie_heartbeat'])) die();

define(DEBUG, false);
if(DEBUG){
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	//error_reporting(E_ALL);
}


global $db;

// import db credentials
require_once('../git-ignore/credentials.php');

/*
 * Include Helper Functions
 */
require_once('../library/functions.helper.php');

/*
 * Require DB Class, Request Class and Instantiate
 */
require_once('../library/class.database.php');
require_once('../library/class.request.php');
require_once('../library/class.uuid.php');

$db = new EmergencieDatabase('heartbeat');

//if($db->HeartbeatUpdate(84758, 51.816433, -2.727435)) echo '1';
//else echo '0';

//PrettyPrint($_REQUEST);

//84756 - np253sw
//84757 - np253xp
//84758 - sw1a1aa
//84759 - monmouth petrol station ?emie_heartbeat_set_alert=%7B"emie_personal"%3A%5B"Harri"%2C"M"%5D%2C+"emie_location"%3A%5B"51.806433"%2C+"-2.717435"%5D%7D

//PrettyPrint($db->HeartbeatMatrix(51.820878, -2.697587));
// 
//echo urlencode('{"emie_id":["56435","465"], "emie_personal":["Harri","M"], "emie_location":[51.816433, -2.727435]}');
//echo urlencode('{"emie_id":"84758"}');



/*
 * Check if Heartbeat update has been requested
 */
if( isset($_REQUEST['emie_heartbeat']) ) {

	$request_data = json_decode($_REQUEST['emie_heartbeat']);

	//PrettyPrint($_REQUEST['emie_heartbeat']);
	
	//$request_data->emie_heartbeat;  // array, 2 values, uuid stuff
	//$request_data->emie_location;  // array, 2 values, lat and long
	
	
	if($db->HeartbeatUpdate($request_data->emie_id[0], 
	(string)$request_data->emie_location[0], 
	(string)$request_data->emie_location[1])) echo 1;
	
	else echo 0;






/*
 * Check if Heartbeat set alert has been requested
 */
} elseif( isset($_REQUEST['emie_heartbeat_set_alert']) ) {

	$request_data = json_decode($_REQUEST['emie_heartbeat_set_alert']);
	
	//PrettyPrint($request_data);

	if($alert_number = $db->CreateNewHeartbeatAlert($request_data->emie_personal, $request_data->emie_location, $request_data->emie_id)) {

		if($users = $db->HeartbeatMatrix($request_data->emie_location[0], $request_data->emie_location[1])) { 
			//echo $alert_number;
			//PrettyPrint($users);
			foreach($users as $user)
				$db->AddNewAlertToUser($user['ID'], $user['UniqueID'], $alert_number);
		
			echo 2;
		
		
		} else echo 1;
		
	} else echo 0;




/*
 * Check if Heartbeat get alerts has been requested
 */
} elseif( isset($_REQUEST['emie_heartbeat_get_alerts']) ) {

	$request_data = json_decode($_REQUEST['emie_heartbeat_get_alerts']);
	
	if($output = $db->GetAlertsByUID($request_data->emie_id[0])) {
		foreach($output as $alert) {
			echo json_encode($alert);
			echo '%%%%';
		}
		//PrettyPrint($output);
	} else echo 0;




/*
 * Create ID if request
 */
} elseif( isset($_REQUEST['emie_heartbeat_init']) && $_REQUEST['emie_heartbeat_init'] == HEARTBEAT_INIT_AUTH  ) {
	$db->CreateUniqueID();


/* 
 * Remove ID if requested
 *
 * example - {"emie_id":["4a8c757a-852f-499b-8c13-f932ab1d2eca","f8d64ea0-b7e8-58f1-82e3-407498567d7d"]}
 */
} elseif( isset($_REQUEST['emie_heartbeat_remove'])) {

	$request_data = json_decode($_REQUEST['emie_heartbeat_remove']);
	
	if($db->RemoveID($request_data->emie_id[0], $request_data->emie_id[1])) echo 1;
	else echo 0;


// Invalid Request :(
} else echo 0;


ShowNotices();
$db->AddSystemNoticesToLog();
?>