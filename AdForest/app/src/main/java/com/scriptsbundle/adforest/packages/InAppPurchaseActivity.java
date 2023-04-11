package com.scriptsbundle.adforest.packages;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Build;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import android.util.Log;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Toast;

import com.android.billingclient.api.BillingClient;
import com.android.billingclient.api.BillingClientStateListener;
import com.android.billingclient.api.BillingFlowParams;
import com.android.billingclient.api.BillingResult;
import com.android.billingclient.api.ConsumeParams;
import com.android.billingclient.api.ConsumeResponseListener;
import com.android.billingclient.api.Purchase;
import com.android.billingclient.api.PurchasesUpdatedListener;
import com.android.billingclient.api.SkuDetails;
import com.android.billingclient.api.SkuDetailsParams;
import com.android.billingclient.api.SkuDetailsResponseListener;
import com.google.gson.JsonObject;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeoutException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

public class InAppPurchaseActivity extends AppCompatActivity{
    private static String LICENSE_KEY = "";
    String productId, packageId, packageType, activityName, billing_error, no_market = "", one_time = "";
    RestService restService;
    SettingsMain settingsMain;
    // PUT YOUR MERCHANT KEY HERE;
    // put your Google merchant id here (as stated in public profile of your Payments Merchant Center)
    // if filled library will provide protection against Freedom alike Play Market simulators

    BillingClient billingClient;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_in_app_purchase);
        if (!getIntent().getStringExtra("id").equals("")) {
            packageId = getIntent().getStringExtra("id");
            packageType = getIntent().getStringExtra("packageType");
            productId = getIntent().getStringExtra("porductId");
            activityName = getIntent().getStringExtra("activityName");
            billing_error = getIntent().getStringExtra("billing_error");
            no_market = getIntent().getStringExtra("no_market");
            one_time = getIntent().getStringExtra("one_time");
            LICENSE_KEY = getIntent().getStringExtra("LICENSE_KEY");

        }
        settingsMain = new SettingsMain(this);
        if (getSupportActionBar() != null)
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            Window window = getWindow();
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(Color.parseColor(settingsMain.getMainColor()));
        }
        getSupportActionBar().setBackgroundDrawable(new ColorDrawable(Color.parseColor(settingsMain.getMainColor())));

        restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), this);
        setTitle(activityName);
