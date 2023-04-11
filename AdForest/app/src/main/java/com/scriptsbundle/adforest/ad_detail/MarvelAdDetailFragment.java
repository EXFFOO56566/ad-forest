package com.scriptsbundle.adforest.ad_detail;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffColorFilter;
import android.graphics.drawable.ColorDrawable;
import android.graphics.drawable.Drawable;
import android.graphics.drawable.LayerDrawable;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;

import android.text.InputType;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RatingBar;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.cardview.widget.CardView;
import androidx.core.view.ViewCompat;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.facebook.shimmer.ShimmerFrameLayout;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.youtube.player.YouTubeInitializationResult;
import com.google.android.youtube.player.YouTubePlayer;
import com.google.android.youtube.player.YouTubePlayerSupportFragment;
import com.google.gson.JsonObject;
import com.iarcuschin.simpleratingbar.SimpleRatingBar;
import com.smarteist.autoimageslider.IndicatorView.animation.type.IndicatorAnimationType;
import com.smarteist.autoimageslider.SliderAnimations;
import com.smarteist.autoimageslider.SliderView;
import com.squareup.picasso.Picasso;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.sufficientlysecure.htmltextview.HtmlAssetsImageGetter;
import org.sufficientlysecure.htmltextview.HtmlResImageGetter;
import org.sufficientlysecure.htmltextview.HtmlTextView;

import java.io.IOException;
import java.io.Serializable;
import java.net.SocketTimeoutException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.TimeZone;
import java.util.concurrent.TimeoutException;

import cn.iwgang.countdownview.CountdownView;
import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.Biding.Biding_ViewPagerFragment;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.ad_detail.Models.WhizzChatModel;
import com.scriptsbundle.adforest.adapters.SliderAdapterExample;
import com.scriptsbundle.adforest.adapters.SliderItem;
import com.scriptsbundle.adforest.ad_detail.full_screen_image.FullScreenViewActivity;
import com.scriptsbundle.adforest.adapters.ItemMainHomeRelatedAdapter;
import com.scriptsbundle.adforest.adapters.ItemRatingListAdapter;
import com.scriptsbundle.adforest.helper.BlogCommentOnclicklinstener;
import com.scriptsbundle.adforest.helper.OnItemClickListener2;
import com.scriptsbundle.adforest.helper.imageAdapterOnclicklistner;
import com.scriptsbundle.adforest.home.EditAdPost;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.messages.Message;
import com.scriptsbundle.adforest.messages.WhizzChat.View.WhizzChatList;
import com.scriptsbundle.adforest.modelsList.blogCommentsModel;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.modelsList.myAdsModel;
import com.scriptsbundle.adforest.packages.PackagesFragment;
import com.scriptsbundle.adforest.profile.RatingFragment;
import com.scriptsbundle.adforest.public_profile.FragmentPublic_Profile;
import com.scriptsbundle.adforest.utills.AnalyticsTrackers;
import com.scriptsbundle.adforest.utills.CircleTransform;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.RuntimePermissionHelper;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;


public class MarvelAdDetailFragment extends Fragment implements Serializable, RuntimePermissionHelper.permissionInterface {
    public static String myId;


    public static JSONObject jsonObjectBidTabs;
    public static String buttonPress;
    Dialog dialog;
    RelativeLayout relativeLayoutFeature, reactionLayout, curvedLayoutTimer;
    SettingsMain settingsMain;
    ItemRatingListAdapter itemSendRecMesageAdapter;
    myAdsModel item;
    TextView textViewAdName, textViewLocation, textViewSeen, textViewDate, textViewPrice, textViewLastLogin;
    TextView shareBtn, addToFavBtn, reportBtn, verifyBtn, textViewRateNo, textViewUserName, textViewRelated, textViewDescript;
    TextView messageBtn, callBtn, bidBtn, textViewNotify, textViewFeatured, makeFeatureBtn, featuredText, bidStatisticsBtn;
    HtmlTextView htmlTextView;
    ImageView getDirectionBtn;
    LinearLayout linearLayout2, linearLayout1, linearLayoutOuter, linearLayoutLoadMoreRatings;
    RatingBar ratingBar;
    ImageView imageViewProfile, imageViewSeller;
    Button contactSellerMessageIcon;
    SliderView bannerSlider;
    SliderAdapterExample sliderAdapterExample;

    ArrayList<String> imageUrls;
    JSONObject jsonObjectSendMessage;
    JSONObject jsonObjectCallNow;
    JSONObject jsonObjectBidNow;
    JSONObject jsonObjectReport;
    JSONObject jsonObjectShareInfo;
    JSONObject jsonObjectRatingInfo;
    JSONObject jsonObjectPagination;
    JSONObject blockUserObject;
    JSONObject JsonObjectData;
    JSONObject jsonObjectSellerContact;
    String btnTextCLick;
    RecyclerView mRecyclerView, ratingRecylerView;
    View temphide;
    LinearLayout linearLayout, linearLayoutSubmitRating, ratingLoadLayout, linearLayoutSeller;
    int noOfCol = 2;
    CardView cardViewBidSec, cardViewRating, cardViewSeller;
    TextView textViewTotBid, textViewHighBid, textViewLowBid, textViewTotBidtext, textViewHighBidtext, textViewLowBidtext, textViewRatingTitle,
            textViewRatingNotEdit, textViewRatingButton, textViewNoCurrentRating, textViewRatingTitleTop, ratingLoadMoreButton,
            tv_adType, tv_blockUser, editAdd, textViewNameSeller, textViewSellerSubHeading, txtViewDays, txtDay, txtViewHours, txtHours, txtViewMin, txtMin, txtViwsSec, txtSec;
    RestService restService;
    NestedScrollView nestedScroll;
    EditText editTextRattingComment;
    SimpleRatingBar simpleRatingBar;
    boolean LoadMoreDialogOpen = false;
    Dialog loadMoreRating;
    CountdownView countDown;
    //    FrameLayout youtube_view;
    FrameLayout youtube_view;
    RuntimePermissionHelper runtimePermissionHelper;
    Dialog callDialog;
    private ArrayList<catSubCatlistModel> list = new ArrayList<>();
    private ArrayList<blogCommentsModel> listitems = new ArrayList<>();
    private String phoneNumber ="";
    private String adAuthorId;
    private JSONObject jsonObjectStaticText;
    private double latitude = 0.0;
    private double longitude = 0.0;
    Boolean sellerShowHide;
    String sellerCardTitle, sellerCardsubTitle;
    RelativeLayout reactions;
    static Boolean onLoad = false;
    public static Boolean reaction = false;
    Boolean CheckemailRequired = false;
    Boolean ChecknameRequired = false;
    Boolean CheckphoneRequired = false;
    Boolean ChecktextArea_informationRequired = false;
    String nameKey, emailKey, phonenumberKey, messageKey;
    public static Handler handler = new Handler();
    public static Runnable runnable;
    public static long timer;
    static String timerDays = "";
    static String timerHours = "";
    static String timerMin = "";
    static String timerSecs = "";
    CountDownTimer marvelCountTimer;
    String priceType = "";
    RelativeLayout timerValues;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    SwipeRefreshLayout swipeRefreshLayout;
    //Whizz Chat Utils
    WhizzChatModel whizzChatModel;
    boolean isWhizzChatEnabled;
    //Whatsapp Icon
    FloatingActionButton fab;
    boolean phoneNumberVerified = false;
    public MarvelAdDetailFragment() {
        // Required empty public constructor
    }


