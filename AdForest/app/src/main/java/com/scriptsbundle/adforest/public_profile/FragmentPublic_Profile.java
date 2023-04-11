package com.scriptsbundle.adforest.public_profile;


import android.content.Intent;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.drawable.LayerDrawable;
import android.os.Bundle;

import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.core.widget.NestedScrollView;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

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
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.modelsList.myAdsModel;
import com.scriptsbundle.adforest.profile.RatingFragment;
import com.scriptsbundle.adforest.public_profile.adapter.PublicProfileAdapter;
import com.scriptsbundle.adforest.utills.AnalyticsTrackers;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.NestedScroll;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

/**
 * A simple {@link Fragment} subclass.
 */
public class FragmentPublic_Profile extends Fragment {
    SettingsMain settingsMain;
    TextView verifyBtn, textViewRateNo, textViewUserName, textViewLastLogin;
    TextView textViewAdsSold, textViewTotalList, textViewInactiveAds, textViewEmptyData, textViewIntroduction, textIntroduction;
    RatingBar ratingBar;
    ImageView imageViewProfile;
    RecyclerView recyclerView;
    PublicProfileAdapter adapter;
    int nextPage = 1;
    boolean loading = true, hasNextPage = false;
    ProgressBar progressBar;
    NestedScrollView nestedScrollView;
    RestService restService;
    JSONObject jsonObjectforCustom;
    LinearLayout introduction;
    LinearLayout publicProfileCustomIcons;
    private ArrayList<myAdsModel> list = new ArrayList<>();
    private String userId;
    public static Boolean onLoading = false;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    RelativeLayout mainRelative;
    public FragmentPublic_Profile() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_public_profile, null);
        Bundle bundle = getArguments();
        userId = bundle.getString("user_id");
        String requestFrom = bundle.getString("requestFrom", "");
        settingsMain = new SettingsMain(getActivity());

        onLoading = true;
        if (requestFrom.isEmpty()) {
            LinearLayout linearLayout = getActivity().findViewById(R.id.ll11);
            linearLayout.setVisibility(View.GONE);
        }
        if (requestFrom.equals("MarvelDetailPage")) {
            LinearLayout linearLayout = getActivity().findViewById(R.id.ll11);
            linearLayout.setVisibility(View.GONE);
            Ad_detail_activity.favBtn.setVisibility(View.GONE);
            Ad_detail_activity.shareBtn.setVisibility(View.GONE);
            Ad_detail_activity.reportBtn.setVisibility(View.GONE);

        }
        introduction = view.findViewById(R.id.introduction);
        introduction.setVisibility(View.GONE);

        publicProfileCustomIcons = view.findViewById(R.id.publicProfileCustomIcons);

        publicProfileCustomIcons.setVisibility(View.GONE);
//        publicProfileCustomIcons.setBackgroundResource(R.drawable.socialicons);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        mainRelative = view.findViewById(R.id.mainRelative);
        progressBar = view.findViewById(R.id.progressBar4);
        nestedScrollView = view.findViewById(R.id.mainScrollView);
        progressBar.setVisibility(View.GONE);

        textViewLastLogin = view.findViewById(R.id.loginTime);
        verifyBtn = view.findViewById(R.id.verified);
        textViewRateNo = view.findViewById(R.id.numberOfRate);
        textViewUserName = view.findViewById(R.id.text_viewName);

        textIntroduction = view.findViewById(R.id.textIntroduction);
