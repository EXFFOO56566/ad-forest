package com.scriptsbundle.adforest.utills;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import androidx.annotation.NonNull;
import androidx.coordinatorlayout.widget.CoordinatorLayout;

import com.google.android.gms.ads.AdError;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.FullScreenContentCallback;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.ads.interstitial.InterstitialAd;
import com.google.android.gms.ads.interstitial.InterstitialAdLoadCallback;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.ScheduledFuture;
import java.util.concurrent.TimeUnit;

import com.scriptsbundle.adforest.utills.NoInternet.AppLifeCycleManager;

import timber.log.Timber;

/**
 * Created by apple on 11/21/17.
 */

public class Admob extends Activity {
    public static final String TAG = Admob.class.getSimpleName();
    static Runnable loader = null;
    private static SettingsMain settingsMain;
    private static ScheduledFuture loaderHandler;
    private static boolean checkInterstitalLoad = false;
    private static InterstitialAd mInterstitialAd;

    public static void loadInterstitial(final Activity activity) {
        checkInterstitalLoad = false;
        settingsMain = new SettingsMain(activity);
        final AppLifeCycleManager appLifeCycleManager = AppLifeCycleManager.get(activity);
        try {
            loader = new Runnable() {
                @Override
                public void run() {
                    if (!checkInterstitalLoad && appLifeCycleManager.isForeground()) {
                        Log.d(TAG, "Loading Admob interstitial...");
                        activity.runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                AdRequest adRequest = new AdRequest.Builder().build();

                                InterstitialAd.load(activity, settingsMain.getInterstitialAdsId(), adRequest, new InterstitialAdLoadCallback() {
                                    @Override
                                    public void onAdLoaded(@NonNull InterstitialAd interstitialAd) {
                                        super.onAdLoaded(interstitialAd);
                                        adforest_ADsdisplayInterstitial(interstitialAd, activity);
                                        mInterstitialAd = interstitialAd;


                                    }

                                    @Override
                                    public void onAdFailedToLoad(@NonNull LoadAdError loadAdError) {
                                        super.onAdFailedToLoad(loadAdError);
                                        Log.d(TAG, "Ad failed to loadvand error code is " + loadAdError);

                                    }
                                });
                            }
                        });
                    }
                }
            };

            ScheduledExecutorService scheduler = Executors.newScheduledThreadPool(1);
            loaderHandler = scheduler.scheduleWithFixedDelay(loader, 30, 30, TimeUnit.SECONDS);
        } catch (Exception e) {
            Log.d("AdException===>", e.toString());
        }
    }

    @SuppressLint("MissingPermission")
    public static void adforest_DisplaybannersForAdDetail(final Activity activity, final LinearLayout frameLayout, RelativeLayout layout, FloatingActionButton fab) {
        Log.d(TAG, "Loading Admob Banner...");

        settingsMain = new SettingsMain(activity);
        final AdView mAdView = new AdView(activity);
        mAdView.setAdSize(AdSize.BANNER);
        mAdView.setAdUnitId(settingsMain.getBannerAdsId());
        frameLayout.addView(mAdView);
        AdRequest adRequest = new AdRequest.Builder().build();
        mAdView.loadAd(adRequest);

        mAdView.setAdListener(new AdListener() {
            @Override
            public void onAdClosed() {
            }


            @Override
            public void onAdFailedToLoad(LoadAdError error) {
                adDetailInterface.updateUi();
            }

            @Override
            public void onAdOpened() {
            }


            @Override
            public void onAdLoaded() {
                frameLayout.setVisibility(View.VISIBLE);
                settingsMain.setAdShowOrNot(false);
                Log.d(TAG, "Ad has has loaded to load");
            }
        });
    }

    static UpdateAdDetail adDetailInterface;

    public static void setInterface(UpdateAdDetail adDetailInterface) {
        Admob.adDetailInterface = adDetailInterface;
    }

    @SuppressLint("MissingPermission")
    public static void adforest_Displaybanners(final Activity activity, final LinearLayout frameLayout) {
        Log.d(TAG, "Loading Admob Banner...");

        settingsMain = new SettingsMain(activity);
        final AdView mAdView = new AdView(activity);

        mAdView.setAdSize(AdSize.BANNER);
        mAdView.setAdUnitId(settingsMain.getBannerAdsId());
        frameLayout.addView(mAdView);
        AdRequest adRequest = new AdRequest.Builder().build();
        mAdView.loadAd(adRequest);
        mAdView.setAdListener(new AdListener() {
            @Override
            public void onAdClosed() {
                super.onAdClosed();
            }

            @Override
            public void onAdFailedToLoad(@NonNull LoadAdError loadAdError) {
                super.onAdFailedToLoad(loadAdError);
                Timber.d("Ad failed to load Error code is %s", loadAdError.toString());

            }

            @Override
            public void onAdOpened() {
                super.onAdOpened();
            }

            @Override
            public void onAdLoaded() {
                super.onAdLoaded();
                frameLayout.setVisibility(View.VISIBLE);
                settingsMain.setAdShowOrNot(false);
                Timber.d("Ad has has loaded to load");
            }

            @Override
            public void onAdClicked() {
                super.onAdClicked();
            }

            @Override
            public void onAdImpression() {
                super.onAdImpression();
            }
        });
    }

    public static void adforest_ADsdisplayInterstitial(InterstitialAd interstitialAd, Activity activity) {
        interstitialAd.show(activity);
        checkInterstitalLoad = true;
    }

    public static void adforest_cancelInterstitial() {
        if (loaderHandler != null) {
            loaderHandler.cancel(true);
        }
    }

    @Override
    public void onPointerCaptureChanged(boolean hasCapture) {

    }

    public interface UpdateAdDetail {
        void updateUi();
    }
}


