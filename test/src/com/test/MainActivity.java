package com.test;

import java.io.FileInputStream;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.telephony.gsm.SmsManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Toast;
import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class MainActivity extends ActionBarActivity {
	
	public static MainActivity instance;
	
	public MainActivity() {
		instance = this;
	}
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
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
	}
//}


public void openMenu (View view) {
	Intent intent = new Intent (this, Settings.class);
	startActivity(intent);
}

public void openMap (View view){
	Intent intent = new Intent(android.content.Intent.ACTION_VIEW, 
		    Uri.parse("http://maps.google.com/maps?saddr=My Location&daddr=Lydney & District Hospital, Grove Road, Lydney"));
		startActivity(intent);
}

public void openServerUtil (View view) {
	Intent intent = new Intent(this, ServerUtil.class);
	startActivity(intent);
}

}

