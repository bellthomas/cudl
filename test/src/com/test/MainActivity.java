package com.test;

import java.io.FileInputStream;
import java.io.IOException;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.telephony.gsm.SmsManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Toast;

import com.test.services.HeartBeatIntentService;
import com.test.services.LocationUtil;


public class MainActivity extends ActionBarActivity {
	
	public static MainActivity instance;
	
	public MainActivity() {
		instance = this;
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		LocationUtil.instance.EnableNormal();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
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
	
public void sendMessage (View view) {
	try {
		HeartBeatIntentService.heartbeat.SendPanicToServer("Harri", 'M',LocationUtil.instance.best.getLatitude(), LocationUtil.instance.best.getLongitude());
	} catch (IOException e1) {
		//e1.printStackTrace();
	}
	
	
	
	//Getting message file
	String temp="";
	try{
        FileInputStream fin = openFileInput("messageBody");
        int c;
        
        while( (c = fin.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String textMessage = temp.toString();
	
	///Getting numbers from file

	
	temp="";
	try{
        FileInputStream fin1 = openFileInput("contact1");
        int c;
        
        while( (c = fin1.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        fin1.close();
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String _messageNumber1 = temp.toString();
	
	temp="";
	try{
        FileInputStream fin2 = openFileInput("contact2");
        int c;
        
        while( (c = fin2.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        fin2.close();
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String _messageNumber2 = temp.toString();
	
	temp="";
	try{
        FileInputStream fin3 = openFileInput("contact3");
        int c;
        
        while( (c = fin3.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        fin3.close();
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String _messageNumber3 = temp.toString();
	
	temp="";
	try{
        FileInputStream fin4 = openFileInput("contact4");
        int c;
        
        while( (c = fin4.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        fin4.close();
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String _messageNumber4 = temp.toString();
	
	temp="";
	try{
        FileInputStream fin5 = openFileInput("contact5");
        int c;
        
        while( (c = fin5.read()) != -1){
           temp = temp + Character.toString((char)c);
        }
        
        fin5.close();
        Toast.makeText(getBaseContext(),"file read",
        Toast.LENGTH_SHORT).show();

     }catch(Exception e){

     }
	
	String _messageNumber5 = temp.toString();
	
	
	//Sending message
	
	
    SmsManager sms = SmsManager.getDefault();
    sms.sendTextMessage(_messageNumber1, null, textMessage, null, null);
    sms = SmsManager.getDefault();
    sms.sendTextMessage(_messageNumber2, null, textMessage, null, null);
    sms = SmsManager.getDefault();
    sms.sendTextMessage(_messageNumber3, null, textMessage, null, null);
    sms = SmsManager.getDefault();
    sms.sendTextMessage(_messageNumber4, null, textMessage, null, null);
    sms = SmsManager.getDefault();
    sms.sendTextMessage(_messageNumber5, null, textMessage, null, null);
    
    Intent intent = new Intent(this, SosSender.class);
    startActivity(intent);
	}
//}


public void openMenu (View view) {
	Intent intent = new Intent (this, Settings.class);
	startActivity(intent);
}

public void HeartBeatLauncher (View view) {
	Intent intent = new Intent(this, HeartBeatIntentService.class);
	startService(intent);
}



}

