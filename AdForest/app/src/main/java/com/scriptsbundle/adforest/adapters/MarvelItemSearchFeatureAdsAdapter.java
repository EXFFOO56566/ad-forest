package com.scriptsbundle.adforest.adapters;

import android.content.Context;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.os.Build;
import android.text.TextUtils;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.ScaleAnimation;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import cn.iwgang.countdownview.CountdownView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.CatSubCatOnclicklinstener;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.utills.AdsTimerConvert;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class MarvelItemSearchFeatureAdsAdapter extends RecyclerView.Adapter<MarvelItemSearchFeatureAdsAdapter.CustomViewHolder> {

    private SettingsMain settingsMain;
    private ArrayList<catSubCatlistModel> list;
    private CatSubCatOnclicklinstener oNItemClickListener;
    private Context mContext;
    private boolean isMultiLine = false;
    private String horizontelAd;
    private String featuredAdsLayout;

    public boolean isMultiLine() {
        return isMultiLine;
    }

    public void setMultiLine(boolean multiLine) {
        isMultiLine = multiLine;
    }

    public void setHorizontelAd(String isHorizontelAd) {
        horizontelAd = isHorizontelAd;
    }

    public MarvelItemSearchFeatureAdsAdapter(Context context, ArrayList<catSubCatlistModel> feedItemList) {
        this.list = feedItemList;
        this.mContext = context;
    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        settingsMain = new SettingsMain(mContext);
        featuredAdsLayout = settingsMain.getfeaturedAdsLayout();

        View view = null;
        if (featuredAdsLayout.equals("default")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.marvel_featured_ads_default, viewGroup, false);
        } else if (featuredAdsLayout.equals("horizental")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.marvel_featured_ads_horizontal, viewGroup, false);
        } else if (featuredAdsLayout.equals("vertical")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.marvel_featured_ads_vertical, viewGroup, false);
        }
        return new MarvelItemSearchFeatureAdsAdapter.CustomViewHolder(view);
    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, final int position) {

        final catSubCatlistModel feedItem = list.get(position);

        holder.titleTextView.setText(list.get(position).getCardName());
        if (featuredAdsLayout.equals("default")) {
            holder.catNameTV.setText(list.get(position).getCatName());
            holder.catNameTV.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
            holder.priceTV.setText(list.get(position).getPrice());
            holder.priceTV.setTextColor(Color.parseColor(SettingsMain.getMainColor()));

            holder.priceTypeTV.setText(list.get(position).getPriceType());
            holder.locationTV.setText(list.get(position).getLocation());
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                holder.locationTV.setCompoundDrawableTintList(ColorStateList.valueOf(Color.parseColor(SettingsMain.getMainColor())));
            }
        }
        holder.locationTV.setText(list.get(position).getLocation());
        holder.priceTV.setText(list.get(position).getPrice());
        holder.priceTV.setTextColor(Color.parseColor(SettingsMain.getMainColor()));

        setScaleAnimation(holder.itemView);
        if (feedItem.isIs_show_countDown()) {
            holder.cv_countdownView.setVisibility(View.VISIBLE);
            holder.cv_countdownView.start(AdsTimerConvert.adforest_bidTimer(feedItem.getTimer_array()));
        } else {
            holder.cv_countdownView.setVisibility(View.GONE);
        }

        if (!TextUtils.isEmpty(feedItem.getImageResourceId())) {
            Picasso.get().load(feedItem.getImageResourceId())
                    .resize(250, 250).centerCrop()
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(holder.mainImage);
        }

        View.OnClickListener listener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                oNItemClickListener.onItemClick(feedItem);
            }
        };

        if (featuredAdsLayout.equals("horizental")) {
            holder.linearLayoutMain1.setOnClickListener(listener);
        } else {
            holder.linearLayoutMain.setOnClickListener(listener);
        }
    }

    private final static int FADE_DURATION = 1000; //FADE_DURATION in milliseconds

    private void setScaleAnimation(View view) {
        ScaleAnimation anim = new ScaleAnimation(0.0f, 1.0f, 0.0f, 1.0f, Animation.RELATIVE_TO_SELF, 0.5f, Animation.RELATIVE_TO_SELF, 0.5f);
        anim.setDuration(FADE_DURATION);
        view.startAnimation(anim);
    }

    @Override
    public int getItemCount() {
        return (null != list ? list.size() : 0);
    }

    public CatSubCatOnclicklinstener getOnItemClickListener() {
        return oNItemClickListener;
    }

    public void setOnItemClickListener(CatSubCatOnclicklinstener onItemClickListener) {
        this.oNItemClickListener = onItemClickListener;
    }

    class CustomViewHolder extends RecyclerView.ViewHolder {
        CountdownView cv_countdownView;
        private TextView titleTextView, priceTV, locationTV, featureText, priceTypeTV, catNameTV;
        private ImageView mainImage, featuredImg;
        private RelativeLayout linearLayoutMain;
        private LinearLayout linearLayoutMain1;

        CustomViewHolder(View v) {
            super(v);

            titleTextView = v.findViewById(R.id.text_view_name);
            priceTV = v.findViewById(R.id.prices);
            locationTV = v.findViewById(R.id.location);
            mainImage = v.findViewById(R.id.image_view);
            cv_countdownView = v.findViewById(R.id.cv_countdownView);
            priceTypeTV = v.findViewById(R.id.text_view_priceType);
            catNameTV = v.findViewById(R.id.text_view_cat_name);
            priceTV.setTextColor(Color.parseColor(settingsMain.getMainColor()));
            if (featuredAdsLayout.equals("horizental")) {
                linearLayoutMain1 = v.findViewById(R.id.linear_layout_card_view);
            } else {
                linearLayoutMain = v.findViewById(R.id.linear_layout_card_view);
            }
//            if (horizontelAd.equals("default")) {
            featuredImg = v.findViewById(R.id.textView4);
            /*} else {
                featureText = v.findViewById(R.id.textView4);

//            */
        }
//            } else {
//
//                linearLayoutMain = v.findViewById(R.id.linear_layout_card_view);
//            }
//        }
    }
}