//        textViewIntroduction=view.findViewById(R.id.textViewIntroduction);
        introduction.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));

        imageViewProfile = view.findViewById(R.id.image_view);
        ratingBar = view.findViewById(R.id.ratingBar);

        LayerDrawable stars = (LayerDrawable) this.ratingBar.getProgressDrawable();
        stars.getDrawable(2).setColorFilter(Color.parseColor("#ffcc00"), PorterDuff.Mode.SRC_ATOP);

        textViewEmptyData = view.findViewById(R.id.textView5);
        textViewEmptyData.setVisibility(View.GONE);
        textViewAdsSold = view.findViewById(R.id.share);
        textViewTotalList = view.findViewById(R.id.addfav);
        textViewInactiveAds = view.findViewById(R.id.report);


        recyclerView = view.findViewById(R.id.cardView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setNestedScrollingEnabled(false);

        GridLayoutManager MyLayoutManager = new GridLayoutManager(getActivity(), 2);
        MyLayoutManager.setOrientation(RecyclerView.VERTICAL);
        recyclerView.setLayoutManager(MyLayoutManager);
        if (settingsMain.getAppOpen()) {
            Log.d("info data object", "sdfasdfadsasdfasdfasdf");
            restService = UrlController.createService(RestService.class);
        } else {
            Log.d("info data object", "elsssss");
            restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), getActivity());
        }
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
                    if (settingsMain.getAppOpen()) {
                        Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();
                    } else {
                        RatingFragment fragment = new RatingFragment();
                        Bundle bundle = new Bundle();
                        bundle.putString("id", userId);
                        bundle.putBoolean("isprofile", true);
                        fragment.setArguments(bundle);

                        replaceFragment(fragment, "RatingFragment");
                    }
                }
                return true;
            }
        });
        loadData();

        return view;
    }

    private void setAllViewsText(JSONObject jsonObject, JSONObject object) {
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
            if (!object.getJSONObject("introduction").getString("value").equals("")) {
                introduction.setVisibility(View.VISIBLE);
                Log.d("info Introduction", object.getJSONObject("introduction").toString());
//                textViewIntroduction.setText(object.getJSONObject("introduction").getString("key"));
                textIntroduction.setText(object.getJSONObject("introduction").getString("value"));
            }
            textViewAdsSold.setText(jsonObject.getString("ads_sold"));
            textViewTotalList.setText(jsonObject.getString("ads_total"));
            textViewInactiveAds.setText(jsonObject.getString("ads_inactive"));

            ratingBar.setNumStars(5);
            ratingBar.setRating(Float.parseFloat(jsonObject.getJSONObject("rate_bar").getString("number")));
            textViewRateNo.setText(jsonObject.getJSONObject("rate_bar").getString("text"));

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

    private void adforest_loadMore(int nextPag) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            JsonObject params = new JsonObject();

            params.addProperty("page_number", nextPag);
            params.addProperty("user_id", userId);

            Log.d("info sendPublic Load", params.toString());

            Call<ResponseBody> myCall = restService.postGetPublicProfile(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info PublicPrMore Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info PublicProfile obj", "" + response.getJSONObject("data"));
                                Log.d("info Public Profile", "" + response.getJSONObject("data").getJSONObject("profile_extra"));

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
                        Log.d("info FeaturedMore ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);
                        Log.d("info FeaturedMore err", String.valueOf(t));
                        Log.d("info FeaturedMore err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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

    private void loadData() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("user_id", userId);
            Log.d("info sendPublic Profile", params.toString());
            Call<ResponseBody> myCall = restService.postGetPublicProfile(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info PublicProfile Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info PublicProfile obj", "" + response.getJSONObject("data"));
                                Log.d("info Public Profile", "" + response.getJSONObject("data").getJSONObject("profile_extra"));
                                getActivity().setTitle(response.getJSONObject("data").getString("page_title"));
                                onLoading = false;
                                JSONObject jsonObjectPagination = response.getJSONObject("data").getJSONObject("pagination");

                                nextPage = jsonObjectPagination.getInt("next_page");
                                hasNextPage = jsonObjectPagination.getBoolean("has_next_page");

                                makeList(response.getJSONObject("data"), response.getJSONObject("data").getJSONObject("text"));
                                setAllViewsText(response.getJSONObject("data").getJSONObject("profile_extra"), response.getJSONObject("data"));

                                if (response.getJSONObject("data").getBoolean("is_show_social")) {
                                    Log.d("Info custom", "====Add all===");
                                    social_icons.adforest_setViewsForCustom(response.getJSONObject("data"), publicProfileCustomIcons, getContext());
                                }
                                if (list.size() > 0) {
                                    adapter = new PublicProfileAdapter(getActivity(), list);
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
                                        public void delViewOnClick(View v, int position) {
                                            Toast.makeText(getContext(), "ss" + "" + position, Toast.LENGTH_LONG).show();
                                        }

                                        @Override
                                        public void editViewOnClick(View v, int position) {
                                            Toast.makeText(getContext(), "s" + "" + position, Toast.LENGTH_LONG).show();
                                        }
                                    });
                                } else {
                                    textViewEmptyData.setVisibility(View.VISIBLE);
                                    textViewEmptyData.setText(response.get("message").toString());
                                }

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
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    mainRelative.setVisibility(View.VISIBLE);
                    Log.d("info FeaturedAds error", String.valueOf(t));
                    Log.d("info FeaturedAds error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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
                    item.setAdTypeText(object.getJSONObject("ad_status").getString("featured_type_text"));
                    item.setPrice(object.getJSONObject("ad_price").getString("price"));
                    item.setImage((object.getJSONArray("ad_images").getJSONObject(0).getString("thumb")));

                    item.setAdType(texts.getString("ad_type"));

                    list.add(item);
                }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    void makeList(JSONObject data, JSONObject texts) {
        list.clear();

        try {
            JSONArray jsonArray = data.getJSONArray("ads");

//            Log.d("jsonaarry is = ", jsonArray.toString());
            if (jsonArray.length() > 0)
                for (int i = 0; i < jsonArray.length(); i++) {

                    myAdsModel item = new myAdsModel();
                    JSONObject object = jsonArray.getJSONObject(i);

                    item.setAdId(object.getString("ad_id"));
                    item.setName(object.getString("ad_title"));
                    item.setAdStatus(object.getJSONObject("ad_status").getString("status"));
                    item.setAdStatusValue(object.getJSONObject("ad_status").getString("status_text"));
                    item.setAdTypeText(object.getJSONObject("ad_status").getString("featured_type_text"));
                    item.setPrice(object.getJSONObject("ad_price").getString("price"));
                    item.setImage((object.getJSONArray("ad_images").getJSONObject(0).getString("thumb")));

                    item.setAdType(texts.getString("ad_type"));


                    list.add(item);
                }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume() {
        try {
            if (settingsMain.getAnalyticsShow() && !settingsMain.getAnalyticsId().equals(""))
                AnalyticsTrackers.getInstance().trackScreenView("Public Profile");
            super.onResume();
        } catch (IllegalStateException e) {
            e.printStackTrace();
        }
    }
}

