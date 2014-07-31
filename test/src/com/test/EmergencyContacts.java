package com.test;

import java.io.FileOutputStream;

import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;
import java.io.*;


public class EmergencyContacts extends ActionBarActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_emergency_contacts);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.emergency_contacts, menu);
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
	
	public void submitContacts (View view){
		String file1 = "contact1";
		String file2 = "contact2";
		String file3 = "contact3";
		String file4 = "contact4";
		String file5 = "contact5";
		EditText editText1 = (EditText) findViewById(R.id.editText1);
		EditText editText2 = (EditText) findViewById(R.id.editText2);
		EditText editText3 = (EditText) findViewById(R.id.editText3);
		EditText editText4 = (EditText) findViewById(R.id.editText4);
		EditText editText5 = (EditText) findViewById(R.id.editText5);
		String data1 = editText1.getText().toString();
		String data2 = editText2.getText().toString();
		String data3 = editText3.getText().toString();
		String data4 = editText4.getText().toString();
		String data5 = editText5.getText().toString();
	      try {
	         FileOutputStream fOut1 = openFileOutput(file1,MODE_WORLD_READABLE);
	         fOut1.write(data1.getBytes());
	         fOut1.close();
	         
	         FileOutputStream fOut2 = openFileOutput(file2,MODE_WORLD_READABLE);
	         fOut2.write(data2.getBytes());
	         fOut2.close();
	         
	         FileOutputStream fOut3 = openFileOutput(file3,MODE_WORLD_READABLE);
	         fOut3.write(data3.getBytes());
	         fOut3.close();
	         
	         FileOutputStream fOut4 = openFileOutput(file4,MODE_WORLD_READABLE);
	         fOut4.write(data4.getBytes());
	         fOut4.close();
	         
	         FileOutputStream fOut5 = openFileOutput(file5,MODE_WORLD_READABLE);
	         fOut5.write(data5.getBytes());
	         fOut5.close();
	         
	         Toast.makeText(getBaseContext(),"Contacts Saved",
	         Toast.LENGTH_SHORT).show();
	      } catch (Exception e) {
	         // TODO Auto-generated catch block
	         e.printStackTrace();
	      }
	   }
	}
