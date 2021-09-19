package com.example.freepaidebook.util;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Point;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.text.Html;
import android.util.Log;
import android.view.Display;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import com.example.freepaidebook.R;
import com.example.freepaidebook.activity.MainActivity;
import com.example.freepaidebook.database.DatabaseHandler;
import com.example.freepaidebook.interfaces.FavouriteIF;
import com.example.freepaidebook.interfaces.OnClick;
import com.example.freepaidebook.item.DownloadList;
import com.example.freepaidebook.response.FavouriteRP;
import com.example.freepaidebook.rest.ApiClient;
import com.example.freepaidebook.rest.ApiInterface;
import com.example.freepaidebook.service.DownloadService;
import com.facebook.ads.Ad;
import com.facebook.ads.AdError;
import com.facebook.ads.CacheFlag;
import com.facebook.ads.InterstitialAdListener;
import com.facebook.login.LoginManager;
import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.InterstitialAd;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.auth.api.signin.GoogleSignIn;
import com.google.android.gms.auth.api.signin.GoogleSignInClient;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;
import com.google.gson.Gson;
import com.google.gson.JsonObject;

import org.jetbrains.annotations.NotNull;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class Method {

    public Activity activity;
    private String filename;
    private DatabaseHandler db;
    public static boolean loginBack = false, personalizationAd = false, isDownload = true;

    public SharedPreferences pref;
    public SharedPreferences.Editor editor;
    private final String myPreference = "login";
    public String pref_login = "pref_login";
    private String firstTime = "firstTime";
    public String profileId = "profileId";
    public String userImage = "userImage";
    public String loginType = "loginType";
    public String show_login = "show_login";
    public String notification = "notification";
    public String themSetting = "them";

    private OnClick onClick;

    @SuppressLint("CommitPrefEdits")
    public Method(Activity activity) {
        this.activity = activity;
        pref = activity.getSharedPreferences(myPreference, 0); // 0 - for private mode
        editor = pref.edit();
        db = new DatabaseHandler(activity);
    }

    @SuppressLint("CommitPrefEdits")
    public Method(Activity activity, OnClick onClick) {
        this.activity = activity;
        this.onClick = onClick;
        pref = activity.getSharedPreferences(myPreference, 0); // 0 - for private mode
        editor = pref.edit();
        db = new DatabaseHandler(activity);
    }

    public void login() {
        if (!pref.getBoolean(firstTime, false)) {
            editor.putBoolean(pref_login, false);
            editor.putBoolean(firstTime, true);
            editor.commit();
        }
    }

    //user login or not
    public boolean isLogin() {
        return pref.getBoolean(pref_login, false);
    }

    //get login type
    public String getLoginType() {
        return pref.getString(loginType, "");
    }

    //get user id
    public String userId() {
        return pref.getString(profileId, null);
    }

    //get user image
    public String getUserImage() {
        return pref.getString(userImage, "");
    }

    //get device id
    @SuppressLint("HardwareIds")
    public String getDeviceId() {
        String deviceId;
        try {
            deviceId = Settings.Secure.getString(activity.getContentResolver(), Settings.Secure.ANDROID_ID);
        } catch (Exception e) {
            deviceId = "NotFound";
        }
        return deviceId;
    }

    //rtl
    public void forceRTLIfSupported() {
        if (activity.getResources().getString(R.string.isRTL).equals("true")) {
            activity.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }
    }

    public String themMode() {
        return pref.getString(themSetting, "system");
    }

    //rtl
    public boolean isRtl() {
        return activity.getResources().getString(R.string.isRTL).equals("true");
    }

    //network check
    public boolean isNetworkAvailable() {
        ConnectivityManager connectivityManager
                = (ConnectivityManager) activity.getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
        return activeNetworkInfo != null && activeNetworkInfo.isConnected();
    }

    public void changeStatusBarColor() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = activity.getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.TRANSPARENT);
        }
    }

    //get screen width
    public int getScreenWidth() {
        int columnWidth;
        WindowManager wm = (WindowManager) activity
                .getSystemService(Context.WINDOW_SERVICE);
        Display display = wm.getDefaultDisplay();

        final Point point = new Point();

        point.x = display.getWidth();
        point.y = display.getHeight();

        columnWidth = point.x;
        return columnWidth;
    }

    /*<---------------------- download book ---------------------->*/

    public void download(String id, String bookName, String bookImage, String bookAuthor, String bookUrl, String type) {

        //Book file save folder name
        File root_book = new File(Constant.bookPath);
        if (!root_book.exists()) {
            root_book.mkdirs();
        }

        if (type.equals("epub")) {
            filename = "filename-" + id + ".epub";
        } else {
            filename = "filename-" + id + ".pdf";
        }

        File file = new File(Constant.bookPath, filename);
        if (!file.exists()) {

            Method.isDownload = false;

            Intent serviceIntent = new Intent(activity, DownloadService.class);
            serviceIntent.setAction(DownloadService.ACTION_START);
            serviceIntent.putExtra("id", id);
            serviceIntent.putExtra("downloadUrl", bookUrl);
            serviceIntent.putExtra("file_path", root_book.toString());
            serviceIntent.putExtra("file_name", filename);

            activity.startService(serviceIntent);

        } else {
            alertBox(activity.getResources().getString(R.string.you_have_allReady_download_book));
        }

        new DownloadImage().execute(bookImage, id, bookName, bookAuthor);

    }

    @SuppressLint("StaticFieldLeak")
    public class DownloadImage extends AsyncTask<String, String, String> {

        private String filePath;
        private String iconsStoragePath;
        private String id, bookName, bookAuthor;

        @Override
        protected String doInBackground(String... params) {
            try {

                URL url = new URL(params[0]);
                id = params[1];
                bookName = params[2];
                bookAuthor = params[3];

                iconsStoragePath = Constant.bookPath;
                File sdIconStorageDir = new File(iconsStoragePath);
                //create storage directories, if they don't exist
                if (!sdIconStorageDir.exists()) {
                    sdIconStorageDir.mkdirs();
                }

                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setDoInput(true);
                connection.connect();
                InputStream input = connection.getInputStream();
                Bitmap bitmap = BitmapFactory.decodeStream(input);

                String fname = "Image-" + id;
                filePath = iconsStoragePath + fname + ".jpg";

                File file = new File(iconsStoragePath, filePath);
                if (file.exists()) {
                    Log.d("file_exists", "file_exists");
                } else {
                    try {
                        FileOutputStream fileOutputStream = new FileOutputStream(filePath);
                        BufferedOutputStream bos = new BufferedOutputStream(fileOutputStream);
                        //choose another format if PNG doesn't suit you
                        bitmap.compress(Bitmap.CompressFormat.JPEG, 100, bos);
                        bos.flush();
                        bos.close();
                    } catch (IOException e) {
                        Log.w("TAG", "Error saving image file: " + e.getMessage());
                    }
                }

            } catch (IOException e) {
                // Log exception
            }
            return null;
        }

        @Override
        protected void onPostExecute(String s) {
            if (db.checkIdDownloadBook(id)) {
                db.addDownload(new DownloadList(id, bookName, filePath, bookAuthor, iconsStoragePath + filename));
            }
            super.onPostExecute(s);
        }

    }

    /*<---------------------- download book ---------------------->*/


    //add to favourite
    public void addToFav(String id, String userId, FavouriteIF favouriteIF) {

        ProgressDialog progressDialog = new ProgressDialog(activity);

        progressDialog.show();
        progressDialog.setMessage(activity.getResources().getString(R.string.loading));
        progressDialog.setCancelable(false);

        JsonObject jsObj = (JsonObject) new Gson().toJsonTree(new API(activity));
        jsObj.addProperty("book_id", id);
        jsObj.addProperty("user_id", userId);
        jsObj.addProperty("method_name", "book_favourite");
        ApiInterface apiService = ApiClient.getClient().create(ApiInterface.class);
        Call<FavouriteRP> call = apiService.getFavouriteBook(API.toBase64(jsObj.toString()));
        call.enqueue(new Callback<FavouriteRP>() {
            @Override
            public void onResponse(@NotNull Call<FavouriteRP> call, @NotNull Response<FavouriteRP> response) {

                try {
                    FavouriteRP favouriteRP = response.body();
                    assert favouriteRP != null;

                    if (favouriteRP.getStatus().equals("1")) {
                        if (favouriteRP.getSuccess().equals("1")) {
                            favouriteIF.isFavourite(favouriteRP.getIs_favourite(), favouriteRP.getMsg());
                        } else {
                            favouriteIF.isFavourite("", favouriteRP.getMsg());
                        }
                        Toast.makeText(activity, favouriteRP.getMsg(), Toast.LENGTH_SHORT).show();
                    } else {
                        alertBox(favouriteRP.getMessage());
                    }

                } catch (Exception e) {
                    Log.d("exception_error", e.toString());
                    alertBox(activity.getResources().getString(R.string.failed_try_again));
                }

                progressDialog.dismiss();

            }

            @Override
            public void onFailure(@NotNull Call<FavouriteRP> call, @NotNull Throwable t) {
                // Log error here since request failed
                Log.e("fail", t.toString());
                progressDialog.dismiss();
                alertBox(activity.getResources().getString(R.string.failed_try_again));
            }
        });

    }

    //---------------Interstitial Ad---------------//

    public void onClickAd(final int position, final String type, final String id, final String subId, final String title, final String fileType, final String fileUrl, String otherData) {

        ProgressDialog progressDialog = new ProgressDialog(activity);
        progressDialog.show();
        progressDialog.setMessage(activity.getResources().getString(R.string.loading));
        progressDialog.setCancelable(false);

        if (Constant.appRP != null) {

            if (Constant.appRP.isInterstitial_ad()) {

                Constant.AD_COUNT = Constant.AD_COUNT + 1;
                if (Constant.AD_COUNT == Constant.AD_COUNT_SHOW) {
                    Constant.AD_COUNT = 0;

                    if (Constant.appRP.getInterstitial_ad_type().equals("admob")) {

                        final InterstitialAd interstitialAd = new InterstitialAd(activity);
                        AdRequest adRequest;
                        if (personalizationAd) {
                            adRequest = new AdRequest.Builder()
                                    .build();
                        } else {
                            Bundle extras = new Bundle();
                            extras.putString("npa", "1");
                            adRequest = new AdRequest.Builder()
                                    .addNetworkExtrasBundle(AdMobAdapter.class, extras)
                                    .build();
                        }
                        interstitialAd.setAdUnitId(Constant.appRP.getInterstitial_ad_id());
                        interstitialAd.loadAd(adRequest);
                        interstitialAd.setAdListener(new AdListener() {
                            @Override
                            public void onAdLoaded() {
                                super.onAdLoaded();
                                progressDialog.dismiss();
                                interstitialAd.show();
                            }

                            public void onAdClosed() {
                                onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
                                super.onAdClosed();
                            }

                            @Override
                            public void onAdFailedToLoad(LoadAdError adError) {
                                progressDialog.dismiss();
                                onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
                            }

                        });

                    } else {

                        com.facebook.ads.InterstitialAd interstitialAd = new com.facebook.ads.InterstitialAd(activity, Constant.appRP.getInterstitial_ad_id());
                        InterstitialAdListener interstitialAdListener = new InterstitialAdListener() {
                            @Override
                            public void onInterstitialDisplayed(Ad ad) {
                                // Interstitial ad displayed callback
                                Log.e("fb_ad", "Interstitial ad displayed.");
                            }

                            @Override
                            public void onInterstitialDismissed(Ad ad) {
                                // Interstitial dismissed callback
                                progressDialog.dismiss();
                                onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
                                Log.e("fb_ad", "Interstitial ad dismissed.");
                            }

                            @Override
                            public void onError(Ad ad, AdError adError) {
                                // Ad error callback
                                progressDialog.dismiss();
                                onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
                                Log.e("fb_ad", "Interstitial ad failed to load: " + adError.getErrorMessage());
                            }

                            @Override
                            public void onAdLoaded(Ad ad) {
                                // Interstitial ad is loaded and ready to be displayed
                                Log.d("fb_ad", "Interstitial ad is loaded and ready to be displayed!");
                                progressDialog.dismiss();
                                // Show the ad
                                interstitialAd.show();
                            }

                            @Override
                            public void onAdClicked(Ad ad) {
                                // Ad clicked callback
                                Log.d("fb_ad", "Interstitial ad clicked!");
                            }

                            @Override
                            public void onLoggingImpression(Ad ad) {
                                // Ad impression logged callback
                                Log.d("fb_ad", "Interstitial ad impression logged!");
                            }
                        };

                        // For auto play video ads, it's recommended to load the ad
                        // at least 30 seconds before it is shown
                        com.facebook.ads.InterstitialAd.InterstitialLoadAdConfig loadAdConfig = interstitialAd.buildLoadAdConfig().
                                withAdListener(interstitialAdListener).withCacheFlags(CacheFlag.ALL).build();
                        interstitialAd.loadAd(loadAdConfig);

                    }

                } else {
                    progressDialog.dismiss();
                    onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
                }

            } else {
                progressDialog.dismiss();
                onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
            }
        } else {
            progressDialog.dismiss();
            onClick.position(position, type, id, subId, title, fileType, fileUrl, otherData);
        }
    }

    //---------------Interstitial Ad---------------//

    //---------------Banner Ad---------------//

    public void adView(LinearLayout linearLayout) {
        if (Constant.appRP != null) {
            if (Constant.appRP.isBanner_ad()) {
                if (Constant.appRP.getBanner_ad_type().equals("admob")) {
                    if (personalizationAd) {
                        showPersonalizedAds(linearLayout);
                    } else {
                        showNonPersonalizedAds(linearLayout);
                    }
                } else {
                    FbBannerAd(linearLayout);
                }
            } else {
                linearLayout.setVisibility(View.GONE);
            }
        } else {
            linearLayout.setVisibility(View.GONE);
        }
    }

    public void FbBannerAd(LinearLayout linearLayout) {
        if (Constant.appRP != null) {
            if (Constant.appRP.isBanner_ad()) {
                com.facebook.ads.AdView adView = new com.facebook.ads.AdView(activity, Constant.appRP.getBanner_ad_id(), com.facebook.ads.AdSize.BANNER_HEIGHT_50);
                // Find the Ad Container
                // Add the ad view to your activity layout
                linearLayout.addView(adView);
                // Request an ad
                adView.loadAd();
            } else {
                linearLayout.setVisibility(View.GONE);
            }
        } else {
            linearLayout.setVisibility(View.GONE);
        }
    }

    public void showNonPersonalizedAds(LinearLayout linearLayout) {

        if (Constant.appRP != null) {
            if (Constant.appRP.isBanner_ad()) {

                Bundle extras = new Bundle();
                extras.putString("npa", "1");

                AdView adView = new AdView(activity);
                AdRequest adRequest = new AdRequest.Builder()
                        .addNetworkExtrasBundle(AdMobAdapter.class, extras)
                        .build();
                adView.setAdUnitId(Constant.appRP.getBanner_ad_id());
                adView.setAdSize(AdSize.BANNER);
                linearLayout.addView(adView);
                adView.loadAd(adRequest);
            } else {
                linearLayout.setVisibility(View.GONE);
            }
        } else {
            linearLayout.setVisibility(View.GONE);
        }

    }

    public void showPersonalizedAds(LinearLayout linearLayout) {

        if (Constant.appRP != null) {
            if (Constant.appRP.isBanner_ad()) {
                AdView adView = new AdView(activity);
                AdRequest adRequest = new AdRequest.Builder()
                        .build();
                adView.setAdUnitId(Constant.appRP.getBanner_ad_id());
                adView.setAdSize(AdSize.BANNER);
                linearLayout.addView(adView);
                adView.loadAd(adRequest);
            } else {
                linearLayout.setVisibility(View.GONE);
            }
        } else {
            linearLayout.setVisibility(View.GONE);
        }

    }

    //---------------Banner Ad---------------//


    // view format
    public static String Format(Integer number) {
        String[] suffix = new String[]{"k", "m", "b", "t"};
        int size = (number != 0) ? (int) Math.log10(number) : 0;
        if (size >= 3) {
            while (size % 3 != 0) {
                size = size - 1;
            }
        }
        double notation = Math.pow(10, size);
        String result = (size >= 3) ? +(Math.round((number / notation) * 100) / 100.0d) + suffix[(size / 3) - 1] : +number + "";
        return result;
    }

    //alert message box
    public void alertBox(String message) {

        try {
            if (activity != null) {
                if (!activity.isFinishing()) {
                    MaterialAlertDialogBuilder builder = new MaterialAlertDialogBuilder(activity, R.style.DialogTitleTextStyle);
                    builder.setMessage(Html.fromHtml(message));
                    builder.setCancelable(false);
                    builder.setPositiveButton(activity.getResources().getString(R.string.ok),
                            (arg0, arg1) -> {

                            });

                    AlertDialog alertDialog = builder.create();
                    alertDialog.show();
                }
            }
        } catch (Exception e) {
            Log.d("error_message", e.toString());
        }

    }

    //account suspend
    public void suspend(String message) {

        if (isLogin()) {

            String loginType = getLoginType();
            assert loginType != null;
            if (loginType.equals("google")) {

                // Configure sign-in to request the user's ID, email address, and basic
                // profile. ID and basic profile are included in DEFAULT_SIGN_IN.
                GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                        .requestEmail()
                        .build();

                // Build a GoogleSignInClient with the options specified by gso.
                GoogleSignInClient mGoogleSignInClient = GoogleSignIn.getClient(activity, gso);
                mGoogleSignInClient.signOut()
                        .addOnCompleteListener(activity, task -> {

                        });
            } else if (loginType.equals("facebook")) {
                LoginManager.getInstance().logOut();
            }

            editor.putBoolean(pref_login, false);
            editor.commit();
            Events.Login loginNotify = new Events.Login("");
            GlobalBus.getBus().post(loginNotify);
        }

        try {
            if (activity != null) {
                if (!activity.isFinishing()) {
                    MaterialAlertDialogBuilder builder = new MaterialAlertDialogBuilder(activity, R.style.DialogTitleTextStyle);
                    builder.setMessage(Html.fromHtml(message));
                    builder.setCancelable(false);
                    builder.setPositiveButton(activity.getResources().getString(R.string.ok),
                            (arg0, arg1) -> {
                                activity.startActivity(new Intent(activity, MainActivity.class));
                                activity.finishAffinity();
                            });

                    AlertDialog alertDialog = builder.create();
                    alertDialog.show();
                }
            }
        } catch (Exception e) {
            Log.d("error_message", e.toString());
        }

    }

    public String webViewText() {
        String color;
        if (isDarkMode()) {
            color = Constant.webViewTextDark;
        } else {
            color = Constant.webViewText;
        }
        return color;
    }

    public String webViewLink() {
        String color;
        if (isDarkMode()) {
            color = Constant.webViewLinkDark;
        } else {
            color = Constant.webViewLink;
        }
        return color;
    }

    public String imageGalleryToolBar() {
        String color;
        if (isDarkMode()) {
            color = Constant.darkGallery;
        } else {
            color = Constant.lightGallery;
        }
        return color;
    }

    public String imageGalleryProgressBar() {
        String color;
        if (isDarkMode()) {
            color = Constant.progressBarDarkGallery;
        } else {
            color = Constant.progressBarLightGallery;
        }
        return color;
    }

    //check dark mode or not
    public boolean isDarkMode() {
        int currentNightMode = activity.getResources().getConfiguration().uiMode & Configuration.UI_MODE_NIGHT_MASK;
        switch (currentNightMode) {
            case Configuration.UI_MODE_NIGHT_NO:
                // Night mode is not active, we're using the light theme
                return false;
            case Configuration.UI_MODE_NIGHT_YES:
                // Night mode is active, we're using dark theme
                return true;
            default:
                return false;
        }
    }


}
