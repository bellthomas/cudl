package com.test;

import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class JsonHelper {
	
	/**
	 * 
	 * @param Omni-toString function - json_format
	 * @return
	 */
	@SuppressWarnings("rawtypes")
	public static String toString(Object object) {
		if(object instanceof String) return (String) "\""+object+"\"";
		if(object instanceof Integer) return Integer.toString((Integer)object);
		if(object instanceof Float) return Float.toString((Float)object);
		if(object instanceof Boolean) return ((Boolean)object)?"TRUE":"FALSE";
		////////////////////////////////////////////////
		if(object instanceof Map<?,?>) {
			String jmap = "{";
			for(Object key : ((Map)object).keySet()) {
				jmap = jmap + toString(key);
				jmap = jmap + ":";
				jmap = jmap + toString(((Map)object).get(key));
				jmap = jmap + ",";
			}
			jmap = jmap.substring(0, jmap.length()-1);
			jmap = jmap + "}";
			return jmap;
		}
		/////////////////////////////////////////
		if(object.getClass().isArray()) {
			String array = "[";
			for(Object obj : toObjectArray(object)) {
				array = array + toString(obj);
				array = array + ",";
			}
			array = array.substring(0,array.length()-1);
			array = array + "]";
			return array;
		}
		/////////////////////////////////////////
		if(object instanceof Iterable<?>) {
			String array = "[";
			for(Object obj : ((Iterable)object)) {
				array = array + toString(obj);
				array = array + ",";
			}
			array = array.substring(0,array.length()-1);
			array = array + "]";
			return array;
		}
		return object.toString();
	}
	
	
	public static List<List<Map<String,Object>>> getListedListJsons(String string) {
		return null; //TODO
	}
	
	
	//Special json - server => client parsing class - since we use odd formatting
	public static List<Map<String,Object>> getListedJsonsWithCommaFormatting(String string) {
		String[] strings = string.replace("<br>","").split("%%%%");
		List<Map<String,Object>> maps = new ArrayList<Map<String,Object>>();
		for(String json : strings) {
			json = json.substring(1, json.length() - 1);
			Map<String,Object> map = new HashMap<String,Object>();
			String[] sets = json.split(",");
			for(String set : sets) {
				String key = set.split(":")[0];
				String value = set.split(":")[1];
				key = key.substring(1,key.length() - 1).replace("<comma>", ",").replace("<colon>",":").replace(new String(new char[] {(char)92}),"");
				
				if(value.startsWith("\"")) {
					map.put(key, value.substring(1, value.length() - 1).replace("<comma>",",").replace("<colon>",":").replace(new String(new char[] {(char)92}),""));
				}else
				if(value.indexOf(".") != -1) {
					map.put(key, Double.parseDouble(value));
				}else
				if(value == "null") {
					map.put(key, null);
				}
				else {
					map.put(key,Long.parseLong(value));
				}
				//System.out.println(map.get(key));
			}
			maps.add(map);	
		}
		
		return maps;
	}
	
	
	
	
	public static Object[] toObjectArray(Object array) {
	    int length = Array.getLength(array);
	    Object[] ret = new Object[length];
	    for(int i = 0; i < length; i++)
	        ret[i] = Array.get(array, i);
	    return ret;
	}
}