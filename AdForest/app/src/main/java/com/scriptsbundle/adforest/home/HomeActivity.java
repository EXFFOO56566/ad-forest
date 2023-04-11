package com.scriptsbundle.adforest.home;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.graphics.drawable.GradientDrawable;
import android.location.Address;
import android.location.Geocoder;
import android.net.http.SslError;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import com.facebook.shimmer.ShimmerFrameLayout;

import com.google.android.gms.auth.api.Auth;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.gms.common.api.ResultCallback;
import com.google.android.gms.common.api.Status;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.navigation.NavigationView;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.core.view.GravityCompat;
import androidx.core.view.MenuItemCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.SearchView;
import androidx.appcompat.widget.Toolbar;

import android.text.Editable;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.webkit.SslErrorHandler;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.codemybrainsout.ratingdialog.RatingDialog;
import com.facebook.login.LoginManager;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.ApiException;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.libraries.places.api.model.AutocompletePrediction;
import com.google.android.libraries.places.api.model.AutocompleteSessionToken;
import com.google.android.libraries.places.api.model.TypeFilter;
import com.google.android.libraries.places.api.net.FetchPlaceRequest;
import com.google.android.libraries.places.api.net.FindAutocompletePredictionsRequest;
import com.google.android.libraries.places.api.net.PlacesClient;
import com.google.firebase.messaging.FirebaseMessaging;
import com.google.gson.JsonObject;

import com.scriptsbundle.adforest.messages.WhizzChat.View.WhizzChatList;
import com.scriptsbundle.adforest.packages.PackagesFragment;
import com.scriptsbundle.adforest.profile.Expired_SoldAds;
import com.scriptsbundle.adforest.profile.Most_ViewedAds;
import com.scriptsbundle.adforest.profile.MyAds_Rejected;


import com.mapbox.api.geocoding.v5.models.CarmenFeature;
import com.mapbox.geojson.Point;
import com.mapbox.mapboxsdk.plugins.places.autocomplete.PlaceAutocomplete;
import com.mapbox.mapboxsdk.plugins.places.autocomplete.model.PlaceOptions;

import com.squareup.picasso.Picasso;
import com.xw.repo.BubbleSeekBar;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Locale;
import java.util.concurrent.TimeoutException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.Blog.BlogDetailFragment;
import com.scriptsbundle.adforest.Blog.BlogFragment;
import com.scriptsbundle.adforest.Notification.Config;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.Search.FragmentCatSubNSearch;
import com.scriptsbundle.adforest.Search.SearchActivity;
import com.scriptsbundle.adforest.Settings.Settings;
import com.scriptsbundle.adforest.Shop.shopActivity;
import com.scriptsbundle.adforest.SplashScreen;
import com.scriptsbundle.adforest.helper.LocaleHelper;
import com.scriptsbundle.adforest.home.helper.Location_popupModel;
import com.scriptsbundle.adforest.messages.Message;
import com.scriptsbundle.adforest.profile.FragmentProfile;
import com.scriptsbundle.adforest.profile.MyAds;
import com.scriptsbundle.adforest.profile.MyAds_Favourite;
import com.scriptsbundle.adforest.profile.MyAds_Featured;
import com.scriptsbundle.adforest.profile.MyAds_Inactive;
import com.scriptsbundle.adforest.signinorup.MainActivity;
import com.scriptsbundle.adforest.signinorup.MainViewRegisterFragment;
import com.scriptsbundle.adforest.userAndSellers.Blocked_UserFragment;
import com.scriptsbundle.adforest.userAndSellers.SellersListFragment;
import com.scriptsbundle.adforest.utills.Admob;
import com.scriptsbundle.adforest.utills.CircleTransform;
import com.scriptsbundle.adforest.utills.GPSTracker;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.RuntimePermissionHelper;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static com.scriptsbundle.adforest.utills.SettingsMain.getMainColor;

//import com.com.com.scriptsbundle.adforest.adapters.PlaceArrayAdapter;
//import static com.google.android.gms.location.places.AutocompleteFilter.TYPE_FILTER_ADDRESS;
//import static com.google.android.gms.location.places.AutocompleteFilter.TYPE_FILTER_REGIONS;

public class HomeActivity extends AppCompatActivity
        implements NavigationView.OnNavigationItemSelectedListener, RuntimePermissionHelper.permissionInterface,
        GoogleApiClient.OnConnectionFailedListener, GoogleApiClient.ConnectionCallbacks, AdapterView.OnItemClickListener {

    //    FirebaseDatabase database;
//    DatabaseReference myRef;
    GoogleApiClient mGoogleApiClient;
    public static Activity activity;
    public static Boolean checkLoading = false;
    boolean back_pressed = false;
    public static Boolean loadingScreen = false;

    @Override
    protected void onResume() {
        super.onResume();
        if (settingsMain != null) {
            if (!settingsMain.getUserVerified()) {
                replaceFragment(new FragmentProfile(), "FragmentProfile");
            }
        }
    }


    ArrayList<String> strings = new ArrayList<>();
    ArrayList<String> list = new ArrayList<>();
    SettingsMain settingsMain;
    ImageView imageViewProfile;
    UpdateFragment updatfrag;
    TextView textViewUserName, title;
    RestService restService;
    AutoCompleteTextView currentLocationText;
    FragmentHome fragmentHome;
    FragmentHomeMarvel fragmentHomeMarvel;
    FragmentHomeMulti fragmentHomeMulti;
    MainViewRegisterFragment mainViewRegisterFragment;
    FragmentProfile fragmentProfile;
    GPSTracker gps;
    double latitude, longitude;
    RuntimePermissionHelper runtimePermissionHelper;
    SwipeRefreshLayout swipeRefreshLayout;
    ArrayList<String> places = new ArrayList<>();
    ArrayList<String> ids = new ArrayList<>();
    private PlacesClient placesClient;
    String location, locationIdHomePOpup, locationIdHomePOpupName, notiTitle, notiMessage, notiImage;
    String calledFromChat = "false";
    AutoCompleteTextView placesContainer;
    String address_by_mapbox;
    //    public static Skeleton skeleton;
    public static FloatingActionButton fab;
    public static ShimmerFrameLayout shimmerFrameLayout;
    public static LinearLayout loadingLayout;
    RelativeLayout mainRelative;
    FrameLayout frameLayout;

    public void updateApi(UpdateFragment listener) {
        updatfrag = listener;
    }


    @SuppressLint("RestrictedApi")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_home);
        System.gc();
        settingsMain = new SettingsMain(this);

        runtimePermissionHelper = new RuntimePermissionHelper(this, this);
