package com.scriptsbundle.adforest.adapters;

import android.content.Context;
import android.graphics.Color;

import androidx.recyclerview.widget.RecyclerView;

import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import cn.iwgang.countdownview.CountdownView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.OnItemClickListener2;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.utills.AdsTimerConvert;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class ItemMainHomeRelatedAdapter extends RecyclerView.Adapter<ItemMainHomeRelatedAdapter.MyViewHolder> {
    SettingsMain settingsMain;
    Context context;
    private ArrayList<catSubCatlistModel> list;
    private OnItemClickListener2 onItemClickListener;
    private boolean isMultiLine = false;
    private String horizontelAd;
    public boolean calledFromAdDetail = false;
    public boolean isMultiLine() {
        return isMultiLine;
    }

    public void setMultiLine(boolean multiLine) {
        isMultiLine = multiLine;
    }
    public void getCalledFromAdDetail(boolean calledFromAdDetail){
        this.calledFromAdDetail = calledFromAdDetail;
    }

    public void setHorizontelAd(String isHorizontelAd) {
        horizontelAd = isHorizontelAd;
    }

    public ItemMainHomeRelatedAdapter(Context context, ArrayList<catSubCatlistModel> Data) {
        this.list = Data;
        settingsMain = new SettingsMain(context);
        this.context = context;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        horizontelAd = settingsMain.gethorizontal();
        View itemView = null;
        //

        if(calledFromAdDetail){
            itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
        }else{
            if (horizontelAd.equals("horizental")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_vertical, parent, false);
            } else if ( horizontelAd.equals("default")) {
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
            } else if  (horizontelAd.equals("vertical")){
                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_verticallyads, parent, false);

            }
        }
        return new ItemMainHomeRelatedAdapter.MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {
        final catSubCatlistModel feedItem = list.get(position);

        holder.titleTextView.setText(list.get(position).getCardName());
        if (horizontelAd.equals("horizental")) {
            holder.dateTV.setVisibility(View.GONE);
        } else {
            holder.dateTV.setText(list.get(position).getDate());

        }
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
        if(calledFromAdDetail){
            holder.linearLayout.setOnClickListener(listener);
        }else{
            if (horizontelAd.equals("horizental")) {
                holder.linearLayout1.setOnClickListener(listener);
            } else {
                holder.linearLayout.setOnClickListener(listener);
            }
        }
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
        RelativeLayout linearLayout;
        LinearLayout linearLayout1;
        CountdownView cv_countdownView;

        MyViewHolder(View v) {
            super(v);

            titleTextView = v.findViewById(R.id.text_view_name);
            dateTV = v.findViewById(R.id.date);
            priceTV = v.findViewById(R.id.prices);
            locationTV = v.findViewById(R.id.location);
            priceTV.setTextColor(Color.parseColor(settingsMain.getMainColor()));
            mainImage = v.findViewById(R.id.image_view);
            cv_countdownView = v.findViewById(R.id.cv_countdownView);
            if (calledFromAdDetail){
                linearLayout = v.findViewById(R.id.linear_layout_card_view);
            }else {
                if (horizontelAd.equals("horizental")) {
                    linearLayout1 = v.findViewById(R.id.linear_layout_card_view);

                } else {

                    linearLayout = v.findViewById(R.id.linear_layout_card_view);
                }
            }
            featureText = v.findViewById(R.id.textView4);
        }
    }
}