//        bp = new BillingProcessor(this, LICENSE_KEY, this);
//
//
//        new Handler().postDelayed(new Runnable() {
//            @Override
//            public void run() {
//                if (BillingProcessor.isIabServiceAvailable(InAppPurchaseActivity.this)) {
//                    if (bp.isOneTimePurchaseSupported()) {
//
//                        bp.consumePurchase(porductId);
//                        bp.purchase(InAppPurchaseActivity.this, porductId);
//                    } else {
//                        if (one_time.equals(""))
//                            Toast.makeText(InAppPurchaseActivity.this, "One Time Purchase not Supported on your Device.", Toast.LENGTH_SHORT).show();
//                        else
//                            Toast.makeText(InAppPurchaseActivity.this, one_time, Toast.LENGTH_SHORT).show();
//                    }
//                } else if (no_market.equals(""))
//                    Toast.makeText(InAppPurchaseActivity.this, "Play Market app is not installed.", Toast.LENGTH_SHORT).show();
//                else
//                    Toast.makeText(InAppPurchaseActivity.this, no_market, Toast.LENGTH_SHORT).show();
//            }
//        }, 200);

        billingClient = BillingClient.newBuilder(this)
                .setListener(purchasesUpdatedListener)
                .enablePendingPurchases()
                .build();

        billingClient.startConnection(new BillingClientStateListener() {
            @Override
            public void onBillingSetupFinished(BillingResult billingResult) {
                if (billingResult.getResponseCode() ==  BillingClient.BillingResponseCode.OK) {
                    SkuDetailsParams.Builder params = SkuDetailsParams.newBuilder();
                    List<String> skuList = new ArrayList<>();
                    skuList.add(productId);
                    params.setSkusList(skuList).setType(BillingClient.SkuType.INAPP);
                    billingClient.querySkuDetailsAsync(params.build(),
                            new SkuDetailsResponseListener() {
                                @Override
                                public void onSkuDetailsResponse(BillingResult billingResult,
                                                                 List<SkuDetails> skuDetailsList) {
                                    BillingFlowParams billingFlowParams = BillingFlowParams.newBuilder()
                                            .setSkuDetails(skuDetailsList.get(0))
                                            .build();
                                    int responseCode = billingClient.launchBillingFlow(InAppPurchaseActivity.this, billingFlowParams).getResponseCode();
                                }
                            });
                    // The BillingClient is ready. You can query purchases here.
                }
            }
            @Override
            public void onBillingServiceDisconnected() {
                // Try to restart the connection on the next request to
                // Google Play by calling the startConnection() method.
            }
        });
    }

    private void adforest_Checkout() {

        if (SettingsMain.isConnectingToInternet(InAppPurchaseActivity.this)) {

            JsonObject params = new JsonObject();
            params.addProperty("package_id", packageId);
            params.addProperty("payment_from", packageType);
            Log.d("info Send Checkout", params.toString());

            Call<ResponseBody> myCall = restService.postCheckout(params, UrlController.AddHeaders(this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info Checkout Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            Log.d("info Checkout object", "" + response.toString());
                            if (response.getBoolean("success")) {
                                settingsMain.setPaymentCompletedMessage(response.get("message").toString());
                                adforest_getDataForThankYou();
                            } else
                                Toast.makeText(InAppPurchaseActivity.this, response.get("message").toString(), Toast.LENGTH_SHORT).show();

                        }
                    } catch (JSONException e) {
                        SettingsMain.hideDilog();
                        e.printStackTrace();
                    } catch (IOException e) {
                        SettingsMain.hideDilog();
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        settingsMain.hideDilog();
                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getApplicationContext(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        settingsMain.hideDilog();
                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info Checkout ", "NullPointert Exception" + t.getLocalizedMessage());
                        settingsMain.hideDilog();
                    } else {
                        SettingsMain.hideDilog();
                        Log.d("info Checkout err", String.valueOf(t));
                        Log.d("info Checkout err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            SettingsMain.hideDilog();
            Toast.makeText(InAppPurchaseActivity.this, settingsMain.getAlertDialogTitle("error"), Toast.LENGTH_SHORT).show();
        }
    }

    public void adforest_getDataForThankYou() {
        if (SettingsMain.isConnectingToInternet(InAppPurchaseActivity.this)) {
            Call<ResponseBody> myCall = restService.getPaymentCompleteData(UrlController.AddHeaders(InAppPurchaseActivity.this));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info ThankYou Details", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                JSONObject responseData = response.getJSONObject("data");

                                Log.d("info ThankYou object", "" + response.getJSONObject("data"));

                                Intent intent = new Intent(InAppPurchaseActivity.this, Thankyou.class);
                                intent.putExtra("data", responseData.getString("data"));
                                intent.putExtra("order_thankyou_title", responseData.getString("order_thankyou_title"));
                                intent.putExtra("order_thankyou_btn", responseData.getString("order_thankyou_btn"));
                                startActivity(intent);
//                                SettingsMain.hideDilog();
                                InAppPurchaseActivity.this.finish();
                            } else {
                                SettingsMain.hideDilog();
                                Toast.makeText(InAppPurchaseActivity.this, response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                        SettingsMain.hideDilog();
                    } catch (IOException e) {
                        e.printStackTrace();
                        SettingsMain.hideDilog();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    SettingsMain.hideDilog();
                    Log.d("info ThankYou error", String.valueOf(t));
                    Log.d("info ThankYou error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            SettingsMain.hideDilog();
            Toast.makeText(InAppPurchaseActivity.this, "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onDestroy() {
        if (billingClient != null) {
            billingClient.endConnection();
        }
        super.onDestroy();
    }

    public void showToast(String message){
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
    }


    private PurchasesUpdatedListener purchasesUpdatedListener = new PurchasesUpdatedListener() {
        @Override
        public void onPurchasesUpdated(BillingResult billingResult, List<Purchase> purchases) {
            if (billingResult.getResponseCode() == BillingClient.BillingResponseCode.OK
                    && purchases != null) {
                for (Purchase purchase : purchases) {
                    ConsumeParams consumeParams =
                            ConsumeParams.newBuilder()
                                    .setPurchaseToken(purchase.getPurchaseToken())
                                    .build();
                    billingClient.consumeAsync(consumeParams,listener);
                }
            } else if (billingResult.getResponseCode() == BillingClient.BillingResponseCode.USER_CANCELED) {
                showToast(billing_error);
                finish();
            } else {
                showToast(billing_error);
                finish();
            }

        }
    };

    ConsumeResponseListener listener = (billingResult, purchaseToken) -> {
        if (billingResult.getResponseCode() == BillingClient.BillingResponseCode.OK) {
            adforest_Checkout();
        }
    };
}
