package com.example.freepaidebook.util;

import android.os.Environment;

import com.example.freepaidebook.response.AppRP;

public class Constant {

    //upload image gallery section
    public static String lightGallery = "#5387ED";
    public static String darkGallery = "#000000";
    public static String progressBarLightGallery = "#5387ED";
    public static String progressBarDarkGallery = "#FFFFFF";

    //Change WebView text color light and dark mode
    public static String webViewText = "#8b8b8b;";
    public static String webViewTextDark = "#FFFFFF;";

    //Change WebView link color light and dark mode
    public static String webViewLink = "#0782C1;";
    public static String webViewLinkDark = "#5387ED;";

    public static int AD_COUNT = 0;
    public static int AD_COUNT_SHOW = 0;

    public static AppRP appRP;

    //book storage folder path
    public static String bookPath = Environment.getExternalStorageDirectory() + "/AndroidEBook/";

}
