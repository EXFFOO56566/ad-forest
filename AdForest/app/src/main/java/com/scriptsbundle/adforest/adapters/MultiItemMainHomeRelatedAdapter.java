package com.scriptsbundle.adforest.adapters;

import android.content.Context;
import android.graphics.Color;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.view.animation.ScaleAnimation;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import cn.iwgang.countdownview.CountdownView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.OnItemClickListener2;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.utills.AdsTimerConvert;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class MultiItemMainHomeRelatedAdapter extends RecyclerView.Adapter<MultiItemMainHomeRelatedAdapter.MyViewHolder> {

    SettingsMain settingsMain;
    Context context;
    private ArrayList<catSubCatlistModel> list;
    private OnItemClickListener2 onItemClickListener;
    private boolean isMultiLine = false;
    private String horizontelAd;
    private String latestAd;
    private String sliderAds;
    private String nearByAds;
    public boolean fromSlider = false;
    public boolean fromNearBy = false;
    public String checkAdsType;

    public boolean isMultiLine() {
        return isMultiLine;
    }

    public void setMultiLine(boolean multiLine) {
        isMultiLine = multiLine;
    }

    public void setHorizontelAd(String isHorizontelAd) {
        horizontelAd = isHorizontelAd;
    }

    public MultiItemMainHomeRelatedAdapter(Context context, ArrayList<catSubCatlistModel> Data) {
        this.list = Data;
        settingsMain = new SettingsMain(context);
        this.context = context;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

//        View view = LayoutInflater.from(parent.getContext())
//                .inflate(R.layout.item_main_home_related, parent, false);
//        return new MyViewHolder(view);
//        horizontelAd = settingsMain.getlatestAdsLayout();
        latestAd = settingsMain.getlatestAdsLayout();
        sliderAds = settingsMain.getSliderAdsLayout();
        nearByAds = settingsMain.getnearbyAdsLayout();
        //settingsMain.gethorizontal();
        View itemView = null;
        if (!fromSlider) {
            if (latestAd.equals("default")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_item_main_home_related, parent, false);
            } else if (latestAd.equals("horizental")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_latest_ads_horizontal, parent, false);
            } else if (latestAd.equals("vertical")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_itemof_latest_related_vertical, parent, false);

            }
        }
        if (fromNearBy) {
            if (nearByAds.equals("default")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_item_main_home_related, parent, false);
            } else if (nearByAds.equals("horizental")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_latest_ads_horizontal, parent, false);
            } else if (nearByAds.equals("vertical")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_itemof_latest_related_vertical, parent, false);

            }
        }
        if (fromSlider) {
            if (sliderAds.equals("default")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_item_main_home_related, parent, false);
            } else if (sliderAds.equals("horizental")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_latest_ads_horizontal, parent, false);
            } else if (sliderAds.equals("vertical")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.multi_itemof_latest_related_vertical, parent, false);

            }
        }
        //
//        itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.marvel_latest_ads_horizontal, parent, false);
        return new MultiItemMainHomeRelatedAdapter.MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {
        final catSubCatlistModel feedItem = list.get(position);

        holder.titleTextView.setText(list.get(position).getCardName());

        holder.dateTV.setText(list.get(position).getDate());


        holder.priceTV.setText(list.get(position).getPrice());
        holder.locationTV.setText(list.get(position).getLocation());
        if (list.get(position).getFeaturetype()) {
            holder.featureText.setVisibility(View.VISIBLE);
            holder.featureText.setText(list.get(position).getAddTypeFeature());
            holder.featureText.setBackgroundColor(Color.parseColor("#E52D27"));
        }
        if (list.get(position).isIs_show_countDown()) {
            holder.cv_countdownView.setVisibility(View.VISIBLE);
            holder.cv_countdownView.start(AdsTimerConvert.adforest_bidTimer(list.get(position).getTimer_array()));
        } else {
            holder.cv_countdownView.setVisibility(View.GONE);
        }
        setFadeAnimation(holder.cardView);
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
                onItemClickListener.onItemClick(feedItem);
            }
        };

