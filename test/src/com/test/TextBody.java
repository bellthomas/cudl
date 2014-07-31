package com.test;

import java.io.FileOutputStream;

import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

public class TextBody extends ActionBarActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_text_body);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.text_body, menu);
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

	public void messageConfirm ( View view ) {
		
		String file = "messageBody";
		EditText editText = (EditText) findViewById(R.id.editText1);
		String data = editText.getText().toString();
	      try {
	         FileOutputStream fOut = openFileOutput(file,MODE_WORLD_READABLE);
	         fOut.write(data.getBytes());
	         fOut.close();
	         Toast.makeText(getBaseContext(),"Message Text Saved",
	         Toast.LENGTH_SHORT).show();
	      } catch (Exception e) {
	         // TODO Auto-generated catch block
	         e.printStackTrace();
	      }
	   }

		
		
	}
