package com.test.services;

import com.test.MainActivity;

import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;

public class LocationUtil implements LocationListener{
	
	public static final LocationUtil instance = new LocationUtil();
	
	public transient Location best = getNullLocation();
	
	public boolean isEnabled = false;
	
	
	private LocationManager locManager = (LocationManager) MainActivity.instance.getSystemService(Context.LOCATION_SERVICE);
	
	//Interface stuff
	/**
	 * Registers with hyper sensitivity to ensure high accuracy of locating the person in distress
	 * 
	 * @return if it is enabled successfully
	 */
	public void EnableHyper() { //Use for emergencies
		locManager.requestLocationUpdates(LocationManager.GPS_PROVIDER,100,0,this);
		locManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER,100,0,this);
		isEnabled = true;
	}
	
	private Location getNullLocation() {
		Location loc = new Location("");
		loc.setAccuracy(Integer.MAX_VALUE);
		loc.setAltitude(0);
		loc.setLatitude(0);
		loc.setLongitude(0);
		return loc;
	}
	
	
	public void EnableNormal() { //Use for heartbeats - 100 second update interval - leave running in the background
		locManager.requestLocationUpdates(LocationManager.GPS_PROVIDER,1000,0*30,this);
		locManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER,1000*30,0,this);
		isEnabled = true;
	}
	
	public void Disable() {
		locManager.removeUpdates(this);
		isEnabled = false;
	}
	
	
	public void DefaultPinPoint() {
		EnableHyper();
		try {
			Thread.sleep(10000); //TODO check if thread sleeping works
		} catch (InterruptedException e) {}
		Disable();
	}
	
	private int Diff(Location l1, Location l2) {
		final double R = 6378.137;
		double dLat = (l2.getLatitude() - l1.getLatitude()) * Math.PI / 180;
	    double dLon = (l2.getLongitude() - l1.getLongitude()) * Math.PI / 180;
	    double a = Math.sin(dLat/2) * Math.sin(dLat/2) + 
	    		Math.cos(l1.getLatitude() * Math.PI / 180) *
	    		Math.cos(l2.getLatitude() * Math.PI / 180) *
	    		Math.sin(dLon/2) * Math.sin(dLon/2);
	    double c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	    double d = R * c;
	    return (int)d * 1000; // meters
	}
	
	//////////////LocationListener Handler Methods - re-add the overrides 
	
	//@Override
	public void onLocationChanged(Location location) {
		//Check if the new location falls outside of the old range
		/**
		if(Diff(location,best)  > (location.getAccuracy() + best.getAccuracy())) { 
					//Is a new location
					best = location;
		}else{
			//Is similar - check which is better
			if(location.getAccuracy() < best.getAccuracy()) {
				best = location;
			}
		}
		**/
		best = location; //Temp testing stuff - change back later
	}

	//@Override
	public void onProviderDisabled(String provider) {
		//TODO
	}

	//@Override
	public void onProviderEnabled(String provider) {
		//TODO
	}

	//@Override
	public void onStatusChanged(String provider, int status, Bundle extras) {
		//TODO
		
	}
	
	
	
}