//        database = FirebaseDatabase.getInstance();
//        myRef = database.getReference("UserLogin");1111
        activity = this;


        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.parseColor(getMainColor()));
        }
        if (settingsMain.getRTL()) {
            getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        } else {
            getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_LTR);
        }
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
//        if (AppCompatDelegate.getDefaultNightMode() == AppCompatDelegate.MODE_NIGHT_YES) {
//            setTheme(R.style.AppDarkTheme);
//        }
        fab = (FloatingActionButton) findViewById(R.id.fab);

        fab.setBackgroundTintList(ColorStateList.valueOf(Color.parseColor(getMainColor())));
        fab.setOnClickListener(view -> {
            runtimePermissionHelper.requestLocationPermission(2);
        });


        shimmerFrameLayout = (ShimmerFrameLayout) findViewById(R.id.shimmerFrameLayout);
        loadingLayout = (LinearLayout) findViewById(R.id.shimmerMain);
        frameLayout = (FrameLayout) findViewById(R.id.frameContainer);
        swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swipe_refresh_layout);
        if (swipeRefreshLayout != null) {

            swipeRefreshLayout.setOnRefreshListener(() -> {
                loadingLayout.setVisibility(View.VISIBLE);
                shimmerFrameLayout.setVisibility(View.VISIBLE);
                shimmerFrameLayout.startShimmer();
                frameLayout.setVisibility(View.GONE);
                SettingsMain.showShimmer(this);
                checkLoading = true;
                adforest_swipeRefresh();

            });

        }

        toolbar.setBackgroundColor(Color.parseColor(getMainColor()));

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        //noinspection deprecation
        drawer.setDrawerListener(toggle);
        toggle.syncState();

        NavigationView navigationView = (NavigationView) findViewById(R.id.nav_view);
        navigationView.setNavigationItemSelectedListener(this);


        View header = navigationView.getHeaderView(0);

        if (settingsMain.getAppOpen()) {
            restService = UrlController.createService(RestService.class);
            fab.setVisibility(View.VISIBLE);
            fab.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    Toast.makeText(getApplicationContext(), settingsMain.getNoLoginMessage(), Toast.LENGTH_LONG).show();

                }
            });

        } else {
            restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), this);

        }
        if (settingsMain.getHomeScreenStyle().equals("home3")) {
            fab.setVisibility(View.GONE);
        } else {
            fab.setVisibility(View.VISIBLE);
        }
        if (header != null) {
            TextView textViewUserEmail = header.findViewById(R.id.textView);
            textViewUserName = header.findViewById(R.id.username);
            imageViewProfile = header.findViewById(R.id.imageView);


            int[] colors = {Color.parseColor(getMainColor()), Color.parseColor(getMainColor())};
            //create a new gradient color
            GradientDrawable gd = new GradientDrawable(
                    GradientDrawable.Orientation.LEFT_RIGHT, colors);
            gd.setCornerRadius(0f);

            header.setBackground(gd);

            if (!TextUtils.isEmpty(settingsMain.getUserEmail())) {
                textViewUserEmail.setText(settingsMain.getUserEmail());
            }
            if (!TextUtils.isEmpty(settingsMain.getUserName())) {
                textViewUserName.setText(settingsMain.getUserName());
            }
            if (settingsMain.getAppOpen()) {
                if (!TextUtils.isEmpty(settingsMain.getGuestImage())) {
                    Picasso.get().load(settingsMain.getGuestImage())
                            .transform(new CircleTransform())
                            .error(R.drawable.placeholder)
                            .placeholder(R.drawable.placeholder)
                            .into(imageViewProfile);
                }
            } else {
                if (!TextUtils.isEmpty(settingsMain.getUserImage())) {
                    Picasso.get().load(settingsMain.getUserImage())
                            .transform(new CircleTransform())
                            .error(R.drawable.placeholder)
                            .placeholder(R.drawable.placeholder)
                            .into(imageViewProfile);
                }
            }
        }
        locationIdHomePOpup = getIntent().getStringExtra("location_id");
        locationIdHomePOpupName = getIntent().getStringExtra("location_name");
        calledFromChat = getIntent().getStringExtra("calledFromChat");
        fragmentHome = new FragmentHome();
        fragmentHomeMarvel = new FragmentHomeMarvel();
        fragmentHomeMulti = new FragmentHomeMulti();
        mainViewRegisterFragment = new MainViewRegisterFragment();
        Bundle bundle = new Bundle();
        bundle.putString("location_id", locationIdHomePOpup);
        bundle.putString("location_name", locationIdHomePOpupName);
        bundle.putString("notiTitle", notiTitle);
        bundle.putString("notiMessage", notiMessage);
        bundle.putString("notiImage", notiImage);
        bundle.putString("calledFromChat", calledFromChat);
        fragmentHome.setArguments(bundle);
        fragmentHomeMarvel.setArguments(bundle);
        fragmentHomeMulti.setArguments(bundle);
        //|| calledFromChat.equals("false")
        if (!settingsMain.getNotificationTitle().equals("")) {
            String title, message, image;
            title = settingsMain.getNotificationTitle();
            message = settingsMain.getNotificationMessage();
            image = settingsMain.getNotificationImage();

            if (getIntent().getBooleanExtra("calledForNotification", false)) {
                adforest_showNotificationDialog(title, message, image);
                updateViews(settingsMain.getAlertDialogMessage("gmap_lang"));
            }
        }


        try {
            if (settingsMain.getNotificationTitle().equals("")) {
                if (SplashScreen.jsonObjectAppRating.getBoolean("is_show")) {
                    showRatingDialog();
                } else {
                    Log.d("info HomeActivityRating", "False");
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        //HomeScreenStyles CC
        switch (settingsMain.getHomeScreenStyle()) {
            case "home1":
                startFragment(fragmentHome, "FragmentHome");
                break;
            case "home2":
                startFragment(fragmentHomeMarvel, "FragmentHomeMarvel");
                break;
            case "home3":
                startFragment(fragmentHomeMulti, "FragmentHomeMulti");
                break;
        }
        getIncomingIntent();
    }

    public void getIncomingIntent() {
        if (getIntent().hasExtra("title")) {
            String title = getIntent().getStringExtra("title");
            setitle(title);
        }
    }

    private void setitle(String title) {
        TextView name = (TextView) findViewById(R.id.txt_pick_your_language);
        name.setText(title);
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(LocaleHelper.onAttach(base));
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 35) {
            if (resultCode == Activity.RESULT_OK && data != null) {
                CarmenFeature feature = PlaceAutocomplete.getPlace(data);
                Point point = feature.center();
                latitude = point.latitude();
                longitude = point.longitude();
                Log.d("MapBox LatLng", point.toString());
                address_by_mapbox = feature.placeName();
                placesContainer.setText(address_by_mapbox);


            }
        }

    }

    private void updateViews(String languageCode) {
        LocaleHelper.setLocale(this, languageCode);
    }

    private void adforest_swipeRefresh() {
        String fragment = null;
        swipeRefreshLayout.setRefreshing(true);
//        mainRelative.setVisibility(View.VISIBLE);
//        frameLayout.setVisibility(View.VISIBLE);
        Fragment currentFragment = getSupportFragmentManager().findFragmentById(R.id.frameContainer);
        if (currentFragment instanceof FragmentHome) {
            fragment = "FragmentHome";
        }
        if (currentFragment instanceof FragmentHomeMarvel) {
            fragment = "FragmentHomeMarvel";
        }
        if (currentFragment instanceof FragmentHomeMulti) {
            fragment = "FragmentHomeMulti";
        }
        if (currentFragment instanceof BlogFragment) {
            fragment = "BlogFragment";
        }
        if (currentFragment instanceof BlogDetailFragment) {
            fragment = "BlogDetailFragment";
        }
        if (currentFragment instanceof PackagesFragment) {
            fragment = "PackagesFragment";
        }
        if (currentFragment instanceof MyAds) {
            fragment = "MyAds";
        }
        if (currentFragment instanceof MyAds_Favourite) {
            fragment = "MyAds_Favourite";
        }
        if (currentFragment instanceof MyAds_Rejected) {
            fragment = "MyAds_Rejected";
        }
        if (currentFragment instanceof Expired_SoldAds) {
            fragment = "MyAdsExpire";
        }
        if (currentFragment instanceof Most_ViewedAds) {
            fragment = "Most_ViewedAds";
        }
        if (currentFragment instanceof MyAds_Featured) {
            fragment = "MyAds_Featured";
        }
        if (currentFragment instanceof MyAds_Inactive) {
            fragment = "MyAds_Inactive";
        }
        if (currentFragment instanceof FragmentCatSubNSearch) {
            fragment = "FragmentCatSubNSearch";
        }
        if (currentFragment instanceof FragmentAllLocations) {
            fragment = "FragmentAllLocations";
        }
        if (currentFragment instanceof FragmentAllCategories) {
            fragment = "FragmentAllCategories";
        }
        if (currentFragment instanceof FragmentProfile) {
            fragment = "FragmentProfile";
        }
        if (currentFragment instanceof Blocked_UserFragment) {
            fragment = "Blocked_UserFragment";
        }
        if (currentFragment instanceof FragmentCustomPages) {
            fragment = "FragmentCustomPages";
        }
//        if (currentFragment instanceof MessagesFragment) {
//            fragment = "MessagesFragment";
//        }

        Handler handler = new Handler();
        final String finalFragment = fragment;
        if (fragment != null) {
//            if (fragment.equals("Blocked_UserFragment")) {
//                swipeRefreshLayout.setRefreshing(false);
//                shimmerFrameLayout.stopShimmer();
//                shimmerFrameLayout.setVisibility(View.GONE);
//                loadingLayout.setVisibility(View.GONE);
//
//            }
//            else if (fragment.equals("FragmentCatSubNSearch")) {
//                swipeRefreshLayout.setRefreshing(false);
//                shimmerFrameLayout.stopShimmer();
//                shimmerFrameLayout.setVisibility(View.GONE);
//                loadingLayout.setVisibility(View.GONE);
//
//            } else {
//                handler.postDelayed(new Runnable() {
//                    @Override
//                    public void run() {
//                        SettingsMain.reload(HomeActivity.this, finalFragment);
//                        swipeRefreshLayout.setRefreshing(false);
//                        shimmerFrameLayout.stopShimmer();
//                        shimmerFrameLayout.setVisibility(View.GONE);
//                        loadingLayout.setVisibility(View.GONE);
//
//                    }
//                }, 500);
//            }
            handler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    SettingsMain.reload(HomeActivity.this, finalFragment);
                    swipeRefreshLayout.setRefreshing(false);
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    frameLayout.setVisibility(View.VISIBLE);

                }
            }, 500);
        }

    }

    public void showRatingDialog() {
        String title = null, text = null, btn_confirm = null, btn_cancel = null, url = null;
        try {
            title = SplashScreen.jsonObjectAppRating.getString("title");
            btn_confirm = SplashScreen.jsonObjectAppRating.getString("btn_confirm");
            btn_cancel = SplashScreen.jsonObjectAppRating.getString("btn_cancel");
            url = SplashScreen.jsonObjectAppRating.getString("url");
        } catch (JSONException e) {
            e.printStackTrace();
        }
        Log.i("this", "Feedback:");


        final RatingDialog ratingDialog = new RatingDialog.Builder(this)
                .session(3)
                .threshold(3)
                .title(title)
                .positiveButtonText(btn_confirm)
                .negativeButtonText(btn_cancel)
                .ratingBarColor(R.color.yellow)
                .playstoreUrl(url)
                .onRatingBarFormSumbit(feedback -> Log.i("this", "Feedback:" + feedback))
                .build();


        ratingDialog.show();
    }

    private void adforest_showNotificationDialog(String title, String message, String image) {

        final Dialog dialog;
        dialog = new Dialog(HomeActivity.this, R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.custom_notification_layout);
        ImageView imageView = dialog.findViewById(R.id.notificationImage);
        TextView tv_title = dialog.findViewById(R.id.notificationTitle);
        TextView tV_message = dialog.findViewById(R.id.notificationMessage);
        Button button = dialog.findViewById(R.id.cancel_button);
        button.setText(settingsMain.getGenericAlertCancelText());
        button.setBackgroundColor(Color.parseColor(getMainColor()));


        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));


        if (!TextUtils.isEmpty(image)) {
            Picasso.get().load(image)
                    .fit()
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(imageView);
        }

        tv_title.setText(title);
        tV_message.setText(message);

        button.setOnClickListener(v -> dialog.dismiss());
        dialog.show();
    }

    public void changeImage() {
        if (!TextUtils.isEmpty(settingsMain.getUserImage())) {
            Picasso.get().load(settingsMain.getUserImage())
                    .transform(new CircleTransform())
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(imageViewProfile);
        }
        textViewUserName.setText(settingsMain.getUserName());
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.home, menu);
        MenuItem searchViewItem = menu.findItem(R.id.action_search);
        MenuItem advancedsearchViewItem = menu.findItem(R.id.action_advanced_search);
        MenuItem action_home = menu.findItem(R.id.action_home);
        MenuItem action_location = menu.findItem(R.id.action_location);
        action_home.setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem menuItem) {
                switch (settingsMain.getHomeScreenStyle()) {
                    case "home1": {

                        FragmentHome fragmentHome = new FragmentHome();
                        FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
                        fragmentTransaction.replace(R.id.frameContainer, fragmentHome).addToBackStack(null).commit();
                        break;
                    }
                    case "home2": {
                        FragmentHomeMarvel fragmentHomeMarvel = new FragmentHomeMarvel();
                        FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
                        fragmentTransaction.replace(R.id.frameContainer, fragmentHomeMarvel).addToBackStack(null).commit();
                        break;
                    }
                    case "home3": {
                        FragmentHomeMulti fragmentHomeMulti = new FragmentHomeMulti();
                        FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
                        fragmentTransaction.replace(R.id.frameContainer, fragmentHomeMulti).addToBackStack(null).commit();
                        break;
                    }
                }
                return true;
            }
        });
        switch (settingsMain.getHomeScreenStyle()) {
            case "home1":
            case "home3":
                if (settingsMain.getShowHome()) {
                    action_home.setVisible(true);
                } else {
                    action_home.setVisible(false);
                }
                if (settingsMain.getShowNearBy() && settingsMain.getAdsPositionSorter() && !settingsMain.getUserLogin().equals("0")) {
                    action_location.setVisible(true);
                } else {
                    action_location.setVisible(false);
                }
                action_location.setOnMenuItemClickListener(item -> {
                    runtimePermissionHelper.requestLocationPermission(1);
                    return true;
                });
                if (!settingsMain.getShowAdvancedSearch()) {
                    advancedsearchViewItem.setVisible(false);
                    final SearchView searchView = (SearchView) MenuItemCompat.getActionView(searchViewItem);
                    searchView.setQueryHint(settingsMain.getAlertDialogMessage("search_text"));
                    searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
                        @Override
                        public boolean onQueryTextSubmit(String query) {
                            searchView.clearFocus();

                            if (!query.equals("")) {

                                FragmentManager fm = getSupportFragmentManager();
                                Fragment fragment = fm.findFragmentByTag("FragmentCatSubNSearch");
                                Fragment fragment2 = fm.findFragmentById(R.id.frameContainer);

                                FragmentCatSubNSearch fragment_search = new FragmentCatSubNSearch();
                                Bundle bundle = new Bundle();
                                bundle.putString("id", "");
                                bundle.putString("title", query);

                                fragment_search.setArguments(bundle);

                                if (fragment != fragment2) {
                                    replaceFragment(fragment_search, "FragmentCatSubNSearch");
                                    return true;
                                } else {
                                    updatfrag.updatefrag(query);
                                    return true;
                                }
                            }
                            return true;
                        }

                        @Override
                        public boolean onQueryTextChange(String newText) {
                            return false;
                        }
                    });
                } else {
                    searchViewItem.setVisible(false);
                    advancedsearchViewItem.setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
                        @Override
                        public boolean onMenuItemClick(MenuItem menuItem) {
                            Intent intent = new Intent(HomeActivity.this.getApplicationContext(), SearchActivity.class);
                            startActivity(intent);
                            return true;

                        }
                    });
                }
                break;
            case "home2":
                action_home.setVisible(false);
                searchViewItem.setVisible(false);
                advancedsearchViewItem.setVisible(false);
                if (settingsMain.getShowNearBy() && settingsMain.getAdsPositionSorter() && !settingsMain.getUserLogin().equals("0")) {
                    action_location.setVisible(true);
                } else {
                    action_location.setVisible(false);
                }
                action_location.setOnMenuItemClickListener(item -> {
                    runtimePermissionHelper.requestLocationPermission(1);
                    return true;
                });
                break;
        }
        //&& !settingsMain.getUserLogin().equals("0")
        //CCOlder
