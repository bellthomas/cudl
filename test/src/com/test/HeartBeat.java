package com.test;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;
import java.util.UUID;
import java.util.logging.Logger;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
// Inflate the menu; this adds items to the action bar if it is present.
// Handle action bar item clicks here. The action bar will
// automatically handle clicks on the Home/Up button, so long
// as you specify a parent activity in AndroidManifest.xml.
public class HeartBeat extends ActionBarActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_heart_beat);
		runBeat();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.heart_beat, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	
	@SuppressWarnings("deprecation")
	public static String JsonToUrl(String url,Map<String,Object> data) throws JSONException, IOException { //Version for android
		//Calculate the url to visit
		
		JSONObject json = new JSONObject();
		for(String key : data.keySet()) {
			json.put(key, data.get(key));
		}
		
		
		for(int x = 0; x < 1000; x++ ) {
			Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).severe("JSON = " + json.toString());
		}
		
		String encodedJSON = URLEncoder.encode(json.toString());
		URL visit = new URL(url + encodedJSON);
		
		
		
		//Open connections
		
		URLConnection conn = visit.openConnection();
		conn.connect();
		InputStream stream = conn.getInputStream();
		
		//Read stream
		
		int nRead;
		byte[] bytes = new byte[16384];
		ByteArrayOutputStream buffer = new ByteArrayOutputStream();

		while ((nRead = stream.read(bytes, 0, bytes.length)) != -1) {
		  buffer.write(bytes, 0, nRead);
		}
		buffer.flush();
		
		//Parse response to string
		
		String out = "";
		for(byte BYTE : buffer.toByteArray()) {
			out = out + ((char)BYTE);
		}
		
		
		return out;
	}
	


	public static class LocationUtil implements LocationListener{
		
		public static final LocationUtil instance = new LocationUtil();
		
		public Location best = null;
		
		public boolean isEnabled = false;
		
		
		private LocationManager locManager = (LocationManager) MainActivity.instance.getSystemService(Context.LOCATION_SERVICE);
		
		//Interface stuff
		/**
		 * Registers with hyper sensitivity to ensure high accuracy of locating the person in distress
		 * 
		 * @return if it is enabled successfully
		 */
		public void Enable() {
			locManager.requestLocationUpdates(LocationManager.GPS_PROVIDER,100,0,this);
			locManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER,100,0,this);
			isEnabled = true;
		}
		
		public void Disable() {
			locManager.removeUpdates(this);
			isEnabled = false;
		}
		
		
		public void DefaultPinPoint() {
			Enable();
			try {
				Thread.sleep(10000);
			} catch (InterruptedException e) {}
			Disable();
		}
		
		
		
		//////////////LocationListener Handler Methods
		
		@Override
		public void onLocationChanged(Location location) {
			//Check if the new location falls outside of the old range
			if((Math.pow(location.getLatitude() - best.getLatitude(),2)
					+ Math.pow(location.getLongitude() - best.getLongitude(),2)) 
					> Math.pow(location.getAccuracy() + best.getAccuracy(),2)) {
						//Is a new location
						best = location;
			}else{
				//Is similar - check which is better
				if(location.getAccuracy() < best.getAccuracy()) {
					best = location;
				}
			}
		}

		@Override
		public void onProviderDisabled(String provider) {
			//TODO
		}

		@Override
		public void onProviderEnabled(String provider) {
			//TODO
		}

		@Override
		public void onStatusChanged(String provider, int status, Bundle extras) {
			//TODO
			
		}
		
		
		

		
	}
	
	
	@SuppressLint("NewApi")
	public boolean runBeat() {
		//TODO ensure location
		//TEst
		//LocationUtil.instance.DefaultPinPoint();
		//TODO remove ping later and incorporate
		UUID uuid = UUID.randomUUID();
		Map<String,Object> data = new HashMap<String,Object>();
		try {
			data.put("emie_heartbeat",new JSONArray(new long[] {uuid.getMostSignificantBits(),uuid.getLeastSignificantBits()}));
		data.put("emie_location",new JSONArray(new double[] {11D,52D})); //{LocationUtil.instance.best.getLatitude(),LocationUtil.instance.best.getLongitude()});,
		}catch(JSONException e) {e.printStackTrace(); }
		try {
			
			String response = JsonToUrl("http://emergencie.hbt.io/heartbeat?emie_heartbeat=", data);
			
			//TODO handle the response from the server
			for(int x = 0; x < 1000; x++ ) {
				Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).severe("Response = " + response);
			}
			return true;
		} catch (ClientProtocolException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return false;
	}

	
	
}


