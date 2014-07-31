package com.test.services;

import java.io.IOException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Random;
import java.util.UUID;

import android.app.IntentService;
import android.content.Intent;

import com.test.JsonHelper;
import com.test.ServerUtil;


public  class HeartBeatIntentService extends IntentService {

	  /**
	   * A constructor is required, and must call the super IntentService(String)
	   * constructor with a name for the worker thread.
	   */
	  	public HeartBeatIntentService() {
		  super("HeartBeatIntentService");
		  UUID[] ids = loadUUIDs();
		  this.uuid = ids[0];
		  this.auth = ids[1];
		  heartbeat = this;
		  if(1 == 1) {} //TODO remove
	  	}
	  	
	  	/**
	   	* The IntentService calls this method from the default worker thread with
	   	* the intent that started the service. When this method returns, IntentService
	   	* stops the service, as appropriate.
	   	*/
	  	@Override
	  	protected void onHandleIntent(Intent intent) {
	      	// Normally we would do some work here, like download a file.
	      	// For our sample, we just sleep for 5 seconds.
	  		//for(int x = 0; x < 1000; x++) {
	  		//	Logger.getLogger(Logger.GLOBAL_LOGGER_NAME).severe("HeartBeat has LAUNCHED!!!!!!");
	  		//}
	  		//final CountDownLatch latch = new CountDownLatch(100);
	      	while (run) {
	          	//synchronized (this) {
	          		//Beat
	          		beat();
	          		//Wait
	          		try {
	          			Thread.sleep(1000*30);
	          			//latch.await();
						//wait(1000 * 30);//60 * 5);
					} catch (InterruptedException e) {
						//Resumes the service
					}
	          	//}
	      	}
	  	}
	  	/////////////////////////////////////HeartBeat Stuff////////////////////////////////
	  	
	  	
		public static final String AUTHENTICATION = "50ca9c8e-9991-4826-8d04-27f3c18aa912";
		
		public transient static List<Map<String,Object>> notices = new ArrayList<Map<String,Object>>();
		
		//Instance of the beat
		public static HeartBeatIntentService heartbeat= null;
		
		public Random rand = new Random();
		
		private boolean run = true;
		
		public UUID uuid;
		public UUID auth;
		
		@SuppressWarnings("unused")
		private UUID[] loadUUIDs() {
			if(false/**It is saved in a file**/) {
				//Return it in the file     //TODO implement file saving of uuids
			}else{
				try {
					return requestUUIDfromServer();
				} catch (IOException e) {
					e.printStackTrace();
					return null;
				}
			}
			return null;
		}
		
		/**
		public HeartBeat() {
			System.out.println("UUID -> "+id.toString());
			System.out.println("Token ->"+authentication.toString());
			uuid = id;
			auth = authentication;
			run = true;
		}**/
		
		// id then auth-token, use request only once - then save after
		public static UUID[] requestUUIDfromServer() throws IOException {
			String response = ServerUtil.visitURl("http://api.cudl.io/heartbeat/?emie_heartbeat_init=" + AUTHENTICATION);
			Map<String,Object> data = JsonHelper.getListedJsonsWithCommaFormatting(response).get(0);
			return new UUID[] {UUID.fromString((String)data.get("ID")),UUID.fromString((String)data.get("token"))};
		}
		
		@SuppressWarnings("deprecation")
		public String SendPanicToServer(String name, char gender,double latitude, double longitude) throws IOException {
			String url = "http://api.cudl.io/heartbeat/?emie_heartbeat_set_alert=";
			Map<String,Object> data = new HashMap<String,Object>();
			data.put("emie_id",new String[] {uuid.toString(),auth.toString()});
			data.put("emie_personal", new String[] {name,new String(new char[] {gender})});
			data.put("emie_location",new double[] {latitude,longitude});
			return ServerUtil.visitURl(url + URLEncoder.encode(JsonHelper.toString(data)));
		}
		
		@SuppressWarnings("deprecation")
		public List<Map<String, Object>> getAlerts() throws IOException {
			String url = "http://api.cudl.io/heartbeat/?emie_heartbeat_get_alerts=";
			Map<String,Object> data = new HashMap<String,Object>();
			data.put("emie_id",new String[] {uuid.toString(),auth.toString()});
			String result = ServerUtil.visitURl(url + URLEncoder.encode(JsonHelper.toString(data)));
			return JsonHelper.getListedJsonsWithCommaFormatting(result);
		}
		
		@SuppressWarnings("deprecation")
		public void unregister() throws IOException {
			String url = "http://api.cudl.io/heartbeat/?emie_heartbeat_remove=";
			Map<String,Object> data = new HashMap<String,Object>();
			data.put("emie_id",new String[] {uuid.toString(),auth.toString()});
			ServerUtil.visitURl(url + URLEncoder.encode(JsonHelper.toString(data)));
		}
		

		/**private**/public void beat() {
			//TODO Make sure LocationUtil is running updates in the background
			
			try {
				/**String response = **/ServerUtil.visitURl(getURL());
				////
				try{
					List<Map<String,Object>> als = getAlerts();
					for(Map<String,Object> alert : als) {
						notices.add(alert);
					}
					
				}catch(StringIndexOutOfBoundsException e) {}//e.printStackTrace();}
				//System.out.println(JsonHelper.toString(response)); //TODO maybe handle the status of the response
			} catch (Exception e) {
				//e.printStackTrace();
			}
			//TODO handle response - maybe talk to server this way?
		}
		
		
		private String getURL() {
			Random rand = new Random();
			Map<String,Object> data = new HashMap<String,Object>();
			data.put("emie_id",new String[] {uuid.toString(),auth.toString()});
			data.put("emie_location",new double[] {LocationUtil.instance.best.getLatitude(),LocationUtil.instance.best.getLongitude()});
			//data.put("emie_location", new double[] {51.820878,-2.697587}); //TODO hook up locations to not be hardcoded
			//data.put("emie_location", new double[] {(rand.nextDouble()*45D),(rand.nextDouble()*45D)});
			String json = JsonHelper.toString(data);
			@SuppressWarnings("deprecation")
			String encoded = URLEncoder.encode(json);
			return "http://api.cudl.io/heartbeat/?emie_heartbeat="+encoded;
		}
	  
	}
