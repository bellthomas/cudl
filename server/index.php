<?php 
/*
 * Initiate Global Variables and Definitions
 */
 
global $Notices;
global $db;
global $MainExecuted;
$MainExecuted = false;

define(DEBUG, true);


/*
 * Include and Init Performance Monitor
 */
require_once('library/class.performance.php');
$Performance = new Performance;


// Include credentials file
require_once('git-ignore/credentials.php');

/*
 * Include Helper Functions
 */
require_once('library/functions.helper.php');

/*
 * Require DB and Request Class and Instantiate
 */
require_once('library/class.database.php');
require_once('library/class.request.php');

$db = new EmergencieDatabase('main');

//$array['emie_actions'] = array('action1', 'action2');
//$array['emie_parameters'] = array(array('lat', 'long'), array('search'));


//echo json_encode($array);
/*
 * Instantiate Request Class
 */
//$request = new EmergencieRequest('postalCodeLookup');

//PrettyPrint(json_decode($output));


//PrettyPrint($request->GetNotices);

/*
if($_GET['lat'] && $_GET['long']) {
	//if($request->LatLongToPostCode(53.298056, -2.988281) === TRUE) {
	$postcode = $request->LatLongToPostCode($_GET['lat'], $_GET['long']);
	if($postcode === TRUE) {
		$postcode_data = ($request->GetParameter('translated_postcode'));
		echo '<br>Latitude: ' . $postcode_data->lat;
		echo '<br>Longitude: ' . $postcode_data->lng;
		echo '<br>Postcode: ' . $postcode_data->postalCode;
		echo '<br>Place Name: ' . $postcode_data->placeName;
		echo '<br>Distance Away: ' . ($postcode_data->distance) * 1000 . 'm';
	}
	else {
		echo $postcode;	
	}
}*/


/*
 * Pass test data to Request object
 */ 

$emie_actions_test = array( 'NearestStreet');
$emie_parameters_test = array(
	//array('search' => 'epilepsy' ),	// Liverpool
	array('lat' => 51.820878, 'long' => -2.697587 ),	// Liverpool
);

//PrettyPrint($emie_actions);
//PrettyPrint($emie_parameters);


/*
 * Loop through data, creating instantiated objects
 */
function MainExecuteRequest($emie_actions, $emie_parameters) {
	global $MainExecuted;
	if(sizeof($emie_actions) == sizeof($emie_parameters)) :

		$i = 0;

		foreach($emie_actions as $action) {

			//generate unique name based on iteration of loop
			$individual_variable_name = 'request' . $i; 

			//instantiated class with variable name
			$$individual_variable_name = new EmergencieRequest($emie_actions[$i]);



			//generate unique name for URL return variable
			$individual_request_url_name = $individual_variable_name . '_url';

			//assign parameters using iteration count to match the reflected arrays
			$$individual_request_url_name = $$individual_variable_name->RequestParameters($emie_parameters[$i]);



			//create individual output variable name
			$individual_raw_output_name = $individual_variable_name . '_output_raw';

			//get execute return, assign to unique variable
			$$individual_raw_output_name = $$individual_variable_name->Execute($$individual_request_url_name);
			
			
			if($$individual_raw_output_name) :
			
				//create individual output variable name
				$individual_output_name = $individual_variable_name . '_output';
		
				//get execute return, assign to unique variable
				$$individual_output_name = $$individual_variable_name->GenerateOutput($$individual_raw_output_name);
	
				echo '<br>';

			else :
			
				echo 'error';
			
			endif;
			//echo '<h3>'.$i.'</h3>';
			//PrettyPrint(($$individual_variable_name));
			//PrettyPrint(($$individual_request_url_name));
			//PrettyPrint (($$individual_output_name));

			//PrettyPrint($$individual_variable_name->GetNotices());


			$i++;
		}
	else :
		echo 'Mismatched Actions and Parameters arrays - the lengths are different! Corrupt data request, whole request ignored.';
	endif;
	$MainExecuted = true;
}
		//MainExecuteRequest($emie_actions_test, $emie_parameters_test);
$request_data = json_decode($_REQUEST['emie_request']);
//PrettyPrint($request_data);
if(isset($request_data->emie_actions) && isset($request_data->emie_parameters)) :


	//PrettyPrint($request_data->emie_actions);
	//PrettyPrint($request_data->emie_parameters);
	if(!$MainExecuted) 
		MainExecuteRequest($request_data->emie_actions, $request_data->emie_parameters);
	
endif;

/*
$LatLongToLocal = new EmergencieRequest('NearestHospital');
$parameters = array('lat' => 38.897676, 'long' => -77.1 );
$RequestURL = $LatLongToLocal->RequestParameters($parameters);
PrettyPrint($LatLongToLocal->Execute($RequestURL));

$request_notices = $LatLongToLocal->GetNotices();


PrettyPrint($request_notices);*/

$ProcessingTime = $Performance->EndOfScript();
ShowNotices();
$db->AddSystemNoticesToLog();