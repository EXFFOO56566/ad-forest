package com.scriptsbundle.adforest.profile;


import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.drawable.LayerDrawable;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RatingBar;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.facebook.shimmer.ShimmerFrameLayout;

import com.google.gson.JsonObject;
import com.squareup.picasso.Picasso;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.ArrayList;
import java.util.concurrent.TimeoutException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.ad_detail.Ad_detail_activity;
import com.scriptsbundle.adforest.helper.MyAdsOnclicklinstener;
import com.scriptsbundle.adforest.home.EditAdPost;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.modelsList.myAdsModel;
import com.scriptsbundle.adforest.profile.adapter.ItemExpireAdsAdapter;
import com.scriptsbundle.adforest.profile.adapter.ItemMyAdsAdapter;
import com.scriptsbundle.adforest.utills.AnalyticsTrackers;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.NestedScroll;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.RuntimePermissionHelper;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

public class Expired_SoldAds extends Fragment implements RuntimePermissionHelper.permissionInterface {

    SettingsMain settingsMain;
    TextView verifyBtn, textViewRateNo, textViewUserName, textViewLastLogin;
    TextView editProfBtn, textViewAdsSold, textViewTotalList, textViewInactiveAds, textViewEmptyData, textViewExppiry;
    //    ItemMyAdsAdapter adapter;
    ItemExpireAdsAdapter adapter;
    RecyclerView recyclerView;
    RatingBar ratingBar;
    ImageView imageViewProfile;
    int nextPage = 1;
    boolean loading = true, hasNextPage = false;
    ProgressBar progressBar;
    NestedScrollView nestedScrollView;
    RestService restService;
    RuntimePermissionHelper runtimePermissionHelper;
    String adID;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    RelativeLayout mainRelative;
    private ArrayList<myAdsModel> list = new ArrayList<>();