//        if (latestAd.equals("horizental")) {
//            holder.linearLayout1.setOnClickListener(listener);
//        } else {
//            holder.linearLayout.setOnClickListener(listener);
//        }
        if (checkAdsType.equals("nearby")) {
            if (fromNearBy) {
                if (nearByAds.equals("horizental")) {
                    holder.linearLayout1.setOnClickListener(listener);

                } else {
                    holder.linearLayout.setOnClickListener(listener);
                }
            }
        } else if (checkAdsType.equals("latest")) {
            if (!fromSlider) {
                if (latestAd.equals("horizental")) {
                    holder.linearLayout1.setOnClickListener(listener);
                } else {
                    holder.linearLayout.setOnClickListener(listener);
                }
            } else {
                holder.linearLayout1.setOnClickListener(listener);
            }
        } else if (checkAdsType.equals("slider")) {
            if (fromSlider) {
                if (sliderAds.equals("horizental")) {
                    holder.linearLayout1.setOnClickListener(listener);
                } else {
                    holder.linearLayout.setOnClickListener(listener);
                }
            } else {
                holder.linearLayout1.setOnClickListener(listener);
            }
        }
    }

    private final static int FADE_DURATION = 1000; //FADE_DURATION in milliseconds

    private void setFadeAnimation(View view) {
        AlphaAnimation anim = new AlphaAnimation(0.0f, 1.0f);
//        Animation anim = android.view.animation.AnimationUtils.loadAnimation(getApplicationContext(),R.anim.alpha);
        anim.setDuration(FADE_DURATION);
        view.startAnimation(anim);
    }

    private void setScaleAnimation(View view) {
        ScaleAnimation anim = new ScaleAnimation(0.0f, 1.0f, 0.0f, 1.0f, Animation.RELATIVE_TO_SELF, 0.5f, Animation.RELATIVE_TO_SELF, 0.5f);
        anim.setDuration(FADE_DURATION);
        view.startAnimation(anim);
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public void setOnItemClickListener(OnItemClickListener2 onItemClickListener) {
        this.onItemClickListener = onItemClickListener;
    }

    class MyViewHolder extends RecyclerView.ViewHolder {
        TextView titleTextView, dateTV, priceTV, locationTV, featureText;
        ImageView mainImage;
        LinearLayout linearLayout1;
        RelativeLayout linearLayout;
        CountdownView cv_countdownView;
        CardView cardView;

        MyViewHolder(View v) {
            super(v);
            cardView = v.findViewById(R.id.card_view);
            titleTextView = v.findViewById(R.id.text_view_name);
            dateTV = v.findViewById(R.id.date);
            dateTV.setVisibility(View.GONE);
            priceTV = v.findViewById(R.id.prices);
            locationTV = v.findViewById(R.id.location);
            priceTV.setTextColor(Color.parseColor(settingsMain.getMainColor()));
            mainImage = v.findViewById(R.id.image_view);
            cv_countdownView = v.findViewById(R.id.cv_countdownView);
//            linearLayout = v.findViewById(R.id.linear_layout_card_view);
            featureText = v.findViewById(R.id.textView4);
//            if (latestAd.equals("horizental")) {
//                linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
//            } else {
//
//                linearLayout = v.findViewById(R.id.linear_layout_card_view);
//            }
            if (checkAdsType.equals("nearby")) {

                if (fromNearBy) {
                    if (nearByAds.equals("horizental")) {
                        linearLayout1 = v.findViewById(R.id.linear_layout_card_view);

                    } else {
                        linearLayout = v.findViewById(R.id.linear_layout_card_view);

                    }
                }
            }
            if (checkAdsType.equals("latest")) {

                if (!fromSlider) {
                    if (latestAd.equals("horizental")) {
                        linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
                    } else {
                        linearLayout = v.findViewById(R.id.linear_layout_card_view);
                    }
                } else {
                    linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
                }
            }
            if (checkAdsType.equals("slider")) {
                if (sliderAds.equals("horizental")) {
                    linearLayout1 = v.findViewById(R.id.linear_layout_card_view);

                } else {
                    linearLayout = v.findViewById(R.id.linear_layout_card_view);

                }
            }
        }
    }
}
