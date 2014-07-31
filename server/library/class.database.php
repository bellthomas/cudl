<?php 
/**
 * Create Emergencie's Database Object.
 *
 * @author Harri Bell-Thomas < @harribellthomas >
 */
 
class EmergencieDatabase {
	
	public $database_object = NULL;
	public $database_host = '';
	public $database_name = '';
	public $database_user = '';
	public $database_password = '';
	public $db_prefix;
	
	
	function __construct($db_name = 'main') {
		if($db_name == 'main') {
			$this->db_prefix = 'emergencie_';
			$this->database_host = MAIN_DB_HOST;
			$this->database_name = MAIN_DB_NAME;
			$this->database_user = MAIN_DB_USER;
			$this->database_password = MAIN_DB_PASSWORD;
		}
		elseif($db_name == 'heartbeat') {
			$this->db_prefix = 'heartbeat_';
			$this->database_host = HB_DB_HOST;
			$this->database_name = HB_DB_NAME;
			$this->database_user = HB_DB_USER;
			$this->database_password = HB_DB_PASSWORD;
		}
		
		try {
			$pdo = new PDO("mysql:host=".$this->database_host.";dbname=".$this->database_name."", $this->database_user, $this->database_password);
		}
		catch (PDOException $e) {
			exit($e . ' - Error Connecting To Database');
		}
		
		if($pdo) {
			$this->database_object = $pdo;
			$this->DatabaseSetup();
		} else {
			return FALSE;	
		}
	}
	
	
	/**
	 * Check if a table exists in the current database.
	 *
	 * @param string $table Table to search for.
	 * @return bool TRUE if table exists, FALSE if no table found.
	 */
	function TableExists($table) {
	
		// Try a select statement against the table
		// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		try {
			$result = $this->database_object->query("SELECT 1 FROM $table LIMIT 1");
		} catch (Exception $e) {
			// We got an exception == table not found
			$GLOBALS['Notices'][] = array('Table has not been found', time(), __FILE__);
			return FALSE;
		}
	
		// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
		return $result !== FALSE;
	}
	
	
	
	/**
	 * Create table
	 *
	 * @param string $table Table to search for.
	 * @return bool TRUE if table created, FALSE if not.
	 */
	function CreateTable($table, $columns) {
		global $Notices;
		// Try a execution
		// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		try {
			$createTable = $this->database_object->exec("CREATE TABLE $table ($columns)");
		} catch (Exception $e) {
			PrettyPrint($e);
			return FALSE;
		}
		PrettyPrint($createTable);
		
		if ($createTable) 
			$Notices[] = array("Table $table - Created!", time(), __FILE__);
			
		else {
			$Notices[] = array("Table $table not successfully created!", time(), __FILE__);
			//echo $columns;
			//PrettyPrint($this->database_object->errorInfo());
		}
		// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
		return $result !== FALSE;
	}
	
	/**
	 * Setup Database Environment
	 *
	 * @return bool TRUE if no problems, FALSE if error occured.
	 */
	function DatabaseSetup() {
		
		$tables = array(
			array(
				'services' => array('emergencie_'),
				'table_name' => $this->db_prefix . 'logs',
				'columns' => 'ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY' 
							  /*'Prename VARCHAR( 50 ) NOT NULL, 
							  Name VARCHAR( 250 ) NOT NULL,
							  StreetA VARCHAR( 150 ) NOT NULL, 
							  StreetB VARCHAR( 150 ) NOT NULL, 
							  StreetC VARCHAR( 150 ) NOT NULL, 
							  County VARCHAR( 100 ) NOT NULL, 
							  Postcode VARCHAR( 50 ) NOT NULL, 
							  Country VARCHAR( 50 ) NOT NULL'*/,
			),
			array(
				'services' => array('emergencie_', 'heartbeat_'),
				'table_name' => $this->db_prefix . 'system_logs',
				'columns' => 'ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
							  SystemTime VARCHAR( 50 ) , 
							  Message VARCHAR( 250 ),
							  SystemFile VARCHAR( 50 ) ',
			),
			array(
				'services' => array('heartbeat_'),
				'table_name' => $this->db_prefix . 'data',
				'columns' => 'ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
							  UniqueID VARCHAR( 50 ) , 
							  Latitude VARCHAR( 50 ),
							  Longitude VARCHAR( 50 ),
							  CountryCode VARCHAR( 8 ),
							  Region VARCHAR( 50 ), 
							  Alerts VARCHAR( 50 ),
							  SystemTime VARCHAR( 50 )',
			),
			array(
				'services' => array('heartbeat_'),
				'table_name' => $this->db_prefix . 'alerts',
				'columns' => 'ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
							  Name VARCHAR( 50 ) , 
							  Gender VARCHAR( 50 ),
							  Latitude VARCHAR( 50 ),
							  Longitude VARCHAR( 50 ),
							  Time INT( 11 ),
							  UniqueID VARCHAR( 50 )',
			),
			array(
				'services' => array('heartbeat_'),
				'table_name' => $this->db_prefix . 'identifiers',
				'columns' => 'ID VARCHAR( 50 ) , 
							  Token VARCHAR( 50 )',
			),
		);
		
		foreach($tables as $table) {
			if( !$this->TableExists($table['table_name']) && in_array($this->db_prefix, $table['services'], TRUE) )
				$this->CreateTable($table['table_name'], $table['columns']);
		}
	}
	
	
	
	/**
	 * Method to add a data row to a table
	 *
	 * @return bool TRUE if no problems, FALSE if error occured.
	 */
	 
	function AddSystemNoticesToLog() {
		global $Notices;
		foreach($Notices as $message) {
			$file = explode('emergencie', $message[2]);
			$SQL = "INSERT INTO `db537596565`.`".$this->db_prefix."system_logs` (`ID`, `SystemTime`, `Message`, `SystemFile`) VALUES (NULL, '".$message[1]."', '".$message[0]."', '".$file[1]."')";
			$InsertNotice = $this->database_object->exec($SQL);	
			unset($SQL);
			if(!$InsertNotice){}
				//die('');
		}
	}
	
	/**
	 * Method to add a data row to a table
	 *
	 * @return bool TRUE if no problems, FALSE if error occured.
	 */
	function HeartbeatUpdate($UID, $lat, $long) {
		
		global $Notices;
		if($this->db_prefix == 'heartbeat_') {
			$db = $this->database_object;

			$SQL = "SELECT `ID` FROM `".$this->db_prefix . 'data'."` WHERE `UniqueID` = ?";
			$heartbeat = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
			$params = array($UID);
			$heartbeat->execute( $params );
			$UIDExists = $heartbeat->FetchAll();
			
			
			if($UIDExists && $this->LatLongValid($lat, $long)) : 
				$index = $UIDExists[0]['ID'];

				// Emergencie Request to collect region data
				$Regions = new EmergencieRequest('LatLongToLocal');
				$Regions->LatLongToPostCode($lat, $long);
				$postcodeData = $Regions->returnVariable('translated_postcode');
				$region = $postcodeData->adminCode1 . '-' . $postcodeData->adminCode2 . '-' . $postcodeData->adminCode3;
				$ccode = $postcodeData->countryCode;


				// need to update existing entry
				$SQL = "UPDATE ".$this->db_prefix . 'data'." SET Latitude=?, Longitude=?, Region=?, CountryCode=?, SystemTime=? WHERE ID='".$index."'";
				$heartbeat_action = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
				$params = array($lat, $long, $region, $ccode, time());

				if($heartbeat_action->execute( $params )) return TRUE;
				else return FALSE;
				//$UIDExists = $heartbeat->FetchAll();
				//PrettyPrint($heartbeat_action->errorInfo());
				
				
				
			elseif($this->LatLongValid($lat, $long)) :

				// Emergencie Request to collect region data
				$Regions = new EmergencieRequest('LatLongToLocal');
				$Regions->LatLongToPostCode($lat, $long);
				$postcodeData = $Regions->returnVariable('translated_postcode');
				$region = $postcodeData->adminCode1 . '-' . $postcodeData->adminCode2 . '-' . $postcodeData->adminCode3;
				$ccode = $postcodeData->countryCode;


				//doesn't exist, create
				$SQL = "INSERT INTO ".$this->db_prefix . "data (ID, UniqueID, Latitude, Longitude, CountryCode, Region, SystemTime) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
				$heartbeat_action = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
				$params = array($UID, $lat, $long, $ccode, $region, time());
				if($heartbeat_action->execute( $params )) return TRUE;
				else return FALSE;
				//PrettyPrint($heartbeat_action->errorInfo());

			else:
				return FALSE; // not heartbeat db or no uid
			endif;
		}
	}
	 
	 
	 
	/**
	 * Method to check if latitude and longitude are legal
	 *
	 * @return bool TRUE if valid, FALSE if not.
	 */
	
	function LatLongValid($lat, $long) {
		$valid = true; // innocent until proved guilty
		
		if(!is_numeric($lat) || floatval($lat) > 90 || floatval($lat) < -90 )
			$valid = false;
		if(!is_numeric($long) || floatval($long) > 180 || floatval($long) < -180 )
			$valid = false;
			
		return $valid;
	}
	
	/**
	 * Generate Array of Heatbeat members within a radius of location
	 *
	 * @param float/integer $lat (Latitude), float/integer $long (Longitude), float/integer $radius (Radius of Search Circle)
	 * @return string array $locations_array if users are found, FALSE if not.
	 */
	 
	function HeartbeatMatrix($lat, $long, $radius = 0.5) {
		if($this->LatLongValid($lat, $long)) : 
			$db = $this->database_object;
				
			// Emergencie Request to collect region data
			$Regions = new EmergencieRequest('LatLongToLocal');
			$Regions->LatLongToPostCode($lat, $long);
			$postcodeData = $Regions->returnVariable('translated_postcode');
			$region = $postcodeData->adminCode1 . '-' . $postcodeData->adminCode2 . '-' . $postcodeData->adminCode3;
			$ccode = $postcodeData->countryCode;

			$SQL = "SELECT * FROM ".$this->db_prefix."data WHERE CountryCode='".$ccode."' AND Region='".$region."'";
			$heartbeat_action = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
			if($heartbeat_action->execute()) {
				$locations_array = $heartbeat_action->FetchAll();
				if(is_array($locations_array)) {
					$i = 0;
					foreach($locations_array as $location) {
						$locations_array[$i]['distance'] = $this->CalculateDistanceLatLong($long, $lat, $location['Longitude'], $location['Latitude']);
						if($locations_array[$i]['distance'] > $radius)
							$locations_array[$i] = NULL;
						$i++;
					}
					return array_filter($locations_array);
				} return FALSE;
			}
			else return FALSE;
	
		
		endif;
	}
	
	/**
	 * Calculate Distance of 2 points in MILES
	 *
	 * @param float/integer $lon1 (Longitude 1), float/integer $lat1 (Latitude 1), float/integer $lon2 (Longitude 2), float/integer $lat2 (Latitude 2)
	 * @return float/integer $miles if points valid, FALSE if not.
	 */
	 
	function CalculateDistanceLatLong($lon1, $lat1, $lon2, $lat2) {
		
		if($this->LatLongValid($lat1, $lon1) && $this->LatLongValid($lat2, $lon2)) : 
			//Algorithm to calculate distance between 2 sets of points (lat and long)
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			return $miles;
		else: 
			return FALSE;
		endif;
	}
	
	
	/**
	 * Method Add New Alert to the alerts table
	 *
	 * @param string array $personal_data, string array $location, string array $id
	 * @return int last_insert_id (ID for created alert), FALSE if unsuccessful.
	 */
	 
	function CreateNewHeartbeatAlert($personal_data, $location, $id) {
		$id = $id[0];
		
		// verify id is set
		if(trim($id) !== '') {
			$db = $this->database_object;
			
			//check to see if data and location are valid
			if(is_array($personal_data) && is_array($location)) {
				
				//  Find newest of old alerts from this device
				$OldCheck = "SELECT MAX(Time) FROM ".$this->db_prefix . "alerts WHERE UniqueID=?;";
				$heartbeat_old_alerts = $db->prepare($OldCheck, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$params = array($id);
				if($get_old_alerts = $heartbeat_old_alerts->execute( $params )) {
					$old_time = $heartbeat_old_alerts->fetch();
					$new_time = time();
					$time_difference = $new_time - $old_time[0];
					
					// only allow new heartbeat alert after 20 minutes of the last being sent
					if($time_difference > 1200 /* 20 minutes */) {
						$SQL = "INSERT INTO ".$this->db_prefix . "alerts (ID, Name, Gender, Latitude, Longitude, Time, UniqueID) VALUES (NULL, ?, ?, ?, ?, ?, ?); SELECT LAST_INSERT_ID();";
						$heartbeat_alert = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
						$gender = strtoupper($personal_data[1]);
						$params = array($personal_data[0], $gender, $location[0], $location[1], time(), $id);
						if($heartbeat_alert->execute( $params )) return $db->lastInsertId(); // return successfully created entry id
						else return FALSE;
					} else return FALSE; // last alert from this device not long ago enough
				} else return FALSE;
			} else return FALSE;
		} else return FALSE; // no uid set
	}
	
	
	/**
	 * Method to Add Alert Notification to a user's Heartbeat record
	 *
	 * @param integer $ID (id of device's entry in data table), string $UID (device identifier), integer $alert (id of alert entry)
	 * @return bool TRUE if update successful, FALSE if unsuccessful.
	 */
	 
	function AddNewAlertToUser($ID, $UID, $alert) {
		//echo $alert;
		$db = $this->database_object;
		$SQL = "UPDATE ".$this->db_prefix . "data SET Alerts=concat(IFNULL(Alerts,''), '".$alert.",') WHERE ID=? AND UniqueID=?";
		$heartbeat_action = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
		$params = array($ID, $UID);
		if($heartbeat_action->execute( $params )) return TRUE;
		else return FALSE;
	} 
	
	/**
	 * Method to Retrieve the ID of a Devices Alerts
	 *
	 * @param integer $ID (id of device's entry in data table), string $UID (device identifier), integer $alert (id of alert entry)
	 * @return bool TRUE if update successful, FALSE if unsuccessful.
	 */
	 
	function GetAlertsByUID($UID) {
		
		$db = $this->database_object;
		if(trim($UID) !== '') :
			$SQL = "SELECT Alerts FROM ".$this->db_prefix . "data WHERE UniqueID=?";
			$heartbeat_alerts = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
			$params = array($UID);
			if($heartbeat_alerts->execute( $params )) {
				
				if($results = $heartbeat_alerts->FetchAll()) {
				
					$results = $results[0]['Alerts'];
					$results = array_filter(explode(',', $results));
					
					if(is_array($results)) {
						$request_data = '';
						foreach($results as $result) 
							$request_data[] = $this->GetAlertData((int)$result, 43200);
						//PrettyPrint($request_data);
						return $request_data;
					} else return FALSE; // isn't an array
				} else return FALSE; // no results from query
			} else return FALSE; // not executed
		else : 
			return FALSE; // no uid
		endif;
		
	}
	
	
	function GetAlertData($id, $age = 1200) {
		
		$db = $this->database_object;
		if(isset($id)) {
			$SQL = "SELECT * FROM ".$this->db_prefix . "alerts WHERE ID=?";
			$heartbeat_alerts = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
			$params = array($id);
			if($heartbeat_alerts->execute( $params )) {
				if($results = $heartbeat_alerts->FetchAll()) {
					
					$old_time = $results[0]['Time'];
					$new_time = time();
					$time_difference = $new_time - $old_time;
					if($time_difference < $age) {
						
						$result['name'] = $results[0]['Name'];
						$result['gender'] = $results[0]['Gender'];
						$result['lat'] = $results[0]['Latitude'];
						$result['long'] = $results[0]['Longitude'];
						$result['age'] = $time_difference;

						return $result;
					} else return FALSE; // too old, ignore
				} else return FALSE; // no return data from query
			} else return FALSE; // not executed
		} else return FALSE; // no id or not valid
	}
	
	
	
	function CreateUniqueID($first = TRUE, $Token = NULL) {
		// use UUID:v5() for Token
		// use  UUID:v4() for ID 
		$db = $this->database_object;
		$ID = UUID::v4();
		if((!$first && !$Token) || ($first))
			$Token = UUID::v5( UUID_KEY , md5(uniqid(rand(), true)));
		
		$SQL = "SELECT * FROM ".$this->db_prefix . "identifiers WHERE ID=?";
		$heartbeat_identifiers = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
		$params = array($ID);
		if($heartbeat_identifiers->execute( $params )) {

			if(!$result = $heartbeat_identifiers->FetchAll()) {
				$SQL = "INSERT INTO ".$this->db_prefix . "identifiers (ID, Token) VALUES (?, ?);";
				$heartbeat_identifier_create = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				$params = array($ID, $Token);
				if($heartbeat_identifier_create->execute( $params )) {
					$output['ID'] = $ID;
					$output['token'] = $Token;
					echo json_encode($output);
					return $output;
				} else return FALSE;
			} else {
				$this->CreateUniqueID(FALSE, $Token); //ID already exists! Regen.
			}

		} else return FALSE; // not executed

		
		echo $ID;
		echo '<br>';
		echo $Token;
		
	}
	
	
	
	function RemoveID($ID, $Token) {

		$db = $this->database_object;
		$SQL = "DELETE FROM ".$this->db_prefix . "identifiers WHERE ID=? and Token=?";
		$heartbeat_delete = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
		$params = array($ID, $Token);
		if($heartbeat_delete->execute( $params ) !== 0) {
			$SQL = "DELETE FROM ".$this->db_prefix . "data WHERE UniqueID=?";
			$heartbeat_delete_data = $db->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));	
			$params = array($ID);
			$heartbeat_delete_data->execute( $params );
			return TRUE;
		}
		else return FALSE; // not found/error
		
	}
	
 }
 