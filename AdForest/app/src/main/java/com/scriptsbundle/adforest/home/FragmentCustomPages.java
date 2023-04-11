package com.scriptsbundle.adforest.home;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.net.http.SslError;
import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.appcompat.widget.Toolbar;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.HttpAuthHandler;
import android.webkit.SslErrorHandler;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.google.gson.JsonObject;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.TimeoutException;

import okhttp3.Credentials;
import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.utills.AnalyticsTrackers;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;


public class FragmentCustomPages extends Fragment {

    WebView webView;
    String pageId, pageURL, pageType;
    String pageTitle;
    SettingsMain settingsMain;
    RestService restService;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    RelativeLayout mainRelative;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Bundle bundle = this.getArguments();
        if (bundle != null) {
            pageId = bundle.getString("id", "");
            pageURL = bundle.getString("page_url", "");
            pageTitle = bundle.getString("pageTitle", "");
//            pageType=bundle.getString("pageType","");
        }

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_catgory_detaillist, container, false);
        settingsMain = new SettingsMain(getActivity());
        if (settingsMain.getAppOpen()) {
            restService = UrlController.createService(RestService.class);
        } else
            restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), getActivity());
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        mainRelative = view.findViewById(R.id.mainRelative);
        webView = view.findViewById(R.id.webView);
        HomeActivity.loadingScreen = true;

        webView.setScrollContainer(false);
        webView.setWebViewClient(new WebViewClient(){
            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                final AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
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
                final AlertDialog dialog = builder.create();
                dialog.show();
            }
        });

//        SwipeRefreshLayout swipeRefreshLayout = getActivity().findViewById(R.id.swipe_refresh_layout);
//        swipeRefreshLayout.setEnabled(false);

        if (pageURL.isEmpty()) {
            adforest_initialize();
        } else {
            adforest_getData(pageURL);
        }

        return view;
    }
    private void adforest_initialize() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("page_id", pageId);
            Log.d("pagekiid", pageId.toString());
            Log.d("info Send CustomPages =", "" + params.toString());

            Call<ResponseBody> myCall = restService.postGetCustomePages(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info CustomPages Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info CustomPages object", "" + response.getJSONObject("data"));
                                pageTitle = response.getJSONObject("data").getString("page_title");
//                                pageType=response.getJSONObject("data").getString("type");

                                setScreenName(pageTitle);
                                Log.d("pagetitle", response.getJSONObject("data").toString());
                                getActivity().setTitle(pageTitle);
                                webView.loadData(response.getJSONObject("data").getString("page_content"), "text/html", "UTF-8");
                                HomeActivity.loadingScreen = false;

                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);
                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    }
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    mainRelative.setVisibility(View.VISIBLE);

                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info CustomPages ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);
                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                        Log.d("info CustomPages err", String.valueOf(t));
                        Log.d("info CustomPages err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            mainRelative.setVisibility(View.VISIBLE);

            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }

    }

    public void replaceFragment(Fragment someFragment, String tag) {
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, someFragment, tag);
        transaction.addToBackStack(tag);
        transaction.commit();
    }

    public void setScreenName(String pageTitle) {
        try {
            if (settingsMain.getAnalyticsShow() && !settingsMain.getAnalyticsId().equals(""))
                AnalyticsTrackers.getInstance().trackScreenView(pageTitle);

            super.onResume();
        } catch (IllegalStateException e) {
            e.printStackTrace();
        }

    }

    @SuppressLint("SetJavaScriptEnabled")
    private void adforest_getData(String url) {
        getActivity().setTitle(pageTitle);
        Log.d("pageurltitle", pageTitle.toString());
        webView.getSettings().setJavaScriptEnabled(true); // enable javascript
        webView.setWebViewClient(new WebViewClient() {
            @SuppressWarnings("deprecation")
            @Override
            public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
                Toast.makeText(getContext(), description, Toast.LENGTH_SHORT).show();
            }

            @TargetApi(android.os.Build.VERSION_CODES.M)
            @Override
            public void onReceivedError(WebView view, WebResourceRequest req, WebResourceError rerr) {
                // Redirect to deprecated method, so you can use it in all SDK versions
                onReceivedError(view, rerr.getErrorCode(), rerr.getDescription().toString(), req.getUrl().toString());
            }
        });

        final Map<String, String> map = new HashMap<>();
        map.put("Adforest-Shop-Request", "body");
        if (settingsMain.getAppOpen()) {
            webView.loadUrl(url, map);
        } else {
            String authToken = Credentials.basic(settingsMain.getUserEmail(), settingsMain.getUserPassword());
            map.put("Authorization", authToken);
            if (SettingsMain.isSocial(getContext())) {
                map.put("AdForest-Login-Type", "social");
            }
            webView.loadUrl(url, map);
            HomeActivity.loadingScreen = false;

        }
        webView.setWebViewClient(new WebViewClient() {

            @Override
            public void onReceivedHttpAuthRequest(WebView view,
                                                  HttpAuthHandler handler, String host, String realm) {
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                view.loadUrl(url, map);
                return true;
            }

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                loadingLayout.setVisibility(View.VISIBLE);
                shimmerFrameLayout.setVisibility(View.VISIBLE);
                shimmerFrameLayout.startShimmer();
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                shimmerFrameLayout.stopShimmer();
                shimmerFrameLayout.setVisibility(View.GONE);
                loadingLayout.setVisibility(View.GONE);
                mainRelative.setVisibility(View.VISIBLE);

            }

        });

//        Map<String, String> map = new HashMap<>();
//        String authToken = Credentials.basic(settingsMain.getUserEmail(), settingsMain.getUserPassword());
//        map.put("Authorization",authToken);
//        if (SettingsMain.isSocial(context)) {
//            map.put("AdForest-Login-Type", "social");
//        }
//        Log.d("info",map.toString());
//        webView.loadUrl("http://adforest-testapp.scriptsbundle.com/shop/",map);
    }

}