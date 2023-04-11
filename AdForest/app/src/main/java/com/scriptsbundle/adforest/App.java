package com.scriptsbundle.adforest;

import android.app.Application;
import android.content.Context;
import android.content.pm.PackageManager;
import android.util.Log;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatDelegate;
import androidx.multidex.MultiDex;

import com.facebook.drawee.backends.pipeline.Fresco;
import com.google.android.gms.ads.MobileAds;
import com.google.android.gms.ads.initialization.InitializationStatus;
import com.google.android.gms.ads.initialization.OnInitializationCompleteListener;
import com.google.android.libraries.places.api.Places;
import com.paypal.checkout.config.CheckoutConfig;
import com.paypal.checkout.config.Environment;
import com.paypal.checkout.config.SettingsConfig;
import com.paypal.checkout.createorder.CurrencyCode;
import com.paypal.checkout.createorder.UserAction;


import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.LocaleHelper;
import com.scriptsbundle.adforest.utills.NoInternet.AppLifeCycleManager;
import timber.log.Timber;

public class App extends Application {
    private static App instance;

    public static App getInstance() {
        return instance;
    }
    @Override
    public void onCreate() {
        super.onCreate();
        Fresco.initialize(this);
        MobileAds.initialize(this, initializationStatus -> Timber.d("AdMob initialized"));
        AppLifeCycleManager.init(this);
        String apiKey = getString(R.string.places_api_key);
        // Setup Places Client
        if (!Places.isInitialized()) {
            Places.initialize(getApplicationContext(), apiKey);
        }
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(LocaleHelper.onAttach(base, "en"));
        MultiDex.install(this);
    }
}
