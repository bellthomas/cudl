<?php
/**
 * Create Emergencie's Request Object.
 *
 * APIs used: GeonamesAPI (30,000/day, 2,000/hour), NHS Choices API, Google Places API (1,000/day), MedlinePlus
 *
 * @author Harri Bell-Thomas < @harribellthomas >
 */
 
class EmergencieRequest {
	
	public $request_type;
	public $request_notices = NULL;
	protected $allowed_request_types = array('LatLongToLocal',
											 'NearestHospitals',
											 'NearestStreet',
											 'GetArticles'
											);
											
	protected $geonames_username = GEONAMES_USERNAME;
	protected $nhs_choices_api_key = NHS_KEY;
	protected $google_places_api_key = PLACES_API; 
	public $latitude;
	public $longitude;
	
	
	public $translated_postcode = NULL; // null normally, but if lat/long to postcode conversion happens, this holds the return data.
	
	
	/**
	 * Create New Request
	 *
	 * @param string $type Check if this action is valid.
	 * @return bool TRUE if valid request, FALSE if not.
	 */
	function __construct( $type ) {
		
		// If no database connection, abort.
		if(!$GLOBALS['db'] )
			exit();
			
		if(in_array($type, $this->allowed_request_types)) {
			$this->request_type = $type;
			return TRUE;
		} else 
			return FALSE;
	}



	/**
	 * Retrieve Request Notices
	 *
	 * @return string array $request_notices if notices are present, or FALSE if all is sparky.
	 */
	function GetNotices() {
		if($this->request_notices)
			return $this->request_notices;
		else
			return FALSE;
	}



	/**
	 * Retrieve parameters
	 *
	 * @return depends on parameters...
	 */
	function GetParameter($name) {
		if(isset($this->$name)) return $this->$name;
		else return FALSE;
	}



	/**
	 * Assign parameters and generate request URL.
	 *
	 * @param string array $parameters Array of parameters to be parsed.
	 * @return string $RequestURL, valid and to be executed.
	 */

	function RequestParameters($parameters = NULL) {
		$RequestURL = '';
		//PrettyPrint($parameters);
		switch ($this->request_type) :
		
			/**
	 		 * Case: LatLongToLocal
			 *
			 * @param float/integer $parameters['lat'] (Latitude for Request), float/integer $parameters['long'] (Longitude for Request)
			 */
			case 'LatLongToLocal' :
				if($parameters) :
					if(isset($parameters->lat) && isset($parameters->long)) {
						if($this->AreLatAndLongValid($parameters->lat, $parameters->long))
							$RequestURL = 'http://api.geonames.org/findNearbyPlaceNameJSON?lat='.$parameters->lat.'&lng='.$parameters->long. '&username=' . $this->geonames_username;
						else
							$this->request_notices[] = 'LatLongToLocal - lat and long set but not valid.';	
					} else {
						$this->request_notices[] = 'LatLongToLocal - lat and long not set, but parameters variable passed.';	
					}
				else :
					$this->request_notices[] = 'LatLongToLocal - no parameters set.';
				endif;
				break;




		
			/**
	 		 * Case: NearestHospital
			 *
			 * @param float/integer $parameters['lat'] (Latitude for Request), float/integer $parameters['long'] (Longitude for Request)
			 */
			case 'NearestHospitals' :
				$radius = 20000; // in metres
				if($parameters) :
					if(isset($parameters->lat) && isset($parameters->long)) {
						if($this->AreLatAndLongValid($parameters->lat, $parameters->long)){
							$RequestURL = 'https://maps.googleapis.com/maps/api/place/search/json?types=hospital&location='.$parameters->lat.','.$parameters->long. '&radius='.$radius.'&key=' . $this->google_places_api_key;
							$this->latitude = $parameters->lat;
							$this->longitude = $parameters->long;
						}else
							$this->request_notices[] = 'NearestHospital - lat and long set but not valid.';	
					} else {
						$this->request_notices[] = 'NearestHospital - lat and long not set, but parameters variable passed.';	
					}
				else :
					$this->request_notices[] = 'NearestHospital - no parameters set.';
				endif;
				break;


		
			/**
	 		 * Case: GetArticles
			 *
			 * @param string $search (Topic to Search for)
			 */
			case 'GetArticles' :
				if($parameters) :
					if(isset($parameters->search)) {
						$RequestURL = 'http://wsearch.nlm.nih.gov/ws/query?db=healthTopics&term='.urlencode($parameters->search); 
					} else {
						$this->request_notices[] = 'GetArticles - search not set, but parameters variable passed.';	
					}
				else :
					$this->request_notices[] = 'GetArticles - no parameters set.';
				endif;
				break;


		
			/**
	 		 * Case: NearestStreet
			 *
			 * @param float/integer $parameters['lat'] (Latitude for Request), float/integer $parameters['long'] (Longitude for Request)
			 */
			case 'NearestStreet' :
				if($parameters) :
					if(isset($parameters->lat) && isset($parameters->long)) {
						if($this->AreLatAndLongValid($parameters->lat, $parameters->long))
							$RequestURL = 'http://api.geonames.org/findNearbyStreetsOSMJSON?lat='.$parameters->lat.'&lng='.$parameters->long. '&maxRows=1&username=' . $this->geonames_username;
						else
							$this->request_notices[] = 'NearestStreet - lat and long set but not valid.';	
					} else {
						$this->request_notices[] = 'NearestStreet - lat and long not set, but parameters variable passed.';	
					}
				else :
					$this->request_notices[] = 'NearestStreet - no parameters set.';
				endif;
				break;


		
		endswitch;
		
	
		
		return $RequestURL;
	}
	
	
	/**
	 * Execute Generated URL
	 *
	 * @param string $URL URL to be cURL-ed
	 * @return string $output, cURL return data
	 */

