package com.scriptsbundle.adforest;

import android.app.Activity;
import android.app.TaskStackBuilder;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.content.res.Configuration;
import android.os.Bundle;
import android.os.Handler;
import android.preference.PreferenceManager;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import android.util.DisplayMetrics;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import com.scriptsbundle.adforest.home.ChooseLocationActivity;
import com.scriptsbundle.adforest.messages.ChatActivity;

import com.google.android.material.snackbar.Snackbar;

import com.google.firebase.messaging.RemoteMessage;
import com.loopj.android.http.Base64;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import com.scriptsbundle.adforest.Shop.shopMenuModel;
import com.scriptsbundle.adforest.helper.LocaleHelper;
import com.scriptsbundle.adforest.home.ChooseLanguageActivity;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.home.helper.AdPostImageModel;
import com.scriptsbundle.adforest.home.helper.CalanderTextModel;
import com.scriptsbundle.adforest.home.helper.Location_popupModel;
import com.scriptsbundle.adforest.home.helper.ProgressModel;
import com.scriptsbundle.adforest.modelsList.permissionsModel;
import com.scriptsbundle.adforest.packages.adapter.PaymentToastsModel;
import com.scriptsbundle.adforest.profile.Model.OTPModel;
import com.scriptsbundle.adforest.signinorup.MainActivity;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

public class SplashScreen extends AppCompatActivity  {

    public static JSONObject jsonObjectAppRating, jsonObjectAppShare;
    public static boolean gmap_has_countries = false, app_show_languages = false;
    public static JSONArray app_languages;
    public static String languagePopupTitle, languagePopupClose, gmap_countries;
    Activity activity;
    SettingsMain setting;
    JSONObject jsonObjectSetting;
    boolean isRTL = false;
    boolean isWhatsApp = false;
    String gmap_lang;
    SharedPreferences prefs;
//    private static final int REQ_CODE_VERSION_UPDATE = 530;
//    private InAppUpdateManager inAppUpdateManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash_screen);
        Configuration configuration = getResources().getConfiguration();
        configuration.fontScale = (float) 1; //0.85 small size, 1 normal size, 1,15 big etc

//        if (getIntent().hasExtra("data")){
//            try {
//                JSONObject jsonObject = new JSONObject(getIntent().getExtras().getString("data"));
//                Intent in = new Intent(this, ChatActivity.class);
//                in.putExtra("adId", jsonObject.getString("ad_id"));
//                in.putExtra("senderId", jsonObject.getString("senderId"));
//                in.putExtra("recieverId", jsonObject.getString("recieverId"));
//                in.putExtra("type", jsonObject.getString("type"));
//                startActivity(in);
//                finish();
//            } catch (JSONException e) {
//                e.printStackTrace();
//            }
//        }else{
        DisplayMetrics metrics = new DisplayMetrics();
        getWindowManager().getDefaultDisplay().getMetrics(metrics);
        metrics.scaledDensity = configuration.fontScale * metrics.density;
        getBaseContext().getResources().updateConfiguration(configuration, metrics);

        activity = this;
        setting = new SettingsMain(this);
        prefs = PreferenceManager.getDefaultSharedPreferences(this);


        if (getSupportActionBar() != null) {
            getSupportActionBar().hide();
        }


        try {
            PackageInfo info = getPackageManager().getPackageInfo(
                    "com.scriptsbundle.adforest",
                    PackageManager.GET_SIGNATURES);
            for (Signature signature : info.signatures) {
                MessageDigest md = MessageDigest.getInstance("SHA");
                md.update(signature.toByteArray());
                Log.d("KeyHash:", Base64.encodeToString(md.digest(), Base64.DEFAULT));
            }
        } catch (NoSuchAlgorithmException e) {

        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();
        }

        if (SettingsMain.isConnectingToInternet(this)) {
            adforest_getSettings();
        } else {
            AlertDialog.Builder alert = new AlertDialog.Builder(SplashScreen.this);
            alert.setTitle(setting.getAlertDialogTitle("error"));
            alert.setCancelable(false);
            alert.setMessage(setting.getAlertDialogMessage("internetMessage"));
            alert.setPositiveButton(setting.getAlertOkText(), (dialog, which) -> {
                dialog.dismiss();
                SplashScreen.this.recreate();
            });
            alert.show();
        }
        //checkUpdate();
//        }


    }