//        if (settingsMain.getShowHome()) {
//            action_home.setVisible(true);
//        } else {
//            action_home.setVisible(false);
//        }
//        if (settingsMain.getShowNearBy() && settingsMain.getAdsPositionSorter() && !settingsMain.getUserLogin().equals("0")) {
//            action_location.setVisible(true);
//        } else {
//            action_location.setVisible(false);
//        }
//        action_location.setOnMenuItemClickListener(item -> {
//            runtimePermissionHelper.requestLocationPermission(1);
//            return true;
//        });
//        if (!settingsMain.getShowAdvancedSearch()) {
//            advancedsearchViewItem.setVisible(false);
//            final SearchView searchView = (SearchView) MenuItemCompat.getActionView(searchViewItem);
//            searchView.setQueryHint(settingsMain.getAlertDialogMessage("search_text"));
//            searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
//                @Override
//                public boolean onQueryTextSubmit(String query) {
//                    searchView.clearFocus();
//
//                    if (!query.equals("")) {
//
//                        FragmentManager fm = getSupportFragmentManager();
//                        Fragment fragment = fm.findFragmentByTag("FragmentCatSubNSearch");
//                        Fragment fragment2 = fm.findFragmentById(R.id.frameContainer);
//
//                        FragmentCatSubNSearch fragment_search = new FragmentCatSubNSearch();
//                        Bundle bundle = new Bundle();
//                        bundle.putString("id", "");
//                        bundle.putString("title", query);
//
//                        fragment_search.setArguments(bundle);
//
//                        if (fragment != fragment2) {
//                            replaceFragment(fragment_search, "FragmentCatSubNSearch");
//                            return true;
//                        } else {
//                            updatfrag.updatefrag(query);
//                            return true;
//                        }
//                    }
//                    return true;
//                }
//
//                @Override
//                public boolean onQueryTextChange(String newText) {
//                    return false;
//                }
//            });
//        } else {
//            searchViewItem.setVisible(false);
//            advancedsearchViewItem.setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
//                @Override
//                public boolean onMenuItemClick(MenuItem menuItem) {
//                    Intent intent = new Intent(HomeActivity.this.getApplicationContext(), SearchActivity.class);
//                    startActivity(intent);
//                    return true;
//
//                }
//            });
//        }
        return super.onCreateOptionsMenu(menu);
    }

    private void manageAutoComplete(String query) {
        AutocompleteSessionToken token = AutocompleteSessionToken.newInstance();
        FindAutocompletePredictionsRequest.Builder request = FindAutocompletePredictionsRequest.builder();

        if (SplashScreen.gmap_has_countries) {
            request.setCountry(SplashScreen.gmap_countries);
        }
        if (settingsMain.getAlertDialogMessage("location_type").equals("regions")) {
            request.setTypeFilter(TypeFilter.ADDRESS);
        } else {
            request
                    .setTypeFilter(TypeFilter.REGIONS);
        }
        request.setSessionToken(token)
                .setQuery(query);


        placesClient.findAutocompletePredictions(request.build()).addOnSuccessListener((response) -> {

            ids.clear();
            places.clear();
            for (AutocompletePrediction prediction : response.getAutocompletePredictions()) {
                places.add(prediction.getFullText(null).toString());
                ids.add(prediction.getPlaceId());
                Log.i("Places", prediction.getPlaceId());
                Log.i("Places", prediction.getFullText(null).toString());
            }
            String[] data = places.toArray(new String[places.size()]); // terms is a List<String>

            ArrayAdapter<?> adapter = new ArrayAdapter<Object>(HomeActivity.this, android.R.layout.simple_dropdown_item_1line, data);
            currentLocationText.setAdapter(adapter);

            adapter.notifyDataSetChanged();
        }).addOnFailureListener((exception) -> {
            if (exception instanceof ApiException) {
                ApiException apiException = (ApiException) exception;
                Log.e("Places", "Place not found: " + apiException.getStatusCode());
            }
        });


    }

    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {


        String placeId = ids.get(position);
        List<com.google.android.libraries.places.api.model.Place.Field> placeFields = Arrays.asList(com.google.android.libraries.places.api.model.Place.Field.LAT_LNG);

// Construct a request object, passing the place ID and fields array.
        FetchPlaceRequest request = FetchPlaceRequest.builder(placeId, placeFields)
                .build();
// Add a listener to handle the response.
        placesClient.fetchPlace(request).addOnSuccessListener((response) -> {
            com.google.android.libraries.places.api.model.Place place = response.getPlace();
            Log.i("Places", "Place found: " + place.getLatLng().latitude + " " + place.getLatLng().longitude);
            longitude = place.getLatLng().longitude;
            latitude = place.getLatLng().latitude;

        }).addOnFailureListener((exception) -> {
            if (exception instanceof ApiException) {
                ApiException apiException = (ApiException) exception;
                int statusCode = apiException.getStatusCode();
// Handle error with given status code.
                Log.e("Places", "Place not found: " + exception.getMessage());
            }
        });


    }

    private void adforest_loctionSearch() {

        gps = new GPSTracker(HomeActivity.this);

        List<Address> addresses1 = null;
        if (gps.canGetLocation()) {
            latitude = gps.getLatitude();
            longitude = gps.getLongitude();

            Location_popupModel Location_popupModel = settingsMain.getLocationPopupModel(this);

            final Dialog dialog = new Dialog(HomeActivity.this, R.style.customDialog);

            dialog.setCanceledOnTouchOutside(true);
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setContentView(R.layout.dialog_location_seekbar);

            final BubbleSeekBar bubbleSeekBar = dialog.findViewById(R.id.seakBar);
            bubbleSeekBar.getConfigBuilder()
                    .max(Location_popupModel.getSlider_number())
                    .sectionCount(Location_popupModel.getSlider_step())
                    .secondTrackColor(Color.parseColor(getMainColor()))
                    .build();

            try {
                addresses1 = new Geocoder(this, Locale.getDefault()).getFromLocation(latitude, longitude, 1);
            } catch (IOException e) {
                e.printStackTrace();
            }
            StringBuilder result = new StringBuilder();
            if (addresses1 != null) {
                if (addresses1.size() > 0) {
                    Address address = addresses1.get(0);
                    int maxIndex = address.getMaxAddressLineIndex();
                    for (int x = 0; x <= maxIndex; x++) {
                        result.append(address.getAddressLine(x));
                        //result.append(",");
                    }
                }
                Log.d("info location", addresses1.toString());
                Log.d("info locaLatLong", latitude + " Long " + longitude);
            }
            dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));
            Button Send = dialog.findViewById(R.id.send_button);
            Button Cancel = dialog.findViewById(R.id.cancel_button);
            TextView locationText = dialog.findViewById(R.id.locationText);
            currentLocationText = dialog.findViewById(R.id.et_location);

            if (settingsMain.getPlacesSearch()) {
                placesContainer = dialog.findViewById(R.id.et_location_mapBox);
                placesContainer.setVisibility(View.VISIBLE);
                currentLocationText.setVisibility(View.GONE);
                if (result.toString().isEmpty()) {
                    placesContainer.setVisibility(View.GONE);
                } else {
                    placesContainer.setHint(result.toString());
                }
                placesContainer.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Intent intent = new PlaceAutocomplete.IntentBuilder()
                                .accessToken(getString(R.string.access_token))
                                .placeOptions(PlaceOptions.builder().backgroundColor(Color.parseColor("#EEEEEE")).limit(10).build(PlaceOptions.MODE_CARDS))
                                .build(HomeActivity.this);
                        startActivityForResult(intent, 35);
                    }
                });
            } else {
//                currentLocationText = dialog.findViewById(R.id.et_location);
                placesClient = com.google.android.libraries.places.api.Places.createClient(this);
                currentLocationText.setOnItemClickListener(this);

                currentLocationText.addTextChangedListener(new TextWatcher() {

                    @Override
                    public void onTextChanged(CharSequence s, int start, int before, int count) {
                        manageAutoComplete(s.toString());

                    }

                    @Override
                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    @Override
                    public void afterTextChanged(Editable s) {

                    }
                });

            }


            Send.setText(Location_popupModel.getBtn_submit());
            Cancel.setText(Location_popupModel.getBtn_clear());
            locationText.setText(Location_popupModel.getText());
