package com.vadapoche.camark;

import java.io.Serializable;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.Map;

import android.os.Parcel;
import android.os.Parcelable;

public class Marks implements Serializable {

	Map<String, String> child = new LinkedHashMap<String, String>();

	public String toString()
	{
		return child.toString();
	}
}