	function Execute($URL) {
		
		$output = FALSE;
		
		if(isset($URL) && filter_var($URL, FILTER_VALIDATE_URL)) :
		
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $URL); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$output = curl_exec($ch); 
			curl_close($ch);    

		else:
			$this->request_notices[] = 'Execute - URL passed is NOT valid. No cURL retrieval attempted. URL is ' . $URL;
		endif;
		
		return $output;
	}
	
	
		
	/**
	 * Are Latitude and Longitude Values Valid?
	 *
	 * @param float/integer $lat (Latitude), float/integer $long (Longitude)
	 * @return TRUE if both params are valid, FALSE if not.
	 */

	function AreLatAndLongValid($lat, $long) {
		$valid = true; // innocent until proved guilty
		
		if(!is_numeric($lat) || floatval($lat) > 90 || floatval($lat) < -90 )
			$valid = false;
		if(!is_numeric($long) || floatval($long) > 180 || floatval($long) < -180 )
			$valid = false;
			
		return $valid;
	}
	
	
		
	/**
	 * Convert Latitude and Longitude to Postal Code
	 *
	 * @param float/integer $lat (Latitude), float/integer $long (Longitude)
	 * @return TRUE if successful conversion, string if not. (NOTE to check for success need to use === )
	 *
	 * extension to do - cache the conversion.
	 */

	function LatLongToPostCode($lat, $long) {
		if(isset($lat) && isset($long)) {
			if( $this->AreLatAndLongValid($lat, $long) ) {
				$RequestURL = 'http://api.geonames.org/findNearbyPostalCodesJSON?lat='.$lat.'&lng='.$long.'&username='. $this->geonames_username;
				$return = json_decode($this->Execute($RequestURL));
				$this->translated_postcode = $return->postalCodes[0];
				//PrettyPrint($this->translated_postcode);
				return TRUE;
			} else {
				return 'Invalid Latitude and Longitude';	
			}
		} else { 
			return 'Latitude and Longitude not set.';
		} 
	}
	
	function returnVariable($name) {
		return $this->$name;	
	}
	
	
	
	function GenerateOutput($output) {
		global $db;
		switch ($this->request_type) :
		
			case 'NearestStreet' : 
			
				$output = json_decode($output);
				$returndata['name'] = $output->streetSegment->name; 
				$returndata['distance'] = $output->streetSegment->distance;  //km
				if(!$returndata['name']) $returndata['name'] = 'Data Unavailable';
				if(!$returndata['distance']) $returndata['distance'] = 'Data Unavailable';
				echo json_encode($returndata);
				return $returndata;
				break;
			
			case 'LatLongToLocal' : 
			
				$output = json_decode($output);
				$returndata['name'] = $output->geonames[0]->name; 
				$returndata['distance'] = $output->geonames[0]->distance; //km
				$returndata['countryName'] = $output->geonames[0]->countryName;
				if(!$returndata['name']) $returndata['name'] = 'Data Unavailable';
				if(!$returndata['distance']) $returndata['distance'] = 'Data Unavailable';
				if(!$returndata['countryName']) $returndata['countryName'] = 'Data Unavailable';
				return $returndata;
				break;
			

			case 'NearestHospitals' :
				$radius = 10;
				$output = json_decode($output);
				$output = $output->results;
				$i = 0;

					foreach($output as $data) {
	
						$hospitals[$i]['name'] = $data->name;
						$hospitals[$i]['vicinity'] = $data->vicinity;
						$hospitals[$i]['distance'] = $db->CalculateDistanceLatLong($this->longitude, $this->latitude, $data->geometry->location->lng, $data->geometry->location->lat);
						if($hospitals[$i]['distance'] > $radius) $hospitals[$i] = NULL;
						$i++;
					} 
					$hospitals = array_filter($hospitals);
					//PrettyPrint($hospitals);
					usort($hospitals, 'compareByName');
					
					$i = 0;		
					foreach($hospitals as $hospital) {
						while ($value = current($hospital)) {
							$hospitals[$i][key($hospital)] = str_replace(',', '<comma>', $value);
							next($hospital);
						}					
						echo json_encode($hospitals[$i]);
						echo '%%%%'; // hospital the last element
						$i++;
					}

					return $hospitals;
				break;
				
			case 'GetArticles' :
			
				$p = xml_parser_create();
				xml_parse_into_struct($p, $output, $vals, $index);
				xml_parser_free($p);
				$i = 0;
				$articles = 0;
				$documents = array();
				while($articles <= 2 & $i < 100) :
					if($vals[$i]['tag'] == 'DOCUMENT' && isset($vals[$i]['attributes']) && filter_var($vals[$i]['attributes']['URL'], FILTER_VALIDATE_URL)){
						$documents['article_'.$articles] = $vals[$i]['attributes']['URL'];
						$articles++;
					}
					$i++;
				endwhile;
				$k = 0;
				foreach($documents as $doc) {
					$response['article_'.$k] = str_replace(':', '<colon>', $doc);
					$k++;
				}
				echo json_encode($response);
				return $response;
				break; 
				
			default : 
				return ($output);
				break;
		
		endswitch;
	}
	
		
}