    @SuppressLint("ClickableViewAccessibility")
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_marvel_ad_detail, container, false);
        settingsMain = new SettingsMain(getActivity());
        shimmerFrameLayout = view.findViewById(R.id.shimmer_view_container);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        loadingLayout.setVisibility(View.VISIBLE);
        shimmerFrameLayout.setVisibility(View.VISIBLE);
        shimmerFrameLayout.startShimmer();
        fab = getActivity().findViewById(R.id.fab);
        if(settingsMain.getWhatsApp()){
            fab.setVisibility(View.VISIBLE);
        }else{
            fab.setVisibility(View.GONE);
        }
        fab.startAnimation(AnimationUtils.loadAnimation(getContext(),R.anim.wiggle));
        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (!UrlController.loading){
                    if (phoneNumber!=""&&phoneNumberVerified){
                        Uri uri = Uri.parse("https://api.whatsapp.com/send?phone=" + phoneNumber + "&text=" + "");
                        Intent sendIntent = new Intent(Intent.ACTION_VIEW, uri);
                        startActivity(sendIntent);
                    }else{
                        Toast.makeText(getActivity(), "Phone number not verified", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });
        final Bundle bundle = this.getArguments();
        if (bundle != null) {
            myId = bundle.getString("id", "0");
//            comntId = bundle.getString("r_id", "");
//            reactionId = bundle.getString("c_id", "");
        }
//        reactionLayout=getActivity().findViewById(R.id.reactions);
        onLoad = true;
        Ad_detail_activity.favBtn.setVisibility(View.VISIBLE);
        Ad_detail_activity.shareBtn.setVisibility(View.VISIBLE);
        Ad_detail_activity.reportBtn.setVisibility(View.VISIBLE);
        linearLayout = getActivity().findViewById(R.id.ll11);
        linearLayout.setVisibility(View.VISIBLE);
        runtimePermissionHelper = new RuntimePermissionHelper(getActivity(), this);
        curvedLayoutTimer = view.findViewById(R.id.curvedLayoutTimer);
        linearLayout.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        temphide = getActivity().findViewById(R.id.temphide);
        nestedScroll = view.findViewById(R.id.scrollViewUp);
        getDirectionBtn = view.findViewById(R.id.textView20);
        relativeLayoutFeature = view.findViewById(R.id.relMakeFeature);
        textViewFeatured = view.findViewById(R.id.relMakeFeatureTV);
        makeFeatureBtn = view.findViewById(R.id.btnMakeFeat);
        featuredText = view.findViewById(R.id.featuredText);
        txtViewDays = view.findViewById(R.id.countDays);
        txtViewHours = view.findViewById(R.id.countHours);
        txtViewMin = view.findViewById(R.id.countMin);
        txtViwsSec = view.findViewById(R.id.countSec);
        txtDay = view.findViewById(R.id.txtDays);
        txtHours = view.findViewById(R.id.txtHours);
        txtMin = view.findViewById(R.id.txtMin);
        txtSec = view.findViewById(R.id.txtSec);

        textViewNotify = view.findViewById(R.id.textView19);

        bannerSlider = view.findViewById(R.id.banner_slider1);
        sliderAdapterExample = new SliderAdapterExample(getActivity());
        bannerSlider.setSliderAdapter(sliderAdapterExample);
        bannerSlider.setIndicatorAnimation(IndicatorAnimationType.DROP); //set indicator animation by using SliderLayout.IndicatorAnimations. :WORM or THIN_WORM or COLOR or DROP or FILL or NONE or SCALE or SCALE_DOWN or SLIDE and SWAP!!
        bannerSlider.setSliderTransformAnimation(SliderAnimations.SIMPLETRANSFORMATION);
        bannerSlider.setAutoCycleDirection(SliderView.AUTO_CYCLE_DIRECTION_BACK_AND_FORTH);
        bannerSlider.setIndicatorSelectedColor(Color.WHITE);
        bannerSlider.setIndicatorUnselectedColor(Color.GRAY);
        bannerSlider.setScrollTimeInSec(3);
        bannerSlider.setAutoCycle(true);
        bannerSlider.startAutoCycle();
        imageUrls = new ArrayList<>();
        messageBtn = getActivity().findViewById(R.id.message);
        callBtn = getActivity().findViewById(R.id.call);

        bidBtn = view.findViewById(R.id.bidBtn);
        youtube_view = view.findViewById(R.id.youtube_view);
        bidStatisticsBtn = view.findViewById(R.id.bidStatisticsBtn);
        editAdd = view.findViewById(R.id.editAdd);
        cardViewSeller = view.findViewById(R.id.card_view_seller);
        cardViewSeller.setVisibility(View.GONE);
        linearLayoutSeller = view.findViewById(R.id.linear_layout_card_view_seller);
        imageViewSeller = view.findViewById(R.id.image_view_Seller);
        textViewNameSeller = view.findViewById(R.id.text_viewName_Seller);
        textViewSellerSubHeading = view.findViewById(R.id.subHeading_Seller);
        contactSellerMessageIcon = view.findViewById(R.id.contact_seller_MessageIcon);
        contactSellerMessageIcon.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        timerValues = view.findViewById(R.id.timerValues);
        if (messageBtn != null)
            messageBtn.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        if (callBtn != null)
            callBtn.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        makeFeatureBtn.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        bidBtn.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        bidStatisticsBtn.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        editAdd.setBackground(CustomBorderDrawable.customButton(6, 6, 6, 6, SettingsMain.getMainColor(), SettingsMain.getMainColor(), SettingsMain.getMainColor(), 3));

        cardViewBidSec = view.findViewById(R.id.card_view4);

        textViewTotBid = view.findViewById(R.id.textView8);
        textViewTotBidtext = view.findViewById(R.id.textView9);
        textViewHighBid = view.findViewById(R.id.textView10);
        textViewHighBidtext = view.findViewById(R.id.textView11);
        textViewLowBid = view.findViewById(R.id.textView12);
        textViewLowBidtext = view.findViewById(R.id.textView13);

        textViewAdName = view.findViewById(R.id.text_view_name);
        textViewLocation = view.findViewById(R.id.location);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            textViewLocation.setCompoundDrawableTintList(ColorStateList.valueOf(Color.parseColor(SettingsMain.getMainColor())));
        }

        textViewSeen = view.findViewById(R.id.views);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            textViewSeen.setCompoundDrawableTintList(ColorStateList.valueOf(Color.parseColor(SettingsMain.getMainColor())));
        }

        textViewDate = view.findViewById(R.id.date);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            textViewDate.setCompoundDrawableTintList(ColorStateList.valueOf(Color.parseColor(SettingsMain.getMainColor())));
        }

        textViewPrice = view.findViewById(R.id.prices);
        textViewLastLogin = view.findViewById(R.id.loginTime);
        shareBtn = view.findViewById(R.id.share);
        addToFavBtn = view.findViewById(R.id.addfav);
        reportBtn = view.findViewById(R.id.report);
        verifyBtn = view.findViewById(R.id.verified);
        textViewRateNo = view.findViewById(R.id.numberOfRate);
        textViewUserName = view.findViewById(R.id.text_viewName);
        textViewRelated = view.findViewById(R.id.relatedText);
        htmlTextView = view.findViewById(R.id.html_text);
        ratingBar = view.findViewById(R.id.ratingBar);
        imageViewProfile = view.findViewById(R.id.image_view);
        tv_adType = view.findViewById(R.id.tv_adType);
        tv_adType.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        tv_blockUser = view.findViewById(R.id.tv_block_user);
        tv_blockUser.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        textViewPrice.setTextColor(Color.parseColor(SettingsMain.getMainColor()));

        LayerDrawable stars = (LayerDrawable) this.ratingBar.getProgressDrawable();
        stars.getDrawable(2).setColorFilter(Color.parseColor("#ffcc00"), PorterDuff.Mode.SRC_ATOP);

        linearLayoutOuter = view.findViewById(R.id.ll1inner_location);
        linearLayout1 = view.findViewById(R.id.linearLayout1);
        linearLayout2 = view.findViewById(R.id.customLayout1);
        textViewDescript = view.findViewById(R.id.text_view_title);

        //Ratting Initialization
        cardViewRating = view.findViewById(R.id.card_viewRating);
        textViewRatingTitle = view.findViewById(R.id.ratingTitle);
        textViewRatingNotEdit = view.findViewById(R.id.ratingNotEdit);
        editTextRattingComment = view.findViewById(R.id.ratingEditText);
        textViewNoCurrentRating = view.findViewById(R.id.noCurrentRatingText);
        linearLayoutSubmitRating = view.findViewById(R.id.linearLayoutSubmitRating);
        textViewRatingTitleTop = view.findViewById(R.id.sectionTitleRating);
        ratingRecylerView = view.findViewById(R.id.ratingRecylerView);
        simpleRatingBar = view.findViewById(R.id.ratingbarAds);
        ratingLoadLayout = view.findViewById(R.id.ratingLoadLayout);
        final LinearLayoutManager MyLayoutManager = new LinearLayoutManager(getActivity());
        MyLayoutManager.setOrientation(RecyclerView.VERTICAL);
        ratingRecylerView.setLayoutManager(MyLayoutManager);
        ViewCompat.setNestedScrollingEnabled(ratingRecylerView, false);

        textViewRatingButton = view.findViewById(R.id.rating_button);
        textViewRatingButton.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        ratingLoadMoreButton = view.findViewById(R.id.ratingLoadMoreButton);
        ratingLoadMoreButton.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        countDown = view.findViewById(R.id.countDown);
        countDown.setVisibility(View.GONE);

        editAdd.setOnClickListener(view1 -> {

            Intent intent = new Intent(getContext(), EditAdPost.class);
            intent.putExtra("id", myId);
            startActivity(intent);

        });

        textViewRatingButton.setOnClickListener(v -> {
            if (!editTextRattingComment.getText().toString().isEmpty()) {
                JsonObject params = new JsonObject();
                params.addProperty("ad_id", myId);
                params.addProperty("rating", simpleRatingBar.getRating());
                params.addProperty("rating_comments", editTextRattingComment.getText().toString());
                adforest_postRating(params);
            }
            if (editTextRattingComment.getText().toString().isEmpty()) {
                editTextRattingComment.setError("");
            }
        });

        ratingLoadLayout.setOnClickListener(v -> {
            AdRating fragment = new AdRating();
            Bundle bundle1 = new Bundle();
            bundle1.putString("jsonObjectRatingInfo", jsonObjectRatingInfo.toString());
            fragment.setArguments(bundle1);
            replaceFragment(fragment, "AdRating");

        });


        if (settingsMain.getAppOpen()) {
            restService = UrlController.createService(RestService.class);
        } else
            restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), getActivity());

        if (messageBtn != null)
            messageBtn.setOnClickListener(view12 -> {
                if (!UrlController.loading){
                    try {
                        if (settingsMain.getAppOpen()) {
                            Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();

                        } else {
                            if (jsonObjectStaticText.getString("send_msg_btn_type").equals("receive")) {
//                            MessagesFragment messagesFragment=new MessagesFragment();
//                            replaceFragment(messagesFragment,"MessagesFragment");
                                Intent intent = new Intent(getActivity(), Message.class);
                                intent.putExtra("receive", true);
                                startActivity(intent);
                                getActivity().overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                            } else
                                adforest_showDilogMessage();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            });
        if (callBtn != null)

            callBtn.setOnClickListener(view13 -> {
                if (!UrlController.loading){

                    try {
                        if (JsonObjectData.getBoolean("show_phone_to_login") && settingsMain.getAppOpen()) {
                            Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();
                        } else {
                            adforest_showDilogCall();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            });
        contactSellerMessageIcon.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                adforest_showContactSellerDialog();

            }
        });
        bidBtn.setOnClickListener(view14 -> {

            Biding_ViewPagerFragment fragment = new Biding_ViewPagerFragment();
            buttonPress = "bidButton";
            replaceFragment(fragment, "Biding_ViewPagerFragment");
        });
        bidStatisticsBtn.setOnClickListener(v -> {
            Biding_ViewPagerFragment fragment = new Biding_ViewPagerFragment();
            buttonPress = "bidStatisticsBtn";
            replaceFragment(fragment, "Biding_ViewPagerFragment");
        });
        timerValues.setOnClickListener(v -> {
            Biding_ViewPagerFragment fragment = new Biding_ViewPagerFragment();
            buttonPress = "bidStatisticsBtn";
            replaceFragment(fragment, "Biding_ViewPagerFragment");
        });
        imageViewProfile.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                MarvelAdDetailFragment.this.goToPublicProfile();
            }
        });
        Ad_detail_activity.reportBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try {
                    if (jsonObjectStaticText.getString("send_msg_btn_type").equals("receive")) {
                        Toast.makeText(getActivity(), JsonObjectData.getString("cant_report_txt"), Toast.LENGTH_SHORT).show();
                    } else {
                        if (settingsMain.getAppOpen()) {
                            Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();

                        } else {
                            adforest_showDilogReport();
                        }
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        });

        textViewUserName.setOnTouchListener((v, event) -> {
            if (event.getAction() == MotionEvent.ACTION_UP) {
                goToPublicProfile();
            }
            return true;
        });

        ratingBar.setOnTouchListener((v, event) -> {
            if (event.getAction() == MotionEvent.ACTION_UP) {
                if (settingsMain.getAppOpen()) {
                    Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();
                } else {
                    RatingFragment fragment = new RatingFragment();
                    Bundle bundle12 = new Bundle();
                    bundle12.putString("id", adAuthorId);
                    bundle12.putBoolean("isprofile", false);
                    fragment.setArguments(bundle12);

                    replaceFragment(fragment, "RatingFragment");
                }
            }
            return true;
        });
        Ad_detail_activity.shareBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try {
                    Intent i = new Intent(Intent.ACTION_SEND);
                    i.setType("text/plain");
                    i.putExtra(Intent.EXTRA_SUBJECT, jsonObjectShareInfo.getString("title"));
                    i.putExtra(Intent.EXTRA_TEXT, jsonObjectShareInfo.getString("link"));
                    startActivity(Intent.createChooser(i, jsonObjectShareInfo.getString("text")));
                } catch (Exception e) {
                    //e.toString();
                }
            }
        });


        getDirectionBtn.setOnClickListener(view17 -> {

            String strUri = "http://maps.google.com/maps?q=loc:" + latitude + "," + longitude + " (" + textViewLocation.getText().toString() + ")";
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(strUri));
            getActivity().startActivity(intent);

            Log.d("info data object", longitude + "  ===   " + latitude);


        });

        makeFeatureBtn.setOnClickListener(view18 -> {
            if (settingsMain.getAppOpen()) {
                Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();

            } else {
                AlertDialog.Builder alert = new AlertDialog.Builder(getActivity());
                alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                alert.setCancelable(false);
                alert.setMessage(settingsMain.getAlertDialogMessage("confirmMessage"));
                alert.setPositiveButton(settingsMain.getAlertOkText(), new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog,
                                        int which) {
                        adforest_makeFeature();

                        dialog.dismiss();
                    }
                });
                alert.setNegativeButton(settingsMain.getAlertCancelText(), new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialogInterface, int i) {
                        dialogInterface.dismiss();
                    }
                });
                alert.show();
            }
        });
        Ad_detail_activity.favBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (settingsMain.getAppOpen()) {
                    Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();
                } else {
                    adforest_addToFavourite();
                }
            }
        });

        tv_blockUser.setOnTouchListener((v, event) -> {
            if (event.getAction() == MotionEvent.ACTION_UP) {
                adforest_blockUserDialog();
            }
            return true;
        });
        mRecyclerView = view.findViewById(R.id.recycler_view);
        mRecyclerView.setHasFixedSize(true);
        GridLayoutManager MyLayoutManager2 = new GridLayoutManager(getActivity(), 1);
        MyLayoutManager2.setOrientation(LinearLayoutManager.HORIZONTAL);
        mRecyclerView.setLayoutManager(MyLayoutManager2);
        swipeRefreshLayout = view.findViewById(R.id.swipe_refresh_layout);
        swipeRefreshLayout.setOnRefreshListener(() ->
                adforest_getAllData(myId, true)
        );

        adforest_getAllData(myId, true);