//    public void checkUpdate() {
//
//        InAppUpdateManager inAppUpdateManager = InAppUpdateManager.Builder(this, REQ_CODE_VERSION_UPDATE)
//                .resumeUpdates(true) // Resume the update, if the update was stalled. Default is true
//                .mode(Constants.UpdateMode.IMMEDIATE)
//                .snackBarMessage("An update has just been downloaded.")
//                .snackBarAction("RESTART")
//                .handler(this);
//
//        inAppUpdateManager.checkForAppUpdate();
//
//    }

    public void adforest_getSettings() {
        RestService restService =
                UrlController.createService(RestService.class);
        try {

            Call<ResponseBody> myCall = restService.getSettings(UrlController.AddHeaders(this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info settings Responce", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                jsonObjectSetting = response.getJSONObject("data");
                                Log.d("info settings Responce", "" + jsonObjectSetting.toString());

                                setting.setMainColor(jsonObjectSetting.getString("main_color"));

                                isRTL = jsonObjectSetting.getBoolean("is_rtl");
                                setting.setRTL(isRTL);
                                isWhatsApp = jsonObjectSetting.getBoolean("is_whatsapp");
                                setting.setWhatsApp(isWhatsApp);
                                setting.setAlertDialogTitle("error", jsonObjectSetting.getJSONObject("internet_dialog").getString("title"));
                                setting.setAlertDialogMessage("internetMessage", jsonObjectSetting.getJSONObject("internet_dialog").getString("text"));
                                setting.setAlertOkText(jsonObjectSetting.getJSONObject("internet_dialog").getString("ok_btn"));
                                setting.setAlertCancelText(jsonObjectSetting.getJSONObject("internet_dialog").getString("cancel_btn"));
                                setting.setPaidMessage(jsonObjectSetting.getString("app_paid_cat_text"));
                                setting.setImgReqMessage(jsonObjectSetting.getString("required_img"));
                                setting.setLinkednText(jsonObjectSetting.getString("linkedin_login_label"));
                                setting.setAlertDialogTitle("info", jsonObjectSetting.getJSONObject("alert_dialog").getString("title"));
                                setting.setAlertDialogMessage("confirmMessage", jsonObjectSetting.getJSONObject("alert_dialog").getString("message"));

                                setting.setAlertDialogMessage("waitMessage", jsonObjectSetting.getString("message"));

                                setting.setAlertDialogMessage("search", jsonObjectSetting.getJSONObject("search").getString("text"));
                                setting.setAlertDialogMessage("catId", jsonObjectSetting.getString("cat_input"));
                                setting.setAlertDialogMessage("location_type", jsonObjectSetting.getString("location_type"));
                                setting.setAlertDialogMessage("gmap_lang", jsonObjectSetting.getString("gmap_lang"));
                                setting.setExitApp("exit", jsonObjectSetting.getString("confirm_app_close"));
                                setting.setUserUnblock("Unblock_M", jsonObjectSetting.getString("unblock_user_m"));
                                setting.setUserTextblock("Block User", jsonObjectSetting.getString("block_user_text"));

                                gmap_lang = jsonObjectSetting.getString("gmap_lang");

                                setting.setGoogleButn(jsonObjectSetting.getJSONObject("registerBtn_show").getBoolean("google"));
                                setting.setfbButn(jsonObjectSetting.getJSONObject("registerBtn_show").getBoolean("facebook"));
                                setting.setLinkedinButn(jsonObjectSetting.getJSONObject("registerBtn_show").getBoolean("linkedin"));
                                setting.setOTPButn(jsonObjectSetting.getJSONObject("registerBtn_show").getBoolean("phone"));

                                JSONObject alertDialog = jsonObjectSetting.getJSONObject("dialog").getJSONObject("confirmation");
                                setting.setGenericAlertTitle(alertDialog.getString("title"));
                                setting.setGenericAlertMessage(alertDialog.getString("text"));
                                setting.setGenericAlertOkText(alertDialog.getString("btn_ok"));
                                setting.setGenericAlertCancelText(alertDialog.getString("btn_no"));
                                setting.setAdShowOrNot(true);
                                setting.isAppOpen(jsonObjectSetting.getBoolean("is_app_open"));
                                setting.checkOpen(jsonObjectSetting.getBoolean("is_app_open"));
                                setting.setGuestImage(jsonObjectSetting.getString("guest_image"));

                                JSONObject jsonObjectLocationPopup = jsonObjectSetting.getJSONObject("location_popup");
                                Location_popupModel Location_popupModel = new Location_popupModel();
                                Location_popupModel.setSlider_number(jsonObjectLocationPopup.getInt("slider_number"));
                                Location_popupModel.setSlider_step(jsonObjectLocationPopup.getInt("slider_step"));
                                Location_popupModel.setLocation(jsonObjectLocationPopup.getString("location"));
                                Location_popupModel.setText(jsonObjectLocationPopup.getString("text"));
                                Location_popupModel.setBtn_submit(jsonObjectLocationPopup.getString("btn_submit"));
                                Location_popupModel.setBtn_clear(jsonObjectLocationPopup.getString("btn_clear"));
                                setting.setLocationPopupModel(Location_popupModel);

                                JSONObject jsonObjectLocationSettings = jsonObjectSetting.getJSONObject("gps_popup");
                                setting.setShowNearby(jsonObjectSetting.getBoolean("show_nearby"));
                                setting.setShowHome(jsonObjectSetting.getBoolean("show_home_icon"));
                                setting.setShowAdvancedSearch(jsonObjectSetting.getBoolean("show_adv_search_icon"));
                                setting.sethorizontal(jsonObjectSetting.getString("homescreen_layout"));
                                Log.d("Home 1 adsLayout", setting.gethorizontal());

                                setting.setfeaturedAdsLayout(jsonObjectSetting.getString("fetaured_screen_layout"));
                                Log.d("Ads FeatureLayout", setting.getfeaturedAdsLayout());

                                setting.setlatestAdsLayout(jsonObjectSetting.getString("latest_screen_layout"));
                                Log.d("Ads LatestLayout", setting.getlatestAdsLayout());

                                setting.setnearbyAdsLayout(jsonObjectSetting.getString("nearby_screen_layout"));
                                Log.d("Ads NearbyLayout", setting.getnearbyAdsLayout());

                                setting.setSliderAdsLayout(jsonObjectSetting.getString("cat_slider_screen_layout"));
                                Log.d("Ads SliderLayout", setting.getSliderAdsLayout());

                                setting.setCatBtnTitle(jsonObjectSetting.getString("home_cat_icons_setion_title"));
                                Log.d("CatBtnTitle", setting.getCatBtnTitle());
                                setting.setLocationStyle(jsonObjectSetting.getString("adlocation_style"));
                                Log.d("adlocationStyle", setting.getLocationStyle());

                                setting.setHomeScreenStyle(jsonObjectSetting.getString("homescreen_style"));
                                Log.d("homescreenStyle", setting.getHomeScreenStyle());

                                setting.setAdDetailScreenStyle(jsonObjectSetting.getString("api_ad_details_style"));
                                Log.d("AdDetailScreenStyle", setting.getAdDetailScreenStyle());
                                setting.setPlacesSearch(jsonObjectSetting.getBoolean("places_search_switch"));

                                setting.setGpsTitle(jsonObjectLocationSettings.getString("title"));
                                setting.setGpsText(jsonObjectLocationSettings.getString("text"));
                                setting.setGpsConfirm(jsonObjectLocationSettings.getString("btn_confirm"));
                                setting.setGpsCancel(jsonObjectLocationSettings.getString("btn_cancel"));
                                setting.setUserVerified(true);

                                setting.setAdsPositionSorter(jsonObjectSetting.getBoolean("ads_position_sorter"));


                                setting.setNotificationTitle("");
                                setting.setNotificationMessage("");
                                setting.setNotificationTitle("");

                                if (setting.getAppOpen()) {
                                    setting.setNoLoginMessage(jsonObjectSetting.getString("notLogin_msg"));
                                }

                                setting.setFeaturedScrollEnable(jsonObjectSetting.getBoolean("featured_scroll_enabled"));
                                if (setting.isFeaturedScrollEnable()) {
                                    setting.setFeaturedScroolDuration(jsonObjectSetting.getJSONObject("featured_scroll").getInt("duration"));
                                    setting.setFeaturedScroolLoop(jsonObjectSetting.getJSONObject("featured_scroll").getInt("loop"));
                                }

                                jsonObjectAppRating = jsonObjectSetting.getJSONObject("app_rating");
                                jsonObjectAppShare = jsonObjectSetting.getJSONObject("app_share");

                                gmap_has_countries = jsonObjectSetting.getBoolean("gmap_has_countries");
                                if (gmap_has_countries) {
                                    gmap_countries = jsonObjectSetting.getString("gmap_countries");
                                }
                                app_show_languages = jsonObjectSetting.getBoolean("app_show_languages");

                                if (app_show_languages) {
                                    languagePopupTitle = jsonObjectSetting.getString("app_text_title");
                                    languagePopupClose = jsonObjectSetting.getString("app_text_close");
                                    app_languages = jsonObjectSetting.getJSONArray("app_languages");

                                }

                                ProgressModel progressModel = new ProgressModel();
                                JSONObject progressJsonObject = jsonObjectSetting.getJSONObject("upload").getJSONObject("progress_txt");
                                progressModel.setTitle(progressJsonObject.getString("title"));
                                progressModel.setSuccessTitle(progressJsonObject.getString("title_success"));
                                progressModel.setFailTitles(progressJsonObject.getString("title_fail"));
                                progressModel.setSuccessMessage(progressJsonObject.getString("msg_success"));
                                progressModel.setFailMessage(progressJsonObject.getString("msg_fail"));
                                progressModel.setButtonText(progressJsonObject.getString("btn_ok"));
                                SettingsMain.setProgressModel(progressModel);

                                permissionsModel permissionsModel = new permissionsModel();
                                JSONObject permissionJsonObject = jsonObjectSetting.getJSONObject("permissions");
                                permissionsModel.setTitle(permissionJsonObject.getString("title"));
                                permissionsModel.setDesc(permissionJsonObject.getString("desc"));
                                permissionsModel.setBtn_goTo(permissionJsonObject.getString("btn_goto"));
                                permissionsModel.setBtnCancel(permissionJsonObject.getString("btn_cancel"));
                                SettingsMain.setPermissionsModel(permissionsModel);

                                AdPostImageModel adPostImageModel = new AdPostImageModel();
                                JSONObject adpostInmageJsonObject = jsonObjectSetting.getJSONObject("ad_post");
                                adPostImageModel.setImg_size(adpostInmageJsonObject.getString("img_size"));
                                adPostImageModel.setImg_message(adpostInmageJsonObject.getString("img_message"));
                                adPostImageModel.setDim_is_show(adpostInmageJsonObject.getBoolean("dim_is_show"));
                                if (adpostInmageJsonObject.getBoolean("dim_is_show")) {
                                    adPostImageModel.setDim_width(adpostInmageJsonObject.getString("dim_width"));
                                    adPostImageModel.setDim_height(adpostInmageJsonObject.getString("dim_height"));
                                    adPostImageModel.setDim_height_message(adpostInmageJsonObject.getString("dim_height_message"));
                                }
                                setting.setAdPostImageModel(adPostImageModel);
                                setting.setShopUrl(jsonObjectSetting.getString("app_page_test_url"));

                                ArrayList<shopMenuModel> menuModelArrayList = new ArrayList<>();
                                JSONArray jsonArray = jsonObjectSetting.getJSONArray("shop_menu");
                                for (int i = 0; i < jsonArray.length(); i++) {
                                    JSONObject jsonObject = jsonArray.getJSONObject(i);
                                    shopMenuModel menuModel = new shopMenuModel();
                                    menuModel.setTitle(jsonObject.getString("title"));
                                    menuModel.setUrl(jsonObject.getString("url"));
                                    menuModelArrayList.add(menuModel);
                                }
                                setting.setShopMenu(menuModelArrayList);
                                CalanderTextModel calanderTextModel = new CalanderTextModel();
                                JSONObject calenderJsonObject = jsonObjectSetting.getJSONObject("calander_text");

                                calanderTextModel.setBtn_ok(calenderJsonObject.getString("ok_btn"));
                                calanderTextModel.setBtn_cancel(calenderJsonObject.getString("cancel_btn"));
                                calanderTextModel.setTitle(calenderJsonObject.getString("date_time"));
                                setting.setCalanderTextModel(calanderTextModel);

                                setting.setAlertDialogMessage("search_text", jsonObjectSetting.getString("search_text"));

                                JSONObject extraText = response.getJSONObject("extra_text");
                                OTPModel otpModel = OTPModel.getInstance();
                                otpModel.setCode_sent(extraText.getString("code_sent"));
                                otpModel.setNot_received(extraText.getString("not_received"));
                                otpModel.setVerify_number(extraText.getString("verify_number"));
                                otpModel.setTry_again(extraText.getString("try_again"));
                                otpModel.setSms_gateway(extraText.getString("sms_gateway"));
                                otpModel.setVerify_success(extraText.getString("verify_success"));
                                otpModel.setIs_number_verified(extraText.getBoolean("is_number_verified"));
                                otpModel.setIs_number_verified_text(extraText.getString("is_number_verified_text"));
                                otpModel.setPhone(extraText.getString("phone"));
                                otpModel.setName(extraText.getString("name"));
                                otpModel.setError1(extraText.getString("error1"));
                                JSONObject phoneDialog = extraText.getJSONObject("phone_dialog");
                                otpModel.setPhoneDialog(phoneDialog.getString("text_field"),
                                        phoneDialog.getString("btn_cancel"),
                                        phoneDialog.getString("btn_confirm"),
                                        phoneDialog.getString("btn_resend"));
                                JSONObject smsDialog = extraText.getJSONObject("send_sms_dialog");
                                otpModel.setSMSDialog(smsDialog.getString("title"),
                                        smsDialog.getString("text"),
                                        smsDialog.getString("btn_send"),
                                        smsDialog.getString("btn_cancel"));

                                JSONArray site_languages = jsonObjectSetting.getJSONArray("site_languages");

                                ChooseLanguageActivity.setData(jsonObjectSetting.getString("wpml_logo"),
                                        jsonObjectSetting.getString("wpml_header_title_1"),
                                        jsonObjectSetting.getString("wpml_header_title_2"),
                                        jsonObjectSetting.getString("wpml_menu_text"),
                                        site_languages);
                                Log.d(" info_site_languages", site_languages.toString());

//                                if (!getIntent().hasExtra("data")){
                                SettingsMain.isWhizzChatActive = jsonObjectSetting.getBoolean("isWhizActive");
                                SettingsMain.whizzChatAPIKey = jsonObjectSetting.getString("whizchat_api_key");

                                PaymentToastsModel.cred_not_found = jsonObjectSetting.getString("cred_not_found");
                                PaymentToastsModel.payment_failed = jsonObjectSetting.getString("payment_failed");
                                PaymentToastsModel.payment_success = jsonObjectSetting.getString("payment_success");
                                PaymentToastsModel.something_wrong = jsonObjectSetting.getString("something_wrong");
                                PaymentToastsModel.response_fail = jsonObjectSetting.getString("response_fail");
                                PaymentToastsModel.token_fail = jsonObjectSetting.getString("token_fail");
                                PaymentToastsModel.invalid_card_data = jsonObjectSetting.getString("invalid_card_data");
                                PaymentToastsModel.payment_cred = jsonObjectSetting.getString("payment_cred");
                                PaymentToastsModel.payment_verify_fail = jsonObjectSetting.getString("payment_verify_fail");
                                try {
                                    JSONArray site_locations = jsonObjectSetting.getJSONArray("app_top_location_list");
                                    ChooseLocationActivity.setData(jsonObjectSetting.getString("app_location_text"), site_locations);
                                }catch (Exception e){
                                    e.printStackTrace();
                                }
                                if (!prefs.getBoolean("firstTime", false)) {

                                    setting.setUserLogin("0");

                                    SharedPreferences.Editor editor = prefs.edit();
                                    editor.putBoolean("firstTime", true);
                                    editor.apply();
                                    Intent myIntent = new Intent(getApplicationContext(), ChooseLanguageActivity.class);

                                    if (setting.getUserLogin().equals("0")) {
                                        if (jsonObjectSetting.getBoolean("is_wpml_active")) {
                                            myIntent.putExtra("firstTime",true);
                                            startActivity(myIntent);
                                        }else if(jsonObjectSetting.getBoolean("app_top_location") && ChooseLocationActivity.siteLocations.length()!=0){
                                            Intent i = new Intent(SplashScreen.this, ChooseLocationActivity.class);
                                            startActivity(i);
                                        }else {
                                            Intent intent = new Intent(activity, MainActivity.class);
                                            startActivity(intent);
                                        }

                                    }
                                } else {
                                    final Handler handler = new Handler();

                                    handler.postDelayed(() -> {
                                        //Do something after 100ms

                                        if (setting.getUserLogin().equals("0")) {
                                            if (setting.getAppOpen()) {
                                                SharedPreferences.Editor editor = activity.getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                                                editor.putString("isSocial", "false");
                                                editor.apply();
                                                Intent intent = new Intent(activity, HomeActivity.class);
                                                startActivity(intent);
                                                activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                                activity.finish();
                                                setting.setUserEmail("");
                                                setting.setUserImage("");
                                            } else {
                                                SplashScreen.this.finish();

                                                Intent intent = new Intent(SplashScreen.this, MainActivity.class);
                                                startActivity(intent);
                                                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                            }
                                        } else {
                                            SplashScreen.this.finish();

                                            setting.isAppOpen(false);
//                                            if (getIntent().hasExtra("data")) {
//                                                try {
//                                                    JSONObject jsonObject = new JSONObject(getIntent().getExtras().getString("data"));
//                                                    TaskStackBuilder.create(SplashScreen.this)
//                                                            .addParentStack(MainActivity.class)
//                                                            .addNextIntent(new Intent(SplashScreen.this, ChatActivity.class)
//                                                                    .putExtra("adId", jsonObject.getString("ad_id"))
//                                                                    .putExtra("type", jsonObject.getString("type"))
//                                                                    .putExtra("calledFromSplash", true)
//                                                                    .putExtra("recieverId", jsonObject.getString("recieverId"))
//                                                                    .putExtra("senderId", jsonObject.getString("senderId")))
//                                                            .startActivities();
//
//
//                                                } catch (JSONException e) {
//                                                    e.printStackTrace();
//                                                }
//                                            }furqan bhai koi way hota a
//                                            if (getIntent().hasExtra("data") || getIntent().hasExtra("chat")) {
//
//                                                RemoteMessage remoteMessage = new RemoteMessage(getIntent().getExtras());
//                                                String notiTitle = remoteMessage.getData().get("message");
//                                                String notiMessage = remoteMessage.getData().get("title");
//                                                String notiImage = remoteMessage.getData().get("image");
//                                                setting.setNotificationTitle(notiTitle);
//                                                setting.setNotificationMessage(notiMessage);
//                                                setting.setNotificationImage(notiImage);
//                                                Intent intent5 = new Intent(SplashScreen.this, HomeActivity.class);
//                                                intent5.putExtra("notiTitle", notiTitle);
//                                                intent5.putExtra("notiMessage", notiMessage);
//                                                intent5.putExtra("notiImage", notiImage);
//                                                startActivity(intent5);
//                                                finish();
//
//                                                if (getIntent().getExtras() != null) {
//                                                    try {
//                                                        for (String key : getIntent().getExtras().keySet()) {
//                                                            String value = getIntent().getExtras().getString(key);
//                                                            Intent in = new Intent(SplashScreen.this, ChatActivity.class);
//                                                            in.putExtra("adId", getIntent().getExtras().get("adId").toString());
//                                                            in.putExtra("senderId", getIntent().getExtras().get("senderId").toString());
//                                                            in.putExtra("calledFromSplash", true);
//                                                            in.putExtra("calledFromChat", false);
//                                                            in.putExtra("recieverId", getIntent().getExtras().get("recieverId").toString());
//                                                            in.putExtra("type", getIntent().getExtras().get("type").toString());
//                                                            startActivity(in);
//                                                            finish();
//                                                            Log.d("keysss", "Key: " + key + " Value: " + value);
//                                                        }
//
//
//                                                    } catch (NullPointerException e) {
//                                                        e.printStackTrace();
//                                                    }
//                                                }
//                                            } else {
//
//                                                Intent intent = new Intent(SplashScreen.this, HomeActivity.class);
//                                                startActivity(intent);
//                                            }

                                            if (getIntent().hasExtra("payload")) {

                                                RemoteMessage remoteMessage = new RemoteMessage(getIntent().getExtras());
                                                String notiTitle = remoteMessage.getData().get("message");
                                                String notiMessage = remoteMessage.getData().get("title");
                                                String notiImage = remoteMessage.getData().get("image");
                                                setting.setNotificationTitle(notiTitle);
                                                setting.setNotificationMessage(notiMessage);
                                                setting.setNotificationImage(notiImage);
                                                Intent intent5 = new Intent(SplashScreen.this, HomeActivity.class);
                                                intent5.putExtra("notiTitle", notiTitle);
                                                intent5.putExtra("notiMessage", notiMessage);
                                                intent5.putExtra("calledForNotification", true);
                                                intent5.putExtra("notiImage", notiImage);
                                                startActivity(intent5);
                                                finish();


                                            } else if (getIntent().hasExtra("chat")) {
                                                try {
                                                    for (String key : getIntent().getExtras().keySet()) {
                                                        String value = getIntent().getExtras().getString(key);
                                                        Intent in = new Intent(SplashScreen.this, ChatActivity.class);
                                                        in.putExtra("adId", getIntent().getExtras().get("adId").toString());
                                                        in.putExtra("senderId", getIntent().getExtras().get("senderId").toString());
                                                        in.putExtra("calledFromSplash", true);
                                                        in.putExtra("recieverId", getIntent().getExtras().get("recieverId").toString());
                                                        in.putExtra("type", getIntent().getExtras().get("type").toString());
                                                        startActivity(in);
                                                        finish();
                                                        Log.d("keysss", "Key: " + key + " Value: " + value);
                                                    }


                                                } catch (NullPointerException e) {
                                                    e.printStackTrace();
                                                }
                                            } else {

                                                Intent intent = new Intent(SplashScreen.this, HomeActivity.class);
                                                startActivity(intent);
                                            }
                                            overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                        }
                                        try {
                                            if (jsonObjectSetting.getBoolean("is_wpml_active")) {
                                                if (setting.isLanguageChanged()) {
                                                    updateViews(SettingsMain.getLanguageCode());
                                                } else updateViews("en");
                                            } else {
                                                updateViews(gmap_lang);
                                                setting.setLanguageCode(gmap_lang);
                                            }
//                                            if(jsonObjectSetting.getBoolean("app_top_location")) {
//                                                if (setting.isLocationChanged()) {
//                                                    updateView(SettingsMain.getlocationId());
//                                                } else {
//                                                    updateView(SettingsMain.getlocationId());
//                                                    setting.setLocationId(SettingsMain.getlocationId());
//                                                }
//
//                                            }
//                                            else{
//                                                SplashScreen.this.finish();
//                                                setting.isAppOpen(false);
//                                                Intent intent = new Intent(SplashScreen.this, HomeActivity.class);
//                                                startActivity(intent);
//
//                                            }
                                        } catch (JSONException e) {
                                            e.printStackTrace();
                                        }

                                    }, 2000);
                                }
//                                }

                            } else {
                                Toast.makeText(activity, response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }

                    } catch (JSONException | IOException e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    Log.d("info settings error", String.valueOf(t));
                    Log.d("info settings error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } catch (
                ArrayIndexOutOfBoundsException e) {
            e.printStackTrace();
        }

    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(LocaleHelper.onAttach(base));
    }

    private void updateViews(String languageCode) {
        LocaleHelper.setLocale(this, languageCode);

    }

    private void updateView(String locationId) {
        LocaleHelper.setLocale(this, locationId);
    }

//    @Override
//    public void onInAppUpdateError(int code, Throwable error) {
//        Log.d("error", "Update error: " + error);
//
//    }
//
//    @Override
//    public void onInAppUpdateStatus(InAppUpdateStatus status) {
//        /*
//         * If the update downloaded, ask user confirmation and complete the update
//         */
//
//        if (status.isDownloaded()) {
//
//            View rootView = getWindow().getDecorView().findViewById(android.R.id.content);
//
//            Snackbar snackbar = Snackbar.make(rootView,
//                    "An update has just been downloaded.",
//                    Snackbar.LENGTH_INDEFINITE);
//
//            snackbar.setAction("RESTART", view -> {
//
//                // Triggers the completion of the update of the app for the flexible flow.
//                inAppUpdateManager.completeUpdate();
//
//            });
//
//            snackbar.show();
//
//        }
//
//    }
//
//    @Override
//    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
//        if (requestCode == REQ_CODE_VERSION_UPDATE) {
//            if (resultCode == Activity.RESULT_CANCELED) {
//                // If the update is cancelled by the user,
//                // you can request to start the update again.
//                inAppUpdateManager.checkForAppUpdate();
//
//                Log.d("successhaaaaaa", "Update flow failed! Result code: " + resultCode);
//            }
//        }
//
//        super.onActivityResult(requestCode, resultCode, data);
//
//    }
}
