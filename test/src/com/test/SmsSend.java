package com.test;

import java.io.FileInputStream;

import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.telephony.gsm.SmsManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Toast;
import android.widget.EditText;

public class SmsSend extends ActionBarActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_sms_send);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.sms_send, menu);
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
	
	 public void read(View view){
	      try{
	         FileInputStream fin = openFileInput("messageBody");
	         int c;
	         String temp="";
	         while( (c = fin.read()) != -1){
	            temp = temp + Character.toString((char)c);
	         }
	         String textMessage = temp.toString();
	         Toast.makeText(getBaseContext(),"file read",
	         Toast.LENGTH_SHORT).show();

	      }catch(Exception e){

	      }
	   }
		 
			 
			public void sayHello(View v) {
				 
			    String _messageNumber="+447933037883";
			    String messageText = "";
			 
			    SmsManager sms = SmsManager.getDefault();
			    sms.sendTextMessage(_messageNumber, null, messageText, null, null);
			 
			}
	 
	    }
