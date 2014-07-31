<?php
/**
 * Simple class to measure timings of server performance
 *
 */
 
class Performance {
	
	public $start;
	
	function __construct() {
		// timing for dev purposes
		$time = microtime(); 
		$time = explode(" ",$time); 
		$time = $time[1] + $time[0]; 
		$this->start = $time; 
	}
	
	function EndOfScript() {
		
		$time = microtime(); 
		$time = explode(" ",$time); 
		$time = $time[1] + $time[0]; 
		$endtime = $time; 
		$totaltime = ($endtime - $this->start); 
		return $totaltime;
	
	}
	
}