//        youTubePlayerView.getYouTubePlayerWhenReady(com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer::pause);


        return view;
    }

    private void adforest_getAllData(final String myId, Boolean isRejected) {
        UrlController.loading = true;
        this.myId = myId;
        nestedScroll.scrollTo(0, 0);
        youtube_view.setVisibility(View.GONE);
        if (SettingsMain.isConnectingToInternet(getActivity())) {
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();

            JsonObject params = new JsonObject();
            params.addProperty("ad_id", myId);
            params.addProperty("is_rejected", isRejected);

            Log.d("info send AdDetails", "" + params.toString());

            Call<ResponseBody> myCall = restService.getAdsDetail(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    UrlController.loading = false;
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info AdDetails Respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                swipeRefreshLayout.setRefreshing(false);
                                nestedScroll.setVisibility(View.VISIBLE);
                                Log.d("info AdDetails object", "" + response.getJSONObject("data"));
                                Log.d("info ProfileDetails obj", "" + response.getJSONObject("data").getJSONObject("profile_detail"));
                                Log.d("info Bids Data", "" + response.getJSONObject("data").getJSONObject("static_text"));

                                JsonObjectData = response.getJSONObject("data");
                                if (response.getJSONObject("data").getString("notification").equals("")) {
                                    textViewNotify.setVisibility(View.GONE);
                                } else {
                                    textViewNotify.setVisibility(View.VISIBLE);
                                    textViewNotify.setText(response.getJSONObject("data").getString("notification"));
                                }

                                if (response.getJSONObject("data").getJSONObject("is_featured").getBoolean("is_show")) {
                                    relativeLayoutFeature.setVisibility(View.VISIBLE);
                                    makeFeatureBtn.setText(response.getJSONObject("data").getJSONObject("is_featured")
                                            .getJSONObject("notification").getString("btn"));
                                    textViewFeatured.setText(response.getJSONObject("data").getJSONObject("is_featured")
                                            .getJSONObject("notification").getString("text"));
                                    makeFeatureBtn.setTag(response.getJSONObject("data").getJSONObject("is_featured")
                                            .getJSONObject("notification").getInt("link"));
                                } else {
                                    relativeLayoutFeature.setVisibility(View.GONE);
                                }

                                noOfCol = response.getJSONObject("data").getJSONObject("ad_detail").getInt("fieldsData_column");
                                onLoad = false;
                                adforest_setAllViewsText(response.getJSONObject("data").getJSONObject("ad_detail"),
                                        response.getJSONObject("data").getJSONObject("profile_detail"),
                                        response.getJSONObject("data").getJSONObject("static_text"));

                                jsonObjectStaticText = response.getJSONObject("data").getJSONObject("static_text");

                                getActivity().setTitle(response.getJSONObject("data").getString("page_title"));

                                jsonObjectBidNow = response.getJSONObject("data").getJSONObject("bid_popup");
                                jsonObjectCallNow = response.getJSONObject("data").getJSONObject("call_now_popup");
                                if (jsonObjectCallNow.getBoolean("phone_verification")) {
                                    if (jsonObjectCallNow.getBoolean("is_phone_verified")) {
                                        phoneNumberVerified = true;
                                    }
                                }else {
                                    phoneNumberVerified = true;
                                }
                                jsonObjectReport = response.getJSONObject("data").getJSONObject("report_popup");
                                jsonObjectSendMessage = response.getJSONObject("data").getJSONObject("message_popup");
                                jsonObjectShareInfo = response.getJSONObject("data").getJSONObject("share_info");

                                //Rating View setAllTexts and Ratting
                                jsonObjectRatingInfo = response.getJSONObject("data").getJSONObject("ad_ratting");
                                if (jsonObjectRatingInfo.getBoolean("rating_show")) {
                                    cardViewRating.setVisibility(View.VISIBLE);
                                    adforest_setAllRattigns();/**/
                                }
                                ItemMainHomeRelatedAdapter adapter = new ItemMainHomeRelatedAdapter(getActivity(), list);
                                adapter.getCalledFromAdDetail(true);
                                mRecyclerView.setAdapter(adapter);
                                adapter.setOnItemClickListener(new OnItemClickListener2() {
                                    @Override
                                    public void onItemClick(catSubCatlistModel item) {
                                        Log.d("item_id", item.getId());
                                        MarvelAdDetailFragment.myId = item.getId();
                                        adforest_recreateAdDetail();
                                        nestedScroll.scrollTo(0, 0);
                                    }
                                });

                                JSONObject adType = response.getJSONObject("data").
                                        getJSONObject("ad_detail").getJSONObject("ad_type_bar");
                                if (adType.getBoolean("is_show")) {
                                    tv_adType.setVisibility(View.VISIBLE);
                                    tv_adType.setText(adType.getString("text"));
                                }

                                if (jsonObjectStaticText.getString("send_msg_btn_type").equals("receive") && !settingsMain.getAppOpen()) {
                                    editAdd.setVisibility(View.VISIBLE);
                                    editAdd.setText(JsonObjectData.getString("edit_txt"));
                                }
                                jsonObjectSellerContact = response.getJSONObject("data").getJSONObject("seller_contact");

                                sellerShowHide = jsonObjectSellerContact.getBoolean("is_show");
                                textViewNameSeller.setText(jsonObjectSellerContact.getString("title"));
                                textViewSellerSubHeading.setText(jsonObjectSellerContact.getString("subtitle"));

                                if (sellerShowHide) {
                                    cardViewSeller.setVisibility(View.VISIBLE);
                                }

                                if (response.getJSONObject("data").has("isWhizActive")){
                                    isWhizzChatEnabled = response.getJSONObject("data").getBoolean("isWhizActive");
                                    if (isWhizzChatEnabled){
                                        JSONObject whizzChatData = response.getJSONObject("data").getJSONObject("whizchat_data");
                                        whizzChatModel = new WhizzChatModel();
                                        whizzChatModel.setChatId(whizzChatData.getString("id"));
                                        whizzChatModel.setPostId(whizzChatData.getString("post_id"));
                                    }
                                }

                                btnTextCLick = response.getJSONObject("data").getString("click_now_btn");
                                contactSellerMessageIcon.setText((CharSequence) btnTextCLick);
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                getActivity().finish();
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
                    UrlController.loading = false;
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);

                    Log.d("info AdDetails error", String.valueOf(t));
                    Log.d("info AdDetails error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });

        } else {
            UrlController.loading = false;
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
            getActivity().finish();
        }
    }

    private void adforest_setAllRattigns() {
        try {
            if (jsonObjectRatingInfo.getBoolean("can_rate") && !jsonObjectStaticText.getString("send_msg_btn_type").equals("receive")
                    && !settingsMain.getAppOpen()) {
                linearLayoutSubmitRating.setVisibility(View.VISIBLE);
                textViewRatingTitle.setText(jsonObjectRatingInfo.getString("title"));
                editTextRattingComment.setHint(jsonObjectRatingInfo.getString("textarea_text"));
                textViewRatingButton.setText(jsonObjectRatingInfo.getString("btn"));
                if (!jsonObjectRatingInfo.getBoolean("is_editable")) {
                    textViewRatingNotEdit.setVisibility(View.VISIBLE);
                    textViewRatingNotEdit.setText(jsonObjectRatingInfo.getString("tagline"));
                }
            } else {
                if (jsonObjectStaticText.getString("send_msg_btn_type").equals("receive") && !settingsMain.getUserLogin().equals("0")) {
                    textViewNoCurrentRating.setVisibility(View.VISIBLE);
                    textViewNoCurrentRating.setText(jsonObjectRatingInfo.getString("can_rate_msg"));
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        adforest_initializeListRating();
//        adforest_commentrating();
    }

    private void adforest_initializeListRating() {
        try {
            listitems.clear();
            try {
                jsonObjectPagination = jsonObjectRatingInfo.getJSONObject("pagination");
                JSONArray jsonArray = jsonObjectRatingInfo.getJSONArray("ratings");
                reaction = jsonObjectRatingInfo.getBoolean("ad_rating_emojies");
                if (jsonArray.length() > 0) {
//                    textViewNoCurrentRating.setVisibility(View.VISIBLE);
//                    textViewNoCurrentRating.setText(jsonObjectRatingInfo.getString("title"));
//                    textViewNoCurrentRating.setVisibility(View.GONE);
                    Log.d("info rating details", jsonArray.toString());
                    for (int i = 0; i < jsonArray.length(); i++) {

                        JSONObject jsonObject1 = jsonArray.getJSONObject(i);

                        blogCommentsModel item = new blogCommentsModel();

                        item.setComntId(jsonObject1.getString("rating_id"));
                        item.setName(jsonObject1.getString("rating_author_name"));
                        item.setMessage(jsonObject1.getString("rating_text"));
//                        item.setSetReaaction(jsonObject1.getBoolean("ad_rating_emojies"));
                        item.setLikeReactionId(jsonObject1.getJSONObject("ad_reactions").getString("like"));
                        item.setHeartReactionId(jsonObject1.getJSONObject("ad_reactions").getString("love"));
                        item.setWowReactionId(jsonObject1.getJSONObject("ad_reactions").getString("wow"));
                        item.setAngryReactionId(jsonObject1.getJSONObject("ad_reactions").getString("angry"));
                        item.setRating(jsonObject1.getString("rating_stars"));
                        item.setDate(jsonObject1.getString("rating_date"));
                        item.setImage(jsonObject1.getString("rating_author_image"));
                        item.setReply(jsonObject1.getString("reply_text"));
                        item.setCanReply(jsonObject1.getBoolean("can_reply"));
                        item.setHasReplyList(jsonObject1.getBoolean("has_reply"));

                        if (jsonObject1.getBoolean("has_reply")) {

                            ArrayList<blogCommentsModel> listitemsiner = new ArrayList<>();

                            JSONArray jsonArray1 = jsonObject1.getJSONArray("reply");
                            for (int ii = 0; ii < jsonArray1.length(); ii++) {
                                JSONObject jsonObject11 = jsonArray1.getJSONObject(ii);

                                blogCommentsModel item11 = new blogCommentsModel();

                                item11.setName(jsonObject11.getString("rating_author_name"));
                                item11.setMessage(jsonObject11.getString("rating_text"));
                                item11.setRating(jsonObject11.getString("rating_user_stars"));
                                item11.setDate(jsonObject11.getString("rating_date"));
                                item11.setImage(jsonObject11.getString("rating_author_image"));
                                item11.setReply(jsonObject11.getString("reply_text"));
                                item11.setCanReply(jsonObject11.getBoolean("can_reply"));

                                listitemsiner.add(item11);
                            }
                            item.setListitemsiner(listitemsiner);
                        }

                        listitems.add(item);

                    }

                    itemSendRecMesageAdapter = new ItemRatingListAdapter(getActivity(), listitems);
                    itemSendRecMesageAdapter.reactEmojiesBool = reaction;
                    if (listitems.size() > 0 & ratingRecylerView != null) {
                        textViewRatingTitleTop.setText(jsonObjectRatingInfo.getString("section_title"));
                        textViewRatingTitleTop.setVisibility(View.VISIBLE);

                        ratingRecylerView.setAdapter(itemSendRecMesageAdapter);

                        itemSendRecMesageAdapter.setOnItemClickListener(new BlogCommentOnclicklinstener() {
                            @Override
                            public void onItemClick(blogCommentsModel item) {
                                if (settingsMain.getAppOpen()) {
                                    Toast.makeText(getActivity(), settingsMain.getNoLoginMessage(), Toast.LENGTH_SHORT).show();
                                } else {
                                    adforest_ratingReplyDialog(item.getComntId());
//                                    adforest_commentrating(item.getComntId(),);
                                }
                            }
                        });
                    }
                }
                if (jsonObjectPagination.getBoolean("has_next_page")) {
                    ratingLoadLayout.setVisibility(View.VISIBLE);
                    ratingLoadMoreButton.setText(jsonObjectRatingInfo.getString("loadmore_btn"));
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
            if (listitems.isEmpty()) {
                textViewRatingTitleTop.setText(jsonObjectRatingInfo.getString("no_rating_message"));
                textViewRatingTitleTop.setVisibility(View.VISIBLE);
                if (jsonObjectRatingInfo.getBoolean("can_rate") && !jsonObjectStaticText.getString("send_msg_btn_type").equals("receive")
                        && !settingsMain.getUserLogin().equals("0")) {
                    textViewNoCurrentRating.setVisibility(View.VISIBLE);
                    textViewNoCurrentRating.setText(jsonObjectRatingInfo.getString("no_rating"));
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }


    private void adforest_setAllViewsText(final JSONObject data, JSONObject profileText, JSONObject buttonTexts) {


        try {


            phoneNumber = data.getString("phone");
            adAuthorId = data.getString("ad_author_id");

            textViewAdName.setText(data.getString("ad_title"));
            textViewLocation.setText(data.getString("location_top"));

            if (data.getBoolean("is_feature")) {
                featuredText.setVisibility(View.VISIBLE);
                featuredText.setText(data.getString("is_feature_text"));
                featuredText.setBackgroundColor(Color.parseColor("#E52D27"));
            } else {
                featuredText.setVisibility(View.GONE);
            }
            if (!data.getJSONObject("location").getString("lat").equals("") || !data.getJSONObject("location").getString("long").equals("")) {
                latitude = Double.parseDouble(data.getJSONObject("location").getString("lat"));
                longitude = Double.parseDouble(data.getJSONObject("location").getString("long"));
                getDirectionBtn.setVisibility(View.VISIBLE);
            } else {
                getDirectionBtn.setVisibility(View.GONE);
            }
            if (data.getJSONObject("ad_timer").getBoolean("is_show")) {
                curvedLayoutTimer.setVisibility(View.VISIBLE);
                JSONArray timeArray = data.getJSONObject("ad_timer").getJSONArray("timer");
                JSONObject timer_strings = data.getJSONObject("ad_timer").getJSONObject("timer_strings");
                txtDay.setText(timer_strings.getString("days"));
                txtHours.setText(timer_strings.getString("hurs"));
                txtMin.setText(timer_strings.getString("mins"));
                txtSec.setText(timer_strings.getString("secs"));
                Animation anim = AnimationUtils.loadAnimation(getActivity()
                        , R.anim.fab_scale_up);
                countDownStart(timeArray);

                txtViewDays.setText(timerDays);
                txtViewHours.setText(timerHours);
                txtViewMin.setText(timerMin);
                txtViwsSec.setText(timerSecs);
//                long days = TimeUnit.DAYS.toMillis(Integer.parseInt(timerDays));     // 1 day to milliseconds.
//                long minutes = TimeUnit.MINUTES.toMillis(Integer.parseInt(timerMin)); // 23 minutes to milliseconds.
//                long hours = TimeUnit.HOURS.toMillis(Integer.parseInt(timerHours));    // 4 hours to milliseconds.
//                long seconds = TimeUnit.SECONDS.toMillis(Integer.parseInt(timerSecs)); // 96 seconds to milliseconds.
//                long finda

                final int[] days = {Integer.parseInt(timerDays)};
                final int[] hours = {Integer.parseInt(timerHours)};
                final int[] minutes = {Integer.parseInt(timerMin)};
                final int[] seconds = {Integer.parseInt(timerSecs)};
                marvelCountTimer = new CountDownTimer(minutes[0] * 60000,1000) {
                    @Override
                    public void onTick(long millisUntilFinished) {
                        Calendar c = Calendar.getInstance();
                        int ssss = Integer.parseInt(timerSecs);
                        int mmm = c.get(Calendar.MINUTE);

                        if (seconds[0]<0){
                            seconds[0] = 59;
                            minutes[0]--;
                            if (minutes[0]<0){
                                minutes[0] = 59;
                                hours[0]--;
                                txtViewHours.setText(String.valueOf(hours[0]));
                                if (hours[0]<0){
                                    hours[0] = 23;
                                    days[0]--;
                                    txtViewDays.setText(String.valueOf(days[0]));
                                }
                            }
                            txtViewMin.setText(String.valueOf(minutes[0]));
                        }
                        txtViwsSec.setText(String.valueOf(seconds[0]--));
//                        txtViewMin.setText(String.valueOf(mmm));


                    }

                    @Override
                    public void onFinish() {

                    }
                };
                marvelCountTimer.start();

                Log.d("sasaassasass", timerDays + ":" + timerHours + ":" + timerMin + ":" + timerSecs);
            } else {
                curvedLayoutTimer.setVisibility(View.GONE);
            }


//            if (data.getJSONObject("ad_timer").getBoolean("is_show")) {
//                DynamicConfig.Builder dynamicConfigBuilder = new DynamicConfig.Builder();
//                JSONArray timeArray = data.getJSONObject("ad_timer").getJSONArray("timer");
//                JSONObject timer_strings = data.getJSONObject("ad_timer").getJSONObject("timer_strings");
//                dynamicConfigBuilder.setShowDay(true)
//                        .setShowHour(true)
//                        .setShowMinute(true)
//                        .setShowSecond(true)
//                        .setSuffixDay(timer_strings.getString("days"))
//                        .setSuffixHour(timer_strings.getString("hurs"))
//                        .setSuffixMinute(timer_strings.getString("mins"))
//                        .setSuffixSecond(timer_strings.getString("secs"))
//                        .setSuffixGravity(Gravity.BOTTOM)
//                        .setSuffixTextColor(Color.WHITE)
//                        .setSuffixTextSize(10)
//                        .setTimeTextColor(Color.WHITE)
//                        .setTimeTextSize(16);
//                countDown.dynamicShow(dynamicConfigBuilder.build());
////                countDown.setVisibility(View.VISIBLE);
//
//                Log.d("Info date", AdsTimerConvert.adforest_bidTimer(timeArray) + "");
//                countDown.start(AdsTimerConvert.adforest_bidTimer(timeArray));
//            }

            textViewSeen.setText(data.getString("ad_view_count"));
            priceType = data.getJSONObject("ad_price").getString("price_type");
            if (priceType.equals("")) {
                textViewPrice.setText(data.getJSONObject("ad_price").getString("price"));
            } else {
                textViewPrice.setText(data.getJSONObject("ad_price").getString("price") + "(" + data.getJSONObject("ad_price").getString("price_type") + ")");
            }
            textViewDate.setText(data.getString("ad_date"));

            if (textViewPrice.getText().toString().equals("")) {
                textViewPrice.setVisibility(View.GONE);
            } else {
                textViewPrice.setVisibility(View.VISIBLE);
            }

            //if there is any ad video show the video and play the video
            if (!data.getJSONObject("ad_video").getString("video_id").equals("") && !settingsMain.getYoutubeApi().equals("")) {
                youtube_view.setVisibility(View.VISIBLE);
                YouTubePlayerSupportFragment youTubePlayerFragment = YouTubePlayerSupportFragment.newInstance();
                youTubePlayerFragment.initialize(settingsMain.getYoutubeApi(), new YouTubePlayer.OnInitializedListener() {

                    @Override
                    public void onInitializationSuccess(YouTubePlayer.Provider provider, YouTubePlayer player, boolean wasRestored) {
                        if (!wasRestored) {
                            player.setPlayerStyle(YouTubePlayer.PlayerStyle.DEFAULT);
                            try {
//                                player.loadVideo(data.getJSONObject("ad_video").getString("video_id"));
                                player.cueVideo(data.getJSONObject("ad_video").getString("video_id"));
                                player.setFullscreen(false);
                                player.setShowFullscreenButton(false);
                                player.play();
//                                player.setFullscreenControlFlags(YouTubePlayer.FULLSCREEN_FLAG_CUSTOM_LAYOUT);
////Tell the player how to control the change
//                                player.setOnFullscreenListener(new YouTubePlayer.OnFullscreenListener(){
//                                    @Override
//                                    public void onFullscreen(boolean arg0) {
//// do full screen stuff here, or don't. I started a YouTubeStandalonePlayer
//// to go to full screen
//                                    }});

                            } catch (JSONException e) {
                                e.printStackTrace();
                            }

                        }
                    }


                    @Override
                    public void onInitializationFailure(YouTubePlayer.Provider provider, YouTubeInitializationResult error) {
                        // YouTube error
                        String errorMessage = error.toString();
                        Toast.makeText(getActivity(), errorMessage, Toast.LENGTH_LONG).show();
                        Log.d("errorMessage:", errorMessage);
                    }
                });
                FragmentTransaction transaction = getChildFragmentManager().beginTransaction();
                transaction.add(R.id.youtube_view, youTubePlayerFragment).commit();
            }


            if (data.getJSONObject("ad_tags_show").getString("name").equals("")) {
                htmlTextView.setHtml(data.getString("ad_desc"), new HtmlResImageGetter(htmlTextView));
            } else
                htmlTextView.setHtml(data.getString("ad_desc") +
                                "<br><br><b><font color=\"black\">" + data.getJSONObject("ad_tags_show").getString("name") + "</b> : " +
                                data.getJSONObject("ad_tags_show").getString("value")
                        , new HtmlResImageGetter(htmlTextView));

            linearLayout1.removeAllViews();
            linearLayout2.removeAllViews();
            linearLayoutOuter.removeAllViews();

            if (noOfCol == 2) {

                JSONArray jsonArray = data.getJSONArray("fieldsData");
                for (int item = 0; item < jsonArray.length(); item++) {
                    HtmlTextView htmlTextView = new HtmlTextView(getActivity());
                    LinearLayout layout = null;
                    htmlTextView.setPadding(0, 0, 0, 10);
                    String s = jsonArray.getJSONObject(item).getString("type");
                    if ("textfield_url".equals(s)) {
                        htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : " +
                                "<a href=" + jsonArray.getJSONObject(item).getString("value") + ">" + JsonObjectData.getString("click_here_text") + "</a>", new HtmlResImageGetter(htmlTextView));
                    } else if ("color_field".equals(s)) {
                        Drawable mDrawable = getResources().getDrawable(R.drawable.bg_addetail_color);
                        mDrawable.setColorFilter(new
                                PorterDuffColorFilter(Color.parseColor(jsonArray.getJSONObject(item).getString("value")), PorterDuff.Mode.SRC_IN));
                        ImageView imageView = new ImageView(getActivity());
                        imageView.setImageDrawable(mDrawable);

                        htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : ",
                                new HtmlAssetsImageGetter(htmlTextView));

                        layout = new LinearLayout(getActivity());
                        layout.setOrientation(LinearLayout.HORIZONTAL);
                        layout.addView(htmlTextView);
                        layout.addView(imageView);
                    } else {
                        htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : " +
                                jsonArray.getJSONObject(item).getString("value"), new HtmlResImageGetter(htmlTextView));
                    }
                    htmlTextView.setTextColor(Color.BLACK);
                    if (item % 2 == 0) {
                        if (jsonArray.getJSONObject(item).getString("type").equals("color_field") && layout != null) {
                            linearLayout1.addView(layout);
                        } else {
                            linearLayout1.addView(htmlTextView);
                        }
                    } else {
                        if (jsonArray.getJSONObject(item).getString("type").equals("color_field") && layout != null) {
                            linearLayout2.addView(layout);
                        } else {
                            linearLayout2.addView(htmlTextView);
                        }
                    }
                }
            } else {
                JSONArray jsonArray = data.getJSONArray("fieldsData");
                for (int item = 0; item < jsonArray.length(); item++) {
                    HtmlTextView htmlTextView = new HtmlTextView(getActivity());
                    LinearLayout layout = null;
                    htmlTextView.setPadding(0, 0, 0, 10);
                    switch (jsonArray.getJSONObject(item).getString("type")) {
                        case "textfield_url":
                            htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : " +
                                    "<a href=" + jsonArray.getJSONObject(item).getString("value") + ">" + JsonObjectData.getString("click_here_text") + "</a>", new HtmlResImageGetter(htmlTextView));
                            break;
                        case "color_field":

                            Drawable mDrawable = getResources().getDrawable(R.drawable.bg_addetail_color);
                            mDrawable.setColorFilter(new
                                    PorterDuffColorFilter(Color.parseColor(jsonArray.getJSONObject(item).getString("value")), PorterDuff.Mode.SRC_IN));
                            ImageView imageView = new ImageView(getActivity());
                            imageView.setImageDrawable(mDrawable);
                            layout = new LinearLayout(getActivity());
                            layout.setOrientation(LinearLayout.HORIZONTAL);
                            layout.addView(htmlTextView);
                            layout.addView(imageView);

                            htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : ",
                                    new HtmlAssetsImageGetter(htmlTextView));
                            break;
                        default:
                            htmlTextView.setHtml("<b>" + jsonArray.getJSONObject(item).getString("key") + "</b> : " +
                                    jsonArray.getJSONObject(item).getString("value"), new HtmlResImageGetter(htmlTextView));
                            break;
                    }
                    htmlTextView.setTextColor(Color.BLACK);

                    if (jsonArray.getJSONObject(item).getString("type").equals("color_field") && layout != null) {
                        linearLayoutOuter.addView(layout);
                    } else {
                        linearLayoutOuter.addView(htmlTextView);
                    }

                }
            }

            HtmlTextView htmlTextView1 = new HtmlTextView(getActivity());
            htmlTextView1.setPadding(0, 0, 0, 10);
            htmlTextView1.setHtml("<b>" + data.getJSONObject("location").getString("title") + "</b> : " +
                    data.getJSONObject("location").getString("address"), new HtmlResImageGetter(htmlTextView));
            htmlTextView1.setTextColor(Color.BLACK);
            linearLayoutOuter.addView(htmlTextView1);

            imageUrls.clear();

            List<SliderItem> sliderItemList = new ArrayList<>();

            for (int i = 0; i < data.getJSONArray("images").length(); i++) {
                SliderItem sliderItem = new SliderItem();
                sliderItem.setImageUrl(data.getJSONArray("images").getJSONObject(i).getString("full"));
                sliderItemList.add(sliderItem);

            }

            sliderAdapterExample.renewItems(sliderItemList);
            Log.d("assaasasasassa", sliderItemList.toString());
//            if (sliderItemList) {
//                bannerSlider.setInfiniteAdapterEnabled(false);
//            }
            if (sliderItemList.size() == 1) {
                bannerSlider.setInfiniteAdapterEnabled(false);

            } else {
                bannerSlider.setInfiniteAdapterEnabled(true);

            }
            sliderAdapterExample.setOnItemClickListener(new imageAdapterOnclicklistner() {
                @Override
                public void onItemClick(SliderItem sliderItem, int Position) {
                    Intent i = new Intent(MarvelAdDetailFragment.this.getActivity(), FullScreenViewActivity.class);
                    i.putExtra("imageUrls", imageUrls);
                    i.putExtra("position", Position);
                    MarvelAdDetailFragment.this.startActivity(i);

                }
            });
            for (int ii = 0; ii < data.getJSONArray("slider_images").length(); ii++) {
                imageUrls.add(data.getJSONArray("slider_images").get(ii).toString());
                Log.d("info slider images", data.getJSONArray("slider_images").get(ii).toString());

            }

            if (messageBtn != null)
                messageBtn.setText(buttonTexts.getString("send_msg_btn"));
            if (callBtn != null)
                callBtn.setText(buttonTexts.getString("call_now_btn"));

            bidBtn.setText(buttonTexts.getString("bid_now_btn"));
            bidStatisticsBtn.setText(buttonTexts.getString("bid_stats_btn"));
            jsonObjectBidTabs = buttonTexts.getJSONObject("bid_tabs");

            if (!buttonTexts.getBoolean("show_call_btn") || !buttonTexts.getBoolean("show_megs_btn")) {
                if (temphide != null)
                    temphide.setVisibility(View.GONE);
            } else {
                if (temphide != null)
                    temphide.setVisibility(View.VISIBLE);
            }

            if (!buttonTexts.getBoolean("show_call_btn")) {
                if (messageBtn != null)
                    showHideButtons(messageBtn);
            }
            if (!buttonTexts.getBoolean("show_megs_btn")) {
                if (callBtn != null)
                    showHideButtons(callBtn);
            }

            if (buttonTexts.getBoolean("ad_bids_enable")) {
                cardViewBidSec.setVisibility(View.VISIBLE);

                textViewTotBid.setText(buttonTexts.getJSONObject("ad_bids").getString("total_text"));
                textViewTotBidtext.setText(buttonTexts.getJSONObject("ad_bids").getString("total"));
                textViewHighBid.setText(buttonTexts.getJSONObject("ad_bids").getString("max_text"));
                textViewHighBidtext.setText(buttonTexts.getJSONObject("ad_bids").getJSONObject("max").getString("price"));
                textViewLowBid.setText(buttonTexts.getJSONObject("ad_bids").getString("min_text"));
                textViewLowBidtext.setText(buttonTexts.getJSONObject("ad_bids").getJSONObject("min").getString("price"));
            } else {
                cardViewBidSec.setVisibility(View.GONE);
            }

            textViewLastLogin.setText(profileText.getString("last_login"));

            blockUserObject = buttonTexts.getJSONObject("block_user");
            if (blockUserObject.getBoolean("is_show") && !settingsMain.getAppOpen()) {
                tv_blockUser.setVisibility(View.VISIBLE);
                tv_blockUser.setText(blockUserObject.getString("text"));
            }
            textViewUserName.setText(profileText.getString("display_name"));
            verifyBtn.setText(profileText.getJSONObject("verify_buton").getString("text"));
            textViewRateNo.setText(profileText.getJSONObject("rate_bar").getString("text"));

            verifyBtn.setBackground(CustomBorderDrawable.customButton(5, 5, 5, 5,
                    profileText.getJSONObject("verify_buton").getString("color"),
                    profileText.getJSONObject("verify_buton").getString("color"),
                    profileText.getJSONObject("verify_buton").getString("color"), 0));

            ratingBar.setNumStars(5);
            ratingBar.setRating(Float.parseFloat(profileText.getJSONObject("rate_bar").getString("number")));
            textViewRateNo.setText(profileText.getJSONObject("rate_bar").getString("text"));
            Picasso.get().load(profileText.getString("profile_img"))
                    .transform(new CircleTransform())
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(imageViewProfile);

        } catch (JSONException e) {
            e.printStackTrace();
        }


        list.clear();

        try {
            data.getJSONArray("related_ads");

            textViewRelated.setText(buttonTexts.getString("related_posts_title"));
            textViewDescript.setText(buttonTexts.getString("description_title"));

            Log.d("Related Ads array", "" + data.getJSONArray("related_ads").toString());

            for (int i = 0; i < data.getJSONArray("related_ads").length(); i++) {
                catSubCatlistModel item = new catSubCatlistModel();

                item.setId(data.getJSONArray("related_ads").getJSONObject(i).getString("ad_id"));
                item.setCardName(data.getJSONArray("related_ads").getJSONObject(i).getString("ad_title"));
                item.setDate(data.getJSONArray("related_ads").getJSONObject(i).getString("ad_date"));
                item.setPrice(data.getJSONArray("related_ads").getJSONObject(i).getJSONObject("ad_price").getString("price"));
                item.setLocation(data.getJSONArray("related_ads").getJSONObject(i).getJSONObject("ad_location").getString("address"));
                item.setImageResourceId(data.getJSONArray("related_ads").getJSONObject(i).getJSONArray("ad_images").getJSONObject(0).getString("thumb"));

                item.setIs_show_countDown(data.getJSONArray("related_ads").getJSONObject(i).getJSONObject("ad_timer").getBoolean("is_show"));
                if (data.getJSONArray("related_ads").getJSONObject(i).getJSONObject("ad_timer").getBoolean("is_show"))
                    item.setTimer_array(data.getJSONArray("related_ads").getJSONObject(i).getJSONObject("ad_timer").getJSONArray("timer"));

                Log.d("Related ads Image", "" + data.getJSONArray("related_ads").getJSONObject(i).getJSONArray("ad_images").getJSONObject(0).getString("thumb"));
                list.add(item);
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    void showHideButtons(TextView btnShowHide) {
        if (btnShowHide.getParent() != null) {
            ((ViewGroup) btnShowHide.getParent()).removeView(btnShowHide);
            linearLayout.removeAllViewsInLayout();
        }
        linearLayout.addView(btnShowHide);
    }

    void adforest_showDilogMessage() {
        dialog = new Dialog(getActivity(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_message);
        //noinspection ConstantConditions
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        Button Send = dialog.findViewById(R.id.send_button);
        Button Cancel = dialog.findViewById(R.id.cancel_button);

        final EditText message = dialog.findViewById(R.id.editText3);

        Send.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));
        Cancel.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));

        try {
            Send.setText(jsonObjectSendMessage.getString("btn_send"));
            message.setHint(jsonObjectSendMessage.getString("input_textarea"));
            Cancel.setText(jsonObjectSendMessage.getString("btn_cancel"));
        } catch (JSONException e) {
            e.printStackTrace();
        }

        Send.setOnClickListener(v -> {

            if (!message.getText().toString().isEmpty()) {
                adforest_sendMessage(message.getText().toString());
                dialog.dismiss();
            } else
                message.setError("");
        });

        Cancel.setOnClickListener(v -> dialog.dismiss());
        dialog.show();
    }

    @SuppressLint("ResourceAsColor")
    void adforest_showDilogCall() {
        dialog = new Dialog(getActivity(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_call);
        //noinspection ConstantConditions
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        Log.d("info call object", jsonObjectCallNow.toString() + phoneNumber);
        final TextView textViewCallNo = dialog.findViewById(R.id.textView2);
        final TextView verifiedOrNotText = dialog.findViewById(R.id.verifiedOrNotText);
        try {
            if (!jsonObjectCallNow.getBoolean("is_phone_verified") && phoneNumber.contains("+")) {
                phoneNumber = phoneNumber.replace("+", "");
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        textViewCallNo.setText(phoneNumber);

        Button Send = dialog.findViewById(R.id.send_button);
        Button Cancel = dialog.findViewById(R.id.cancel_button);
        try {
            if (jsonObjectCallNow.getBoolean("phone_verification")) {
                verifiedOrNotText.setVisibility(View.VISIBLE);
                if (jsonObjectCallNow.getBoolean("is_phone_verified")) {
                    phoneNumberVerified = true;
//                    Toast.makeText(getActivity(), "sadsadsa" + jsonObjectCallNow.getBoolean("is_phone_verified"), Toast.LENGTH_LONG).show();
                    verifiedOrNotText.setText(jsonObjectCallNow.getString("is_phone_verified_text"));
                    verifiedOrNotText.setBackgroundResource(R.drawable.ic_verified_green_logo);
                } else {
                    verifiedOrNotText.setText(jsonObjectCallNow.getString("is_phone_verified_text"));
                    verifiedOrNotText.setBackgroundResource(R.drawable.ic_oncall_red_logo);
                }

            }
        } catch (JSONException e) {
            e.printStackTrace();
        }

        Send.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));
        Cancel.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));

        try {
            Send.setText(jsonObjectCallNow.getString("btn_send"));
            Cancel.setText(jsonObjectCallNow.getString("btn_cancel"));
        } catch (JSONException e) {
            e.printStackTrace();
        }
        Send.setOnClickListener(v -> runtimePermissionHelper.requestCallPermission(1));

        Cancel.setOnClickListener(v -> dialog.dismiss());
        callDialog = dialog;
        dialog.show();
    }

    void adforest_showDilogReport() {
        dialog = new Dialog(getActivity(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_report);
        //noinspection ConstantConditions
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        Button Send = dialog.findViewById(R.id.send_button);
        Button Cancel = dialog.findViewById(R.id.cancel_button);

        Send.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));
        Cancel.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));

        final Spinner spinner = dialog.findViewById(R.id.spinner);
        final EditText editText = dialog.findViewById(R.id.editText3);

        item = new myAdsModel();

        try {
            Send.setText(jsonObjectReport.getString("btn_send"));
            editText.setHint(jsonObjectReport.getString("input_textarea"));
            Cancel.setText(jsonObjectReport.getString("btn_cancel"));

            item.setSpinerValue(jsonObjectReport.getJSONObject("select").getJSONArray("name"));
            item.setSpinerData(jsonObjectReport.getJSONObject("select").getJSONArray("value"));
            ArrayAdapter<String> adapter = new ArrayAdapter<>(getActivity(), R.layout.spinner_item, item.getSpinerData());
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            spinner.setAdapter(adapter);

        } catch (JSONException e) {
            e.printStackTrace();
        }

        Send.setOnClickListener(v -> {
            if (!editText.getText().toString().isEmpty()) {
                adforest_sendReport(item.getSpinerValue().get(spinner.getSelectedItemPosition()), editText.getText().toString());
                dialog.dismiss();
            } else {
                editText.setError("");
            }

        });

        Cancel.setOnClickListener(v -> dialog.dismiss());
        dialog.show();
    }

    void adforest_sendReport(String type, String message) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("ad_id", myId);
            params.addProperty("option", type);
            params.addProperty("comments", message);
            Log.d("info sendReport Status", params.toString());

            Call<ResponseBody> myCall = restService.postSendReport(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info SendReport Respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            Log.d("info SendReport Respon", "" + response.toString());

                            if (response.getBoolean("success")) {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
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
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    Log.d("info SendReport error", String.valueOf(t));
                    Log.d("info SendReport error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    void adforest_addToFavourite() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("ad_id", myId);
            Log.d("info sendFavourite", myId);
            Call<ResponseBody> myCall = restService.postAddToFavourite(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info AdToFav Respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
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
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    Log.d("info AdToFav error", String.valueOf(t));
                    Log.d("info AdToFav error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    void adforest_makeFeature() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("ad_id", myId);
            Log.d("info makeFeature", myId);
            Call<ResponseBody> myCall = restService.postMakeFeatured(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info makeFeature Respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);
                                nestedScroll.setVisibility(View.VISIBLE);
                                adforest_recreateAdDetail();

                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                Handler handler = new Handler();
                                handler.postDelayed(new Runnable() {
                                    @Override
                                    public void run() {
                                        PackagesFragment.calledFromPackages = true;
                                        replaceFragment(new PackagesFragment(), "PackagesFragment");
                                    }
                                }, 1500);
                            }
                            shimmerFrameLayout.stopShimmer();
                            shimmerFrameLayout.setVisibility(View.GONE);
                            loadingLayout.setVisibility(View.GONE);


                        }
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
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);

                    Log.d("info makeFeature error", String.valueOf(t));
                    Log.d("info makeFeature error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);

            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    void adforest_sendMessage(String msg) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            Call<ResponseBody> call;
            if (isWhizzChatEnabled){
                params.addProperty("post_id", whizzChatModel.getPostId());
                params.addProperty("chat_id", whizzChatModel.getChatId());
                params.addProperty("msg", msg);

                call = restService.sendWhizzChatMessage(params, UrlController.AddHeaders(getActivity()));
                call.enqueue(new Callback<ResponseBody>() {
                    @Override
                    public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);
                        if (response.isSuccessful()){
                            try {
                                JSONObject jsonObject = new JSONObject(response.body().string());
                                if (jsonObject.getBoolean("success")){
                                    Intent i = new Intent(getActivity(), WhizzChatList.class);
                                    startActivity(i);
                                }
                                Toast.makeText(getActivity(), jsonObject.getString("message"), Toast.LENGTH_SHORT).show();
                            } catch (JSONException e) {
                                e.printStackTrace();
                            } catch (IOException e) {
                                e.printStackTrace();
                            }
                        }
                    }

                    @Override
                    public void onFailure(Call<ResponseBody> call, Throwable t) {
                        Toast.makeText(getActivity(), t.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                });
            }else {
                params.addProperty("ad_id", myId);
                params.addProperty("message", msg);
                Log.d("info sendMeassage", myId);

                call = restService.postSendMessageFromAd(params, UrlController.AddHeaders(getActivity()));
                call.enqueue(new Callback<ResponseBody>() {
                    @Override
                    public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                        try {
                            if (responseObj.isSuccessful()) {
                                Log.d("info sendMeassage Resp", "" + responseObj.toString());

                                JSONObject response = new JSONObject(responseObj.body().string());

                                if (response.getBoolean("success")) {
                                    shimmerFrameLayout.stopShimmer();
                                    shimmerFrameLayout.setVisibility(View.GONE);
                                    loadingLayout.setVisibility(View.GONE);
                                    nestedScroll.setVisibility(View.VISIBLE);
//                                MessagesFragment messagesFragment= new MessagesFragment();
//                                replace(messagesFragment,"MessagesFragment");
                                    Intent intent = new Intent(getActivity(), Message.class);
                                    startActivity(intent);
                                    getActivity().overridePendingTransition(R.anim.right_enter, R.anim.left_out);

                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                } else {
                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                }
                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);

                            }
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
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);


                        Log.d("info sendMeassage error", String.valueOf(t));
                        Log.d("info sendMeassage error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                });
            }
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);


            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    void adforest_ratingReplyDialog(final String comntId) {
        String text = null, sendBtn = null, cancelBtn = null;
        try {
            JSONObject dialogObject = jsonObjectRatingInfo.getJSONObject("rply_dialog");
            text = dialogObject.getString("text");
            sendBtn = dialogObject.getString("send_btn");
            cancelBtn = dialogObject.getString("cancel_btn");

        } catch (JSONException e) {
            e.printStackTrace();
        }
        final Dialog dialog;
        dialog = new Dialog(getActivity(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_message);
        //noinspection ConstantConditions
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        Button Send = dialog.findViewById(R.id.send_button);
        Button Cancel = dialog.findViewById(R.id.cancel_button);

        Send.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));
        Cancel.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));

        final EditText message = dialog.findViewById(R.id.editText3);
        message.setHint(text);
        Cancel.setText(cancelBtn);
        Send.setText(sendBtn);

        Send.setOnClickListener(v -> {
            if (!message.getText().toString().isEmpty()) {
                JsonObject params = new JsonObject();
                params.addProperty("ad_id", myId);
                params.addProperty("comment_id", comntId);
                params.addProperty("rating_comments", message.getText().toString());
                adforest_postRating(params);
                dialog.dismiss();
            }
            if (message.getText().toString().isEmpty())
                message.setError("");
        });

        Cancel.setOnClickListener(v -> dialog.dismiss());
        dialog.show();
    }

    void adforest_postRating(JsonObject params) {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();


            Log.d("info send PostRating", params.toString());
            Call<ResponseBody> myCall = restService.postRating(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info PostRating Respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                                shimmerFrameLayout.stopShimmer();
                                shimmerFrameLayout.setVisibility(View.GONE);
                                loadingLayout.setVisibility(View.GONE);
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                            shimmerFrameLayout.stopShimmer();
                            shimmerFrameLayout.setVisibility(View.GONE);
                            loadingLayout.setVisibility(View.GONE);
                            adforest_recreateAdDetail();
                            simpleRatingBar.setRating(0);
                            editTextRattingComment.setText("");
                        }
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
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    Log.d("info makeFeature error", String.valueOf(t));
                    Log.d("info makeFeature error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }

    public void adforest_blockUserDialog() {
        try {

            android.app.AlertDialog.Builder alert = new android.app.AlertDialog.Builder(getContext());
            alert.setTitle(blockUserObject.getString("popup_title"));
            alert.setCancelable(false);
            alert.setMessage(blockUserObject.getString("popup_text"));
            alert.setPositiveButton(blockUserObject.getString("popup_confirm"), new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog,
                                    int which) {
                    adforest_blockUser();
                    dialog.dismiss();
                }
            });
            alert.setNegativeButton(blockUserObject.getString("popup_cancel"), new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialogInterface, int i) {
                    dialogInterface.dismiss();
                }
            });
            alert.show();


        } catch (JSONException e) {
            e.printStackTrace();

        }
    }

    public void adforest_blockUser() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("user_id", adAuthorId);
            Log.d("info Send terms id =", "" + params.toString());

            Call<ResponseBody> myCall = restService.postBlockUser(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info terms responce ", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Toast.makeText(getActivity(), response.getString("message"), Toast.LENGTH_SHORT).show();
                                getActivity().finish();
                                HomeActivity.activity.recreate();

                            } else {
                                Toast.makeText(getActivity(), response.getString("message"), Toast.LENGTH_SHORT).show();
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
                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info CustomPages ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        Log.d("info CustomPages err", String.valueOf(t));
                        Log.d("info CustomPages err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onResume() {

        try {
            if (settingsMain.getAnalyticsShow() && !settingsMain.getAnalyticsId().equals(""))
                AnalyticsTrackers.getInstance().trackScreenView("Rating");
            super.onResume();

        } catch (IllegalStateException e) {
            e.printStackTrace();
        }
    }

    //go to public profile fragment when click userProfile image or name
    public void goToPublicProfile() {
        FragmentPublic_Profile fragment = new FragmentPublic_Profile();
        Bundle bundle = new Bundle();
        bundle.putString("user_id", adAuthorId);
        bundle.putString("requestFrom", "MarvelDetailPage");
        fragment.setArguments(bundle);
        replaceFragment(fragment, "FragmentPublic_Profile");
    }

    public void adforest_recreateAdDetail() {
        MarvelAdDetailFragment marvelAdDetailFragment = new MarvelAdDetailFragment();
        Bundle bundle = new Bundle();
        bundle.putString("id", myId);
        marvelAdDetailFragment.setArguments(bundle);
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, marvelAdDetailFragment);
        transaction.commit();
    }

    public void replaceFragment(Fragment someFragment, String tag) {
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, someFragment, tag);
        transaction.addToBackStack(tag);
        transaction.commit();
    }

    public void replace(Fragment someFragment, String tag) {

        FragmentManager fm = getFragmentManager();
        Fragment fragment = fm.findFragmentByTag(tag);
        Fragment fragment2 = fm.findFragmentById(R.id.frameContainer);

        if (fragment != fragment2) {
            FragmentTransaction transaction = getFragmentManager().beginTransaction();
            transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
            transaction.replace(R.id.frameContainer, someFragment, tag);
            transaction.addToBackStack(null);
            transaction.commit();
        }
    }

    public void adforestCall() {
        if (callDialog != null) {
            Intent intent = new Intent(Intent.ACTION_CALL, Uri.parse("tel:" + phoneNumber));
            startActivity(intent);
            callDialog.dismiss();
        }
    }

    public void adforestWhatsapp() {
        String contact = phoneNumber; // use country code with your phone number
        String url = "https://api.whatsapp.com/send?phone=" + contact;
        try {
            PackageManager pm = getContext().getPackageManager();
            pm.getPackageInfo("com.whatsapp", PackageManager.GET_ACTIVITIES);
            Intent i = new Intent(Intent.ACTION_VIEW);
            i.setData(Uri.parse(url));
            startActivity(i);
        } catch (PackageManager.NameNotFoundException e) {
            Toast.makeText(getActivity(), "Whatsapp app not installed in your phone", Toast.LENGTH_SHORT).show();
            e.printStackTrace();
        }

    }

    @Override
    public void onSuccessPermission(int code) {
        adforestCall();
    }

    private void adforest_showContactSellerDialog() {
        final Dialog dialog = new Dialog(getContext(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_contact_seller);
        //noinspection ConstantConditions
        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        Button Send = dialog.findViewById(R.id.send_button);
        Button Cancel = dialog.findViewById(R.id.cancel_button);
        final EditText et_email = dialog.findViewById(R.id.et_email);
        final EditText et_name = dialog.findViewById(R.id.et_name);
        final EditText et_phonenumber = dialog.findViewById(R.id.et_phoneNumber);
        final EditText textArea_information = dialog.findViewById(R.id.et_message);
        Boolean emailRequired = false;
        Boolean nameRequired = false;
        Boolean phoneRequired = false;
        Boolean textArea_informationRequired = false;


        try {
            et_name.setHint(jsonObjectSellerContact.getJSONObject("popup_name").getString("title"));
            nameKey = jsonObjectSellerContact.getJSONObject("popup_name").getString("key");
            nameRequired = jsonObjectSellerContact.getJSONObject("popup_name").getBoolean("is_required");
            Log.d("formNameRequired", nameRequired.toString());
            ChecknameRequired = nameRequired;

            et_email.setHint(jsonObjectSellerContact.getJSONObject("popup_email").getString("title"));
            emailKey = jsonObjectSellerContact.getJSONObject("popup_email").getString("key");
            emailRequired = jsonObjectSellerContact.getJSONObject("popup_email").getBoolean("is_required");
            Log.d("formNameRequired", emailRequired.toString());
            CheckemailRequired = emailRequired;
            et_phonenumber.setHint(jsonObjectSellerContact.getJSONObject("popup_phone").getString("title"));
            et_phonenumber.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

            phonenumberKey = jsonObjectSellerContact.getJSONObject("popup_phone").getString("key");
            phoneRequired = jsonObjectSellerContact.getJSONObject("popup_phone").getBoolean("is_required");
            Log.d("formNameRequired", phoneRequired.toString());
            CheckphoneRequired = phoneRequired;


            textArea_information.setHint(jsonObjectSellerContact.getJSONObject("popup_message").getString("title"));
            messageKey = jsonObjectSellerContact.getJSONObject("popup_message").getString("key");
            textArea_informationRequired = jsonObjectSellerContact.getJSONObject("popup_message").getBoolean("is_required");
            Log.d("formNameRequired", textArea_informationRequired.toString());
            ChecktextArea_informationRequired = textArea_informationRequired;

            Send.setText(jsonObjectSellerContact.getString("popup_confirm"));
            Cancel.setText(jsonObjectSellerContact.getString("popup_cancel"));

        } catch (JSONException e) {
            e.printStackTrace();
        }
        Send.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        Cancel.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));


        Send.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                boolean check = true;
                String email = et_email.getText().toString();
                String name = et_name.getText().toString();
                String phonenumber = et_phonenumber.getText().toString();
                String message = textArea_information.getText().toString();
                if (CheckemailRequired) {
                    if (et_email.getText().toString().equals("")) {
                        if (et_email.getText().toString().equals("")) {
                            et_email.setError("!");
                            et_email.requestFocus();
                            check = false;
                        }

                    }
                }
                if (ChecknameRequired) {
                    if (et_name.getText().toString().equals("")) {
                        if (et_name.getText().toString().equals("")) {
                            et_name.setError("!");
                            et_name.requestFocus();
                            check = false;
                        }

                    }
                }
                if (CheckphoneRequired) {
                    if (et_phonenumber.getText().toString().equals("")) {
                        if (et_phonenumber.getText().toString().equals("")) {
                            et_phonenumber.setError("!");
                            et_phonenumber.requestFocus();
                            check = false;
                        }

                    }
                }
                if (ChecktextArea_informationRequired) {
                    if (textArea_information.getText().toString().equals("")) {
                        if (textArea_information.getText().toString().equals("")) {
                            textArea_information.setError("!");
                            textArea_information.requestFocus();
                            check = false;
                        }

                    }
                }


                if (check) {
                    Log.d("shitttttttttttttt", email);
                    Log.d("shitttttttttttttt45678", name);
                    Log.d("shitttttttttttttte", phonenumber);
                    Log.d("shitttttttttttttt65", message);

                    adforest_sendMessageSeller(name, email, phoneNumber, message);
                    dialog.dismiss();
                }

            }
        });

        Cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialog.dismiss();
            }
        });
        dialog.show();
    }

    public static long countDownStart(JSONArray timerArray) {

        String sday = null, shours = null, smintues = null, ssconds = null;
        try {
            sday = timerArray.getString(0);
            shours = timerArray.getString(1);
            smintues = timerArray.getString(2);
            ssconds = timerArray.getString(3);


        } catch (JSONException e) {
            e.printStackTrace();
        }

//                long time4 = (long)12*24*60*60*1000;
        String defaultDate = "1970-01-01 00:00:00";  // Start date
        SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
//        TimeZone tz = TimeZone.getDefault();
//        Log.d("info date   ", "" + tz.getDisplayName(false, TimeZone.SHORT) + " Timezon id :: " + tz.getID());
        TimeZone timeZone = TimeZone.getTimeZone("UTC");
        simpleDateFormat.setTimeZone(timeZone);
        Calendar calendar = Calendar.getInstance();
        try {
            calendar.setTime(simpleDateFormat.parse(defaultDate));
        } catch (ParseException e) {
            e.printStackTrace();
        }
        calendar.add(Calendar.DATE, Integer.parseInt(sday));
        calendar.add(Calendar.MONTH, 0);
        calendar.add(Calendar.HOUR_OF_DAY, Integer.parseInt(shours));
        calendar.add(Calendar.MINUTE, Integer.parseInt(smintues));
        calendar.add(Calendar.SECOND, Integer.parseInt(ssconds));

        String output = simpleDateFormat.format(calendar.getTime());
        simpleDateFormat.setLenient(false);
        Date oldDate = null;
        try {
            oldDate = simpleDateFormat.parse(output);
        } catch (ParseException e) {
            e.printStackTrace();
        }

//                    txtViewDays.setText(sday);
//                    txtViewHours.setText(shours);
//                    txtViewMin.setText(smintues);
//                    txtViwsSec.setText(ssconds);
        Date finalOldDate = oldDate;
        String finalSday = sday;
        String finalShours = shours;
        String finalSmintues = smintues;
        String finalSsconds = ssconds;

//                handler.postDelayed(con, 1000);
        timer = finalOldDate.getTime();
        timerDays = finalSday;
        timerHours = finalShours;
        timerMin = finalSmintues;
        timerSecs = finalSsconds;

        Log.d("qwqwqwqwqwqwqw", timerDays + ":" + timerHours + ":" + timerMin + ":" + timerSecs);


        return timer;
    }

    public void onStop() {
        super.onStop();
        handler.removeCallbacks(runnable);
    }


    @SuppressLint("LongLogTag")
    public void adforest_sendMessageSeller(String name, String email, String phoneNumber, String message) {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("ad_id", myId);
            params.addProperty(nameKey, name);
            params.addProperty(emailKey, email);
            params.addProperty(phonenumberKey, phoneNumber);
            params.addProperty(messageKey, message);
            Log.d("parSendSellerMsg", params.toString());

            Call<ResponseBody> myCall = restService.postSendSellerMessageFromAdDetail(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info sendSeller Resp", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {


                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                            shimmerFrameLayout.stopShimmer();
                            shimmerFrameLayout.setVisibility(View.GONE);
                            loadingLayout.setVisibility(View.GONE);
                        }
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
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    Log.d("info sendMeassage error", String.valueOf(t));
                    Log.d("info sendMeassage error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }
    }


}