//            if (!settingsMain.getPlacesSearch()) {
            if (result.toString().isEmpty()) {
                currentLocationText.setVisibility(View.GONE);
            } else
                currentLocationText.setHint(result.toString());
//            }
            Send.setBackgroundColor(Color.parseColor(getMainColor()));
            Cancel.setBackgroundColor(Color.parseColor(getMainColor()));

            Send.setOnClickListener(v -> {
                adforest_changeNearByStatus(Double.toString(latitude), Double.toString(longitude),
                        Integer.toString(bubbleSeekBar.getProgress()));
                dialog.dismiss();
            });
            Cancel.setOnClickListener(v -> {
                adforest_changeNearByStatus("", ""
                        , Integer.toString(bubbleSeekBar.getProgress()));
                dialog.dismiss();
            });

            dialog.show();
        } else
            gps.showSettingsAlert();
    }


    @Override
    public void onBackPressed() {

        DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);

        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);

        } else {
            if (fragmentHome != null && fragmentHome.isVisible()) {
                if (!back_pressed) {
                    Toast.makeText(getApplicationContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
                    back_pressed = true;
                    android.os.Handler mHandler = new android.os.Handler();
                    mHandler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            back_pressed = false;
                        }
                    }, 2000L);
                } else {
                    AlertDialog.Builder alert = new AlertDialog.Builder(HomeActivity.this);
                    alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                    alert.setCancelable(false);
                    alert.setMessage(settingsMain.getExitApp("exit"));
                    alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                        finishAffinity();
//                        finish();
                        dialog.dismiss();
                        overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                    });
                    alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                    alert.show();
                }
            } else if (fragmentHomeMulti != null && fragmentHomeMulti.isVisible()) {
                if (!back_pressed) {
                    Toast.makeText(getApplicationContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
                    back_pressed = true;
                    android.os.Handler mHandler = new android.os.Handler();
                    mHandler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            back_pressed = false;
                        }
                    }, 2000L);
                } else {
                    AlertDialog.Builder alert = new AlertDialog.Builder(HomeActivity.this);
                    alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                    alert.setCancelable(false);
                    alert.setMessage(settingsMain.getExitApp("exit"));
                    alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                        finishAffinity();
//                        finish();
                        dialog.dismiss();
                        overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                    });
                    alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                    alert.show();
                }
            } else if (fragmentHomeMarvel != null && fragmentHomeMarvel.isVisible()) {
                if (!back_pressed) {
                    Toast.makeText(getApplicationContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
                    back_pressed = true;
                    android.os.Handler mHandler = new android.os.Handler();
                    mHandler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            back_pressed = false;
                        }
                    }, 2000L);
                } else {
                    AlertDialog.Builder alert = new AlertDialog.Builder(HomeActivity.this);
                    alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                    alert.setCancelable(false);
                    alert.setMessage(settingsMain.getExitApp("exit"));
                    alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                        finishAffinity();
//                        finish();
                        dialog.dismiss();
                        overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                    });
                    alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                    alert.show();
                }
            } else {
                if (HomeActivity.loadingScreen) {

                } else {
                    super.onBackPressed();
                    overridePendingTransition(R.anim.left_enter, R.anim.right_out);
                }
//                super.onBackPressed();
//                overridePendingTransition(R.anim.left_enter, R.anim.right_out);
            }
        }
    }


    @Override
    public void onDestroy() {
        super.onDestroy();
        Admob.adforest_cancelInterstitial();
        Log.d("info onDestroy called", "onDestroy");
    }


    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        // Handle navigation view item clicks here.
        int id = item.getItemId();
        if (!UrlController.loading) {

            item.setChecked(true);

            if (id == R.id.nav_blog) {
                replaceFragment(new BlogFragment(), "BlogFragment");
            }
//        if (id == R.id.nav_night_Mode) {
//            AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES);
//            Toast.makeText(getApplicationContext(), "NightCLicked", Toast.LENGTH_SHORT).show();
//            Intent intent = new Intent(getApplicationContext(), SplashScreen.class);
//            startActivity(intent);
//        }
            if (id == R.id.nav_sellers) {
                replaceFragment(new SellersListFragment(), "SellersListFragment");
            }
            if (id == R.id.nav_shop) {
                Intent intent = new Intent(getApplicationContext(), shopActivity.class);
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
            }
            if (id == R.id.nav_settings) {
                Intent intent = new Intent(getApplicationContext(), Settings.class);
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
            }
            if (id == R.id.nav_language) {

                Intent intent = new Intent(getApplicationContext(), ChooseLanguageActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
            }
            if (id == R.id.nav_location) {
                replaceFragment(new ChooseLocationFragment(), "ChooseLocationFragment");


            }
            if (id == R.id.search) {
                Intent intent = new Intent(getApplicationContext(), SearchActivity.class);
                intent.putExtra("catId", "");
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);

            } else if (id == R.id.profile) {
                replaceFragment(new FragmentProfile(), "FragmentProfile");
            } else if (id == R.id.myAds) {
                if (settingsMain.getAppOpen()) {
                    settingsMain.setUserLogin("0");
                    settingsMain.setFireBaseId("");
                    HomeActivity.this.finish();
                    Intent intent = new Intent(HomeActivity.this, MainActivity.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);
                    overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                } else
                    replaceFragment(new MyAds(), "MyAds");
            } else if (id == R.id.favAds) {
                replaceFragment(new MyAds_Favourite(), "MyAds_Favourite");
            } else if (id == R.id.rejectedAds) {
                replaceFragment(new MyAds_Rejected(), "MyAds_Rejected");
            } else if (id == R.id.expire_sold_Ads) {
                replaceFragment(new Expired_SoldAds(), "MyAdsExpire");
            } else if (id == R.id.most_viewed_Ads) {
                replaceFragment(new Most_ViewedAds(), "Most_ViewedAds");
            } else if (id == R.id.packages) {
                replaceFragment(new PackagesFragment(), "PackagesFragment");
            } else if (id == R.id.home) {
//            replaceFragment(new FragmentHome(), "FragmentHome");

                FragmentManager fm = HomeActivity.this.getSupportFragmentManager();
                for (int i = 0; i < fm.getBackStackEntryCount(); ++i) {
                    fm.popBackStack();
                }
            } else if (id == R.id.inActiveAds) {
                if (settingsMain.getAppOpen()) {
                    settingsMain.setUserLogin("0");
                    settingsMain.setFireBaseId("");
                    HomeActivity.this.finish();
                    Intent intent = new Intent(HomeActivity.this, MainActivity.class);
                    intent.putExtra("page", true);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);
                    overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                } else
                    replaceFragment(new MyAds_Inactive(), "MyAds_Inactive");
            } else if (id == R.id.featureAds) {
                replaceFragment(new MyAds_Featured(), "MyAds_Featured");
            } else if (id == R.id.nav_log_out) {
//            LoginManager.getInstance().logOut();
//            FirebaseMessaging.getInstance().unsubscribeFromTopic(Config.TOPIC_GLOBAL);
//            if (!settingsMain.getUserLogin().equals("0")) {
//
//                ChatUserModel userModel = new ChatUserModel(true, settingsMain.getUserLogin());
//                myRef.child(settingsMain.getUserLogin()).setValue(userModel);
//            }
                AlertDialog.Builder alert = new AlertDialog.Builder(HomeActivity.this);
                alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                alert.setCancelable(false);
                alert.setMessage(settingsMain.getAlertDialogMessage("confirmMessage"));
                alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                    adforest_emptyCart();
                    if (settingsMain.getCheckOpen()) {
                        settingsMain.isAppOpen(true);
                    }
                    dialog.dismiss();
                });
                alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                alert.show();
            } else if (id == R.id.message) {
                Intent intent;
                if (SettingsMain.isWhizzChatActive) {
                    intent = new Intent(HomeActivity.this, WhizzChatList.class);
                } else {
                    intent = new Intent(HomeActivity.this, Message.class);
                }
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
//            replaceFragment(new MessagesFragment(),"MessagesFragment");
            }

            DrawerLayout drawer = (DrawerLayout) findViewById(R.id.drawer_layout);
            drawer.closeDrawer(GravityCompat.START);
        } else {
            return false;
        }
        return true;
    }

    @Override
    protected void onStart() {
        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestEmail()
                .build();
        mGoogleApiClient = new GoogleApiClient.Builder(this)
                .addApi(Auth.GOOGLE_SIGN_IN_API, gso)
                .build();
        mGoogleApiClient.connect();
        super.onStart();
    }

    public void startFragment(Fragment someFragment, String tag) {
        FragmentManager fm = getSupportFragmentManager();
        Fragment fragment = fm.findFragmentByTag(tag);
        if (fragment == null) {
            fragment = someFragment;
            fm.beginTransaction()
                    .add(R.id.frameContainer, fragment, tag).commit();
        }
    }

    public void replaceFragment(Fragment someFragment, String tag) {

        FragmentManager fm = getSupportFragmentManager();
        Fragment fragment = fm.findFragmentByTag(tag);
        Fragment fragment2 = fm.findFragmentById(R.id.frameContainer);

        if (fragment != fragment2) {
            FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
            transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
            transaction.replace(R.id.frameContainer, someFragment, tag);
            transaction.addToBackStack(tag);
            transaction.commit();
        }
    }

    @Override
    protected void onPostResume() {
        super.onPostResume();
    }

    AlertDialog logoutAlert;

    private void showLinkedInLogoutDialog() {
        AlertDialog.Builder dialog = new AlertDialog.Builder(this).setTitle("Logging out LinkedIn").setMessage("Please Wait...");
        WebView wv = new WebView(this);

        WebSettings settings = wv.getSettings();
        settings.setJavaScriptEnabled(true);
        wv.loadUrl("https://linkedin.com/sales/logout");
        wv.setWebViewClient(new WebViewClient() {
            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                final android.app.AlertDialog.Builder builder = new android.app.AlertDialog.Builder(HomeActivity.this);
                builder.setMessage("SSL certificate is not safe!");
                builder.setPositiveButton("continue", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        handler.proceed();
                    }
                });
                builder.setNegativeButton("cancel", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        handler.cancel();
                    }
                });
                final android.app.AlertDialog dialog = builder.create();
                dialog.show();
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                if (url.equals("https://www.linkedin.com/sales/m")) {
//                    if (logoutAlert!=null){
                    LoginManager.getInstance().logOut();
                    FirebaseMessaging.getInstance().unsubscribeFromTopic(Config.TOPIC_GLOBAL);
                    settingsMain.setUserLogin("0");
                    settingsMain.setFireBaseId("");
                    Auth.GoogleSignInApi.signOut(mGoogleApiClient).setResultCallback(
                            new ResultCallback<Status>() {
                                @Override
                                public void onResult(Status status) {
                                    Log.d("GoogleLogout", "Logged Out");

                                }
                            });
                    HomeActivity.this.finish();
                    Intent intent = new Intent(HomeActivity.this, MainActivity.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    startActivity(intent);
                    overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                    adforest_AddFirebaseid();

                }
//                }
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                view.loadUrl(url);

                return true;
            }
        });
        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("waitMessage"), Toast.LENGTH_LONG).show();
    }

    private void adforest_emptyCart() {
        if (SettingsMain.isConnectingToInternet(this)) {
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            frameLayout.setVisibility(View.GONE);
            Call<ResponseBody> myCall = restService.emptyCart(UrlController.AddHeaders(this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info emptyCart Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);
                                frameLayout.setVisibility(View.VISIBLE);
                                if (settingsMain.getLinkedinLogin()) {
                                    showLinkedInLogoutDialog();
                                } else {
                                    LoginManager.getInstance().logOut();
                                    FirebaseMessaging.getInstance().unsubscribeFromTopic(Config.TOPIC_GLOBAL);
                                    settingsMain.setUserLogin("0");
                                    settingsMain.setUserEmail("");
                                    settingsMain.setUserName("");
                                    settingsMain.setUserPassword("");
                                    settingsMain.setFireBaseId("");
                                    Auth.GoogleSignInApi.signOut(mGoogleApiClient).setResultCallback(
                                            new ResultCallback<Status>() {
                                                @Override
                                                public void onResult(Status status) {
                                                    Log.d("GoogleLogout", "Logged Out");

                                                }
                                            });
                                    HomeActivity.this.finish();
                                    Intent intent = new Intent(HomeActivity.this, MainActivity.class);
                                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                                    startActivity(intent);
                                    overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                    adforest_AddFirebaseid();
                                }
                            } else {
//                                if (settingsMain.getLinkedinLogin()){
//                                    showLinkedInLogoutDialog();
//                                }else{
                                LoginManager.getInstance().logOut();
                                FirebaseMessaging.getInstance().unsubscribeFromTopic(Config.TOPIC_GLOBAL);
                                settingsMain.setUserLogin("0");
                                settingsMain.setUserEmail("");
                                settingsMain.setUserName("");
                                settingsMain.setUserPassword("");
                                settingsMain.setFireBaseId("");
                                Auth.GoogleSignInApi.signOut(mGoogleApiClient).setResultCallback(
                                        new ResultCallback<Status>() {
                                            @Override
                                            public void onResult(Status status) {
                                                Log.d("GoogleLogout", "Logged Out");

                                            }
                                        });
                                HomeActivity.this.finish();
                                Intent intent = new Intent(HomeActivity.this, MainActivity.class);
                                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                                startActivity(intent);
                                overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                adforest_AddFirebaseid();
                            }
                        }


//                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    }
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info emptyCart ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        Log.d("info emptyCart err", String.valueOf(t));
                        Log.d("info emptyCart err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            Toast.makeText(this, "Internet error", Toast.LENGTH_SHORT).show();
        }
    }


    private void adforest_AddFirebaseid() {
        if (SettingsMain.isConnectingToInternet(this)) {


            JsonObject params = new JsonObject();


            params.addProperty("firebase_id", "");

            Call<ResponseBody> myCall = restService.postFirebaseId(params, UrlController.AddHeaders(this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info FireBase Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info Data FireBase", response.getJSONObject("data").toString());
                                settingsMain.setFireBaseId(response.getJSONObject("data").getString("firebase_reg_id"));
                                Log.d("info FireBase ID", response.getJSONObject("data").getString("firebase_reg_id"));
                                Log.d("info FireBase", "Firebase id is set with server.!");
                            }
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    }
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info FireBase ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        Log.d("info FireBase err", String.valueOf(t));
                        Log.d("info FireBase err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            Toast.makeText(this, "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    private void adforest_changeNearByStatus(final String nearby_latitude,
                                             final String nearby_longitude, final String nearby_distance) {
        if (SettingsMain.isConnectingToInternet(this)) {


            JsonObject params = new JsonObject();
            params.addProperty("nearby_latitude", nearby_latitude);
            params.addProperty("nearby_longitude", nearby_longitude);
            params.addProperty("nearby_distance", nearby_distance);
            Log.d("info SendNearBy Status", params.toString());
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            Call<ResponseBody> myCall = restService.postChangeNearByStatus(params, UrlController.AddHeaders(this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info NearBy Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Toast.makeText(getApplicationContext(), response.getString("message"), Toast.LENGTH_SHORT).show();
                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);
                                FragmentManager fm = getSupportFragmentManager();
                                Fragment fragment = fm.findFragmentByTag("FragmentCatSubNSearch");
                                Fragment fragment2 = fm.findFragmentById(R.id.frameContainer);

                                FragmentCatSubNSearch fragment_search = new FragmentCatSubNSearch();
                                Bundle bundle = new Bundle();
                                bundle.putString("nearby_latitude", nearby_latitude);
                                bundle.putString("nearby_longitude", nearby_longitude);
                                bundle.putString("nearby_distance", nearby_distance);

                                settingsMain.setLatitude(nearby_latitude);
                                settingsMain.setLongitude(nearby_longitude);
                                settingsMain.setDistance(nearby_distance);

                                fragment_search.setArguments(bundle);

                                if (fragment != fragment2) {
                                    replaceFragment(fragment_search, "FragmentCatSubNSearch");
                                } else {
                                    updatfrag.updatefrag(nearby_latitude, nearby_longitude, nearby_distance);
                                }

                            } else
                                Toast.makeText(getApplicationContext(), response.getString("message"), Toast.LENGTH_SHORT).show();
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        e.printStackTrace();
                    }
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info FireBase ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        Log.d("info FireBase err", String.valueOf(t));
                        Log.d("info FireBase err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            Toast.makeText(this, "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onSuccessPermission(int code) {
        if (code == 1) {
            adforest_loctionSearch();
        }
        // check phone verification
        if (code == 2) {
            if (FragmentHome.Ad_post) {
                Intent intent = new Intent(HomeActivity.this, AddNewAdPost.class);
                startActivity(intent);
            } else if (FragmentHomeMarvel.Ad_post) {
                Intent intent = new Intent(HomeActivity.this, AddNewAdPost.class);
                startActivity(intent);
            } else {
                //Alert dialog for exit form Home screen
                AlertDialog.Builder alert = new AlertDialog.Builder(HomeActivity.this);
                alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                alert.setCancelable(false);
                alert.setMessage(FragmentHome.Verfiedmessage);
                alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                    FragmentProfile fragmentProfile = new FragmentProfile();
                    FragmentTransaction fragmentTransaction = getSupportFragmentManager().beginTransaction();
                    fragmentTransaction.replace(R.id.frameContainer, fragmentProfile).addToBackStack(null).commit();
                    dialog.dismiss();
                });
                alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                alert.show();
            }
        }
    }


    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {
        Toast.makeText(this,
                "Google Places API connection failed with error code:" +
                        connectionResult.getErrorCode(),
                Toast.LENGTH_LONG).show();
    }

    @Override
    public void onConnected(@Nullable Bundle bundle) {

    }

    @Override
    public void onConnectionSuspended(int i) {
    }

    public interface UpdateFragment {
        void updatefrag(String s);

        void updatefrag(String latitude, String longitude, String distance);
    }


}