package com.scriptsbundle.adforest.home;


import android.content.Context;
import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.core.view.ViewCompat;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.facebook.shimmer.ShimmerFrameLayout;

import com.google.gson.JsonObject;

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
import com.scriptsbundle.adforest.Search.FragmentCatSubNSearch;
import com.scriptsbundle.adforest.helper.OnItemClickListener;
import com.scriptsbundle.adforest.home.adapter.ItemMainAllLocationAds;
import com.scriptsbundle.adforest.modelsList.homeCatListModel;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

/**
 * A simple {@link Fragment} subclass.
 */
public class FragmentAllLocations extends Fragment {

    RecyclerView locationRecycler_view;
    ItemMainAllLocationAds itemMainAllLocationAds;
    ArrayList<homeCatListModel> locationAdscat = new ArrayList<>();
    SettingsMain settingsMain;
    RestService restService;
    int next_page = 1;
    boolean has_next_page = false;
    JSONObject responceData, pagination;
    Button btn_loadMore;
    String term_id = "";
    EditText searchView;
    private Context context;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    RelativeLayout mainRelative;

    public FragmentAllLocations() {
        // Required empty public constructor
    }


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.fragment_all_locations, container, false);
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        context = getContext();
        settingsMain = new SettingsMain(context);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        mainRelative = view.findViewById(R.id.mainRelative);
        loadingLayout.setVisibility(View.VISIBLE);
        shimmerFrameLayout.setVisibility(View.VISIBLE);
        shimmerFrameLayout.startShimmer();
        HomeActivity.loadingScreen = true;
        locationRecycler_view = view.findViewById(R.id.locationRecycler_view);
        btn_loadMore = view.findViewById(R.id.btn_loadMore);
        searchView = view.findViewById(R.id.mSearch);
        searchView.requestFocus();

        locationRecycler_view.setHasFixedSize(true);
        locationRecycler_view.setNestedScrollingEnabled(false);
        ViewCompat.setNestedScrollingEnabled(locationRecycler_view, false);
        GridLayoutManager MyLayoutManager = new GridLayoutManager(context, 2);
        MyLayoutManager.setOrientation(RecyclerView.VERTICAL);
        locationRecycler_view.setLayoutManager(MyLayoutManager);

        SwipeRefreshLayout swipeRefreshLayout = getActivity().findViewById(R.id.swipe_refresh_layout);
        swipeRefreshLayout.setEnabled(true);

        if (settingsMain.getAppOpen()) {
            restService = UrlController.createService(RestService.class);
        } else
            restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), getActivity());

        next_page = 1;
        term_id = "";
        adforest_loadData();


        btn_loadMore.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                JsonObject jsonObject = new JsonObject();
                jsonObject.addProperty("term_name", "ad_country");
                jsonObject.addProperty("term_id", term_id);
                try {
                    jsonObject.addProperty("page_number", pagination.getInt("next_page"));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                adforest_loadMore(jsonObject);

            }
        });
        searchView.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                itemMainAllLocationAds.getFilter().filter(searchView.getText().toString());

            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });
    }

    private void adforest_loadMore(JsonObject jsonObject) {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            Log.d("info catLoc sendLoad", jsonObject.toString());

            Call<ResponseBody> myCall = restService.getAllLocAndCat(jsonObject, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info LoadMore Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info LoadMore obj", "" + response.getJSONObject("data"));
                                responceData = response.getJSONObject("data");
                                pagination = responceData.getJSONObject("pagination");
                                has_next_page = pagination.getBoolean("has_next_page");
                                next_page = pagination.getInt("next_page");

                                adforest_setLoadMoreLocationAds(responceData.getJSONArray("terms"));

                                if (!has_next_page) {
                                    btn_loadMore.setVisibility(View.GONE);
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
                        Log.d("info LoadMore ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                        Log.d("info LoadMore err", String.valueOf(t));
                        Log.d("info LoadMore err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            mainRelative.setVisibility(View.VISIBLE);

            Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
        }
    }

    private void adforest_loadData() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {
            if (!HomeActivity.checkLoading)
                shimmerFrameLayout.startShimmer();
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            loadingLayout.setVisibility(View.VISIBLE);
            mainRelative.setVisibility(View.GONE);
            JsonObject jsonObject = new JsonObject();
            jsonObject.addProperty("term_name", "ad_country");
            jsonObject.addProperty("term_id", term_id);
            jsonObject.addProperty("page_number", next_page);
            Log.d("info catLoc send", jsonObject.toString());


            Call<ResponseBody> myCall = restService.getAllLocAndCat(jsonObject, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info catLoc Resp", "" + responseObj.toString());
                            HomeActivity.checkLoading = false;

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info catLoc obj", "" + response.getJSONObject("data"));
                                responceData = response.getJSONObject("data");
                                getActivity().setTitle(responceData.getString("page_title"));
                                pagination = responceData.getJSONObject("pagination");
                                has_next_page = pagination.getBoolean("has_next_page");
                                searchView.setHint(responceData.getString("search_here"));

                                adforest_setAllLocationAds(responceData.getJSONArray("terms"));
                                HomeActivity.loadingScreen = false;
                                if (has_next_page) {
                                    btn_loadMore.setVisibility(View.VISIBLE);
                                    btn_loadMore.setText(responceData.getString("load_more"));
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
                        Log.d("info catLoc ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        mainRelative.setVisibility(View.VISIBLE);

                        Log.d("info catLoc err", String.valueOf(t));
                        Log.d("info catLoc err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            mainRelative.setVisibility(View.VISIBLE);

            Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
        }
    }

    private void adforest_setLoadMoreLocationAds(JSONArray jsonArray) {
        for (int i = 0; i < jsonArray.length(); i++) {
            homeCatListModel item = new homeCatListModel();
            try {
                item.setId(jsonArray.getJSONObject(i).getString("term_id"));
                item.setTitle(jsonArray.optJSONObject(i).getString("name"));
                item.setThumbnail(jsonArray.optJSONObject(i).getString("term_img"));
                item.setAdsCount(jsonArray.getJSONObject(i).getString("count"));
                item.setHas_children(jsonArray.getJSONObject(i).getBoolean("has_children"));
            } catch (JSONException e) {
                e.printStackTrace();
            }

            locationAdscat.add(item);
        }
        itemMainAllLocationAds.notifyDataSetChanged();
    }

    private void adforest_setAllLocationAds(JSONArray jsonArray) {

        locationAdscat.clear();
        for (int i = 0; i < jsonArray.length(); i++) {
            homeCatListModel item = new homeCatListModel();
            try {
                item.setId(jsonArray.getJSONObject(i).getString("term_id"));
                item.setTitle(jsonArray.optJSONObject(i).getString("name"));
                item.setThumbnail(jsonArray.optJSONObject(i).getString("term_img"));
                item.setAdsCount(jsonArray.getJSONObject(i).getString("count"));
                item.setHas_children(jsonArray.getJSONObject(i).getBoolean("has_children"));
            } catch (JSONException e) {
                e.printStackTrace();
            }

            locationAdscat.add(item);
        }

        itemMainAllLocationAds = new ItemMainAllLocationAds(context, locationAdscat, 2);
        locationRecycler_view.setAdapter(itemMainAllLocationAds);
        itemMainAllLocationAds.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(homeCatListModel item) {
                if (item.isHas_children()) {
                    next_page = 1;
                    term_id = item.getId();
                    searchView.setText("");
                    adforest_loadData();
                } else {
                    searchView.setText("");
                    FragmentCatSubNSearch fragment_search = new FragmentCatSubNSearch();
                    Bundle bundle = new Bundle();
                    bundle.putString("ad_country", item.getId());

                    fragment_search.setArguments(bundle);
                    replaceFragment(fragment_search, "FragmentCatSubNSearch");
                }
            }
        });

    }

    void replaceFragment(Fragment someFragment, String tag) {
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, someFragment, tag);
        transaction.addToBackStack(tag);
        transaction.commit();
    }

}