//    SettingsMain settingsMain;
//    Context context;
//    private ArrayList<catSubCatlistModel> list;
//    private OnItemClickListener2 onItemClickListener;
//    private boolean isMultiLine = false;
//    private String horizontelAd;
//    public boolean calledFromAdDetail = false;
//    private String latestAd;
//    private String sliderAds;
//    private String nearByAds;
//    public boolean fromSlider = false;
//    public boolean fromNearBy = false;
//    public String checkAdsType;
//
//    public boolean isMultiLine() {
//        return isMultiLine;
//    }
//
//    public void setMultiLine(boolean multiLine) {
//        isMultiLine = multiLine;
//    }
//
//    public void setHorizontelAd(String isHorizontelAd) {
//        horizontelAd = isHorizontelAd;
//    }
//
//    public void getCalledFromAdDetail(boolean calledFromAdDetail) {
//        this.calledFromAdDetail = calledFromAdDetail;
//    }
//
//    public ItemMainHomeRelatedAdapter(Context context, ArrayList<catSubCatlistModel> Data) {
//        this.list = Data;
//        settingsMain = new SettingsMain(context);
//        this.context = context;
//    }
//
//    @Override
//    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
//
//        latestAd = settingsMain.getlatestAdsLayout();
//        sliderAds = settingsMain.getSliderAdsLayout();
//        nearByAds = settingsMain.getnearbyAdsLayout();
//        View itemView = null;
//        if (calledFromAdDetail) {
//            itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
//        } else {
//            if (fromNearBy) {
//                if (nearByAds.equals("default")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
//                } else if (nearByAds.equals("horizental")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_vertical, parent, false);
//                } else if (nearByAds.equals("vertical")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_verticallyads, parent, false);
//
//                }
//            }
//            if (!fromSlider) {
//                if (latestAd.equals("default")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
//                } else if (latestAd.equals("horizental")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_vertical, parent, false);
//                } else if (latestAd.equals("vertical")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_verticallyads, parent, false);
//
//                }
//            }
//            if (fromSlider) {
//                if (sliderAds.equals("default")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
//                } else if (sliderAds.equals("horizental")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_vertical, parent, false);
//                } else if (sliderAds.equals("vertical")) {
//                    itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_verticallyads, parent, false);
//
//                }
//            }
//
//
////            if (horizontelAd.equals("horizental")) {
////                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_vertical, parent, false);
////            } else if (horizontelAd.equals("default")) {
////                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related, parent, false);
////            } else if (horizontelAd.equals("vertical")) {
////                itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_main_home_related_verticallyads, parent, false);
////
////            }
//
//
//        }
//        return new ItemMainHomeRelatedAdapter.MyViewHolder(itemView);
//    }
//
//    @Override
//    public void onBindViewHolder(final MyViewHolder holder, final int position) {
//        final catSubCatlistModel feedItem = list.get(position);
//
//        holder.titleTextView.setText(list.get(position).getCardName());
////        } else {
////        if (horizontelAd.equals("horizental")) {
//        holder.dateTV.setVisibility(View.GONE);
//
////            holder.dateTV.setText(list.get(position).getDate());
//
//        holder.priceTV.setText(list.get(position).getPrice());
//        holder.locationTV.setText(list.get(position).getLocation());
//        if (list.get(position).getFeaturetype()) {
//            holder.featureText.setVisibility(View.VISIBLE);
//            holder.featureText.setText(list.get(position).getAddTypeFeature());
//            holder.featureText.setBackgroundColor(Color.parseColor("#E52D27"));
//        }
//        if (list.get(position).isIs_show_countDown()) {
//            holder.cv_countdownView.setVisibility(View.VISIBLE);
//            holder.cv_countdownView.start(AdsTimerConvert.adforest_bidTimer(list.get(position).getTimer_array()));
//        } else {
//            holder.cv_countdownView.setVisibility(View.GONE);
//        }
//
//        if (!TextUtils.isEmpty(feedItem.getImageResourceId())) {
//            Picasso.with(context).load(feedItem.getImageResourceId())
//                    .resize(250, 250).centerCrop()
//                    .error(R.drawable.placeholder)
//                    .placeholder(R.drawable.placeholder)
//                    .into(holder.mainImage);
//        }
//        View.OnClickListener listener = new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                onItemClickListener.onItemClick(feedItem);
//            }
//        };
//        if (calledFromAdDetail) {
//            holder.linearLayout.setOnClickListener(listener);
//        } else {
//            if (checkAdsType.equals("nearby")) {
//                if (fromNearBy) {
//                    if (nearByAds.equals("horizental")) {
//                        holder.linearLayout1.setOnClickListener(listener);
//
//                    } else {
//                        holder.linearLayout.setOnClickListener(listener);
//                    }
//                }
//            } else if (checkAdsType.equals("latest")) {
//                if (!fromSlider) {
//                    if (latestAd.equals("horizental")) {
//                        holder.linearLayout1.setOnClickListener(listener);
//                    } else {
//                        holder.linearLayout.setOnClickListener(listener);
//                    }
//                } else {
//                    holder.linearLayout1.setOnClickListener(listener);
//                }
//            } else if (checkAdsType.equals("slider")) {
//                if (fromSlider) {
//                    if (sliderAds.equals("horizental")) {
//                        holder.linearLayout1.setOnClickListener(listener);
//                    } else {
//                        holder.linearLayout.setOnClickListener(listener);
//                    }
//                } else {
//                    holder.linearLayout1.setOnClickListener(listener);
//                }
//            }
////            if (horizontelAd.equals("horizental")) {
////                holder.linearLayout1.setOnClickListener(listener);
////            } else {
////                holder.linearLayout.setOnClickListener(listener);
////            }
//        }
//
//    }
//
//    @Override
//    public int getItemCount() {
//        return list.size();
//    }
//
//    public void setOnItemClickListener(OnItemClickListener2 onItemClickListener) {
//        this.onItemClickListener = onItemClickListener;
//    }
//
//    class MyViewHolder extends RecyclerView.ViewHolder {
//        TextView titleTextView, dateTV, priceTV, locationTV, featureText;
//        ImageView mainImage;
//        RelativeLayout linearLayout;
//        LinearLayout linearLayout1;
//        CountdownView cv_countdownView;
//
//        MyViewHolder(View v) {
//            super(v);
//
//            titleTextView = v.findViewById(R.id.text_view_name);
//            dateTV = v.findViewById(R.id.date);
//            priceTV = v.findViewById(R.id.prices);
//            locationTV = v.findViewById(R.id.location);
//            priceTV.setTextColor(Color.parseColor(settingsMain.getMainColor()));
//            mainImage = v.findViewById(R.id.image_view);
//            cv_countdownView = v.findViewById(R.id.cv_countdownView);
//            if (calledFromAdDetail) {
//                linearLayout = v.findViewById(R.id.linear_layout_card_view);
//            } else {
//                if (checkAdsType.equals("nearby")) {
//
//                    if (fromNearBy) {
//                        if (nearByAds.equals("horizental")) {
//                            linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
//
//                        } else {
//                            linearLayout = v.findViewById(R.id.linear_layout_card_view);
//
//                        }
//                    }
//                }
//                if (checkAdsType.equals("latest")) {
//
//                    if (!fromSlider) {
//                        if (latestAd.equals("horizental")) {
//                            linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
//                        } else {
//                            linearLayout = v.findViewById(R.id.linear_layout_card_view);
//                        }
//                    } else {
//                        linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
//                    }
//                }
//                if (checkAdsType.equals("slider")) {
//                    if (sliderAds.equals("horizental")) {
//                        linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
//
//                    } else {
//                        linearLayout = v.findViewById(R.id.linear_layout_card_view);
//
//                    }
//                }
////                if (horizontelAd.equals("horizental")) {
////                    linearLayout1 = v.findViewById(R.id.linear_layout_card_view);
////
////                } else {
////
////                    linearLayout = v.findViewById(R.id.linear_layout_card_view);
////                }
//            }
//
//            featureText = v.findViewById(R.id.textView4);
//        }
//    }
//}