    public Expired_SoldAds() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_myadd, container, false);

        settingsMain = new SettingsMain(getActivity());
        runtimePermissionHelper = new RuntimePermissionHelper(getActivity(), this);
        HomeActivity.loadingScreen = true;

        progressBar = view.findViewById(R.id.progressBar4);
        nestedScrollView = view.findViewById(R.id.mainScrollView);
        progressBar.setVisibility(View.GONE);

        textViewLastLogin = view.findViewById(R.id.loginTime);
        verifyBtn = view.findViewById(R.id.verified);
        textViewRateNo = view.findViewById(R.id.numberOfRate);
        textViewUserName = view.findViewById(R.id.text_viewName);

        editProfBtn = view.findViewById(R.id.editProfile);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        mainRelative = view.findViewById(R.id.mainRelative);
        textViewEmptyData = view.findViewById(R.id.textView5);
        textViewEmptyData.setVisibility(View.GONE);
        imageViewProfile = view.findViewById(R.id.image_view);
        ratingBar = view.findViewById(R.id.ratingBar);
        LayerDrawable stars = (LayerDrawable) this.ratingBar.getProgressDrawable();
        stars.getDrawable(2).setColorFilter(Color.parseColor("#ffcc00"), PorterDuff.Mode.SRC_ATOP);

        textViewAdsSold = view.findViewById(R.id.share);
        textViewTotalList = view.findViewById(R.id.addfav);
        textViewInactiveAds = view.findViewById(R.id.report);
        textViewExppiry = view.findViewById(R.id.expired);

        editProfBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                replaceFragment(new EditProfile(), "EditProfile");
            }
        });

        recyclerView = view.findViewById(R.id.cardView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setNestedScrollingEnabled(false);

        final GridLayoutManager MyLayoutManager = new GridLayoutManager(getActivity(), 2);
        MyLayoutManager.setOrientation(RecyclerView.VERTICAL);
        recyclerView.setLayoutManager(MyLayoutManager);
        restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), getActivity());

        nestedScrollView.setOnScrollChangeListener(new NestedScroll() {
            @Override
            public void onScroll() {

                if (loading) {
                    loading = false;
                    Log.d("info data object", "sdfasdfadsasdfasdfasdf");

                    if (hasNextPage) {
                        progressBar.setVisibility(View.VISIBLE);
                        adforest_loadMore(nextPage);
                    }
                }
            }
        });

        ratingBar.setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                if (event.getAction() == MotionEvent.ACTION_UP) {
                    RatingFragment fragment = new RatingFragment();
                    Bundle bundle = new Bundle();
                    bundle.putString("id", settingsMain.getUserLogin());
                    bundle.putBoolean("isprofile", true);
                    fragment.setArguments(bundle);

                    replaceFragment(fragment, "RatingFragment");
                }
                return true;
            }
        });

        SwipeRefreshLayout swipeRefreshLayout = getActivity().findViewById(R.id.swipe_refresh_layout);
        swipeRefreshLayout.setEnabled(true);

        adforest_loadData();

        return view;
    }

    private void adforest_loadMore(int nextPag) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            JsonObject params = new JsonObject();
            params.addProperty("page_number", nextPag);

            Log.d("info sendMyAds Loadmore", params.toString());

            Call<ResponseBody> myCall = restService.postGetLoadMoreMyAds(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info MyAdsMore Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info MyAdsMore object", "" + response.getJSONObject("data"));

                                JSONObject jsonObjectPagination = response.getJSONObject("data").getJSONObject("pagination");

                                nextPage = jsonObjectPagination.getInt("next_page");
                                hasNextPage = jsonObjectPagination.getBoolean("has_next_page");

                                loadMoreList(response.getJSONObject("data"), response.getJSONObject("data").getJSONObject("text"));

                                loading = true;
                                adapter.notifyDataSetChanged();
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

                    progressBar.setVisibility(View.GONE);
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
                        Log.d("info MyAdsMore ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);
                        Log.d("info MyAdsMore err", String.valueOf(t));
                        Log.d("info MyAdsMore err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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


    private void adforest_setAllViewsText(JSONObject jsonObject) {
        try {
            textViewLastLogin.setText(jsonObject.getString("last_login"));
            textViewUserName.setText(jsonObject.getString("display_name"));

            Picasso.get().load(jsonObject.getString("profile_img"))
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(imageViewProfile);

            verifyBtn.setText(jsonObject.getJSONObject("verify_buton").getString("text"));
            verifyBtn.setBackground(CustomBorderDrawable.customButton(0, 0, 0, 0,
                    jsonObject.getJSONObject("verify_buton").getString("color"),
                    jsonObject.getJSONObject("verify_buton").getString("color"),
                    jsonObject.getJSONObject("verify_buton").getString("color"), 3));

            textViewAdsSold.setText(jsonObject.getString("ads_sold"));
            textViewTotalList.setText(jsonObject.getString("ads_total"));
            textViewInactiveAds.setText(jsonObject.getString("ads_inactive"));
            textViewExppiry.setText(jsonObject.getString("ads_expired"));

            ratingBar.setNumStars(5);
            ratingBar.setRating(Float.parseFloat(jsonObject.getJSONObject("rate_bar").getString("number")));
            textViewRateNo.setText(jsonObject.getJSONObject("rate_bar").getString("text"));

            editProfBtn.setText(jsonObject.getString("edit_text"));
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            mainRelative.setVisibility(View.VISIBLE);
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    public void replaceFragment(Fragment someFragment, String tag) {
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, someFragment, tag);
        transaction.addToBackStack(tag);
        transaction.commit();
    }

    private void adforest_loadData() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            if (!HomeActivity.checkLoading)
                loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            Call<ResponseBody> myCall = restService.getExpiredandSoldAds(UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info MyAds Responce", "" + responseObj.toString());
                            HomeActivity.checkLoading = false;

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info MyAds data", "" + response.getJSONObject("data"));
                                Log.d("info MyAds Profile Data", "" + response.getJSONObject("data").getJSONObject("profile"));

                                JSONObject jsonObjectPagination = response.getJSONObject("data").getJSONObject("pagination");

                                nextPage = jsonObjectPagination.getInt("next_page");
                                hasNextPage = jsonObjectPagination.getBoolean("has_next_page");
                                getActivity().setTitle(response.getJSONObject("data").getString("page_title"));

                                makeList(response.getJSONObject("data"), response.getJSONObject("data").getJSONObject("text"));

                                adforest_setAllViewsText(response.getJSONObject("data").getJSONObject("profile"));
                                HomeActivity.loadingScreen = false;

                                if (list.size() > 0) {
                                    adapter = new ItemExpireAdsAdapter(getActivity(), list);
                                    recyclerView.setAdapter(adapter);
                                    adapter.setOnItemClickListener(new MyAdsOnclicklinstener() {
                                        @Override
                                        public void onItemClick(myAdsModel item) {

                                            Intent intent = new Intent(getActivity(), Ad_detail_activity.class);
                                            intent.putExtra("adId", item.getAdId());
                                            getActivity().startActivity(intent);
                                            getActivity().overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                        }

                                        @Override
                                        public void delViewOnClick(final View v, int position) {
                                            //Toast.makeText(getContext(), v.getTag().toString(), Toast.LENGTH_LONG).show();

                                            AlertDialog.Builder alert = new AlertDialog.Builder(getActivity());
                                            alert.setTitle(settingsMain.getGenericAlertTitle());
                                            alert.setCancelable(false);
                                            alert.setMessage(settingsMain.getGenericAlertMessage());
                                            alert.setPositiveButton(settingsMain.getGenericAlertOkText(), new DialogInterface.OnClickListener() {
                                                @Override
                                                public void onClick(DialogInterface dialog,
                                                                    int which) {
                                                    del(v.getTag().toString());
                                                    dialog.dismiss();
                                                }
                                            });
                                            alert.setNegativeButton(settingsMain.getGenericAlertCancelText(), new DialogInterface.OnClickListener() {
                                                @Override
                                                public void onClick(DialogInterface dialogInterface, int i) {
                                                    dialogInterface.dismiss();
                                                }
                                            });
                                            alert.show();
                                        }

                                        @Override
                                        public void editViewOnClick(View v, int position) {
                                            adID = v.getTag().toString();
                                            runtimePermissionHelper.requestLocationPermission(1);

                                        }
                                    });
                                } else {
                                    textViewEmptyData.setVisibility(View.VISIBLE);
                                    textViewEmptyData.setText(response.get("message").toString());
                                }
                            } else {
                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);
                                mainRelative.setVisibility(View.VISIBLE);
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
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    mainRelative.setVisibility(View.VISIBLE);
                    Log.d("info MyAds error", String.valueOf(t));
                    Log.d("info MyAds error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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

    private void del(String tag) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("ad_id", tag);
            Log.d("info Send MyAds Delete", tag);

            Call<ResponseBody> myCall = restService.deleteMyAds(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info MyAds Delete Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info MyAds Delete obj", "" + response.get("message"));
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                reload();
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
                        Log.d("info MyAds Delete ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);
                        Log.d("info MyAds Delete err", String.valueOf(t));
                        Log.d("info MyAds Delete err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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

    void makeList(JSONObject data, JSONObject texts) {
        list.clear();

        try {
            JSONArray jsonArray = data.getJSONArray("ads");

            Log.d("jsonaarry is = ", jsonArray.toString());
            if (jsonArray.length() > 0)
                for (int i = 0; i < jsonArray.length(); i++) {

                    myAdsModel item = new myAdsModel();
                    JSONObject object = jsonArray.getJSONObject(i);

                    item.setAdId(object.getString("ad_id"));
                    item.setName(object.getString("ad_title"));
                    item.setAdStatus(object.getJSONObject("ad_status").getString("status"));
                    item.setAdStatusValue(object.getJSONObject("ad_status").getString("status_text"));
                    item.setPrice(object.getJSONObject("ad_price").getString("price"));
                    item.setImage((object.getJSONArray("ad_images").getJSONObject(0).getString("thumb")));

                    item.setDelAd(texts.getString("delete_text"));
                    item.setEditAd(texts.getString("edit_text"));


                    item.setAdType(texts.getString("ad_type"));

                    item.setSpinerData(texts.getJSONArray("status_dropdown_name"));
                    item.setSpinerValue(texts.getJSONArray("status_dropdown_value"));

                    list.add(item);
                }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    void loadMoreList(JSONObject data, JSONObject texts) {
        try {
            JSONArray jsonArray = data.getJSONArray("ads");

            Log.d("jsonaarry is = ", jsonArray.toString());
            if (jsonArray.length() > 0)
                for (int i = 0; i < jsonArray.length(); i++) {

                    myAdsModel item = new myAdsModel();
                    JSONObject object = jsonArray.getJSONObject(i);

                    item.setAdId(object.getString("ad_id"));
                    item.setName(object.getString("ad_title"));
                    item.setAdStatus(object.getJSONObject("ad_status").getString("status"));
                    item.setAdStatusValue(object.getJSONObject("ad_status").getString("status_text"));
                    item.setPrice(object.getJSONObject("ad_price").getString("price"));
                    item.setImage((object.getJSONArray("ad_images").getJSONObject(0).getString("thumb")));

                    item.setDelAd(texts.getString("delete_text"));
                    item.setEditAd(texts.getString("edit_text"));

                    item.setAdType(texts.getString("ad_type"));

                    item.setSpinerData(texts.getJSONArray("status_dropdown_name"));
                    item.setSpinerValue(texts.getJSONArray("status_dropdown_value"));

                    list.add(item);
                }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    private void reload() {
        Fragment frg;
        FragmentManager manager = getActivity().getSupportFragmentManager();
        frg = manager.findFragmentByTag("MyAdsExpire");
        final FragmentTransaction ft = manager.beginTransaction();
        ft.detach(frg);
        ft.attach(frg);
        ft.commit();
    }

    @Override
    public void onResume() {
        try {
            if (settingsMain.getAnalyticsShow() && !settingsMain.getAnalyticsId().equals(""))
                AnalyticsTrackers.getInstance().trackScreenView("My Ads");
            super.onResume();
        } catch (IllegalStateException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onSuccessPermission(int code) {

        Intent in = new Intent(getActivity(), EditAdPost.class);
        in.putExtra("id", adID);
        getActivity().startActivity(in);
        getActivity().overridePendingTransition(R.anim.right_enter, R.anim.left_out);
    }
}
