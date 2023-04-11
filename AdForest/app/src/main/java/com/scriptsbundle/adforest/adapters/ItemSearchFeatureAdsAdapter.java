package com.scriptsbundle.adforest.adapters;

import android.content.Context;
import android.graphics.Color;

import androidx.recyclerview.widget.RecyclerView;

import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.ScaleAnimation;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import cn.iwgang.countdownview.CountdownView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.CatSubCatOnclicklinstener;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.utills.AdsTimerConvert;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class ItemSearchFeatureAdsAdapter extends RecyclerView.Adapter<ItemSearchFeatureAdsAdapter.CustomViewHolder> {

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

    public ItemSearchFeatureAdsAdapter(Context context, ArrayList<catSubCatlistModel> feedItemList) {
        this.list = feedItemList;
        this.mContext = context;
    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
//        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.itemof_feature_search, null);
//        settingsMain = new SettingsMain(mContext);
//        return new CustomViewHolder(view);
//        horizontelAd = settingsMain.gethorizontal();

        View view = null;
        settingsMain = new SettingsMain(mContext);
        if (horizontelAd.equals("horizental"))
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_main_home_related_vertical, viewGroup, false);
        else if (horizontelAd.equals("default"))
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.itemof_feature_search, viewGroup, false);
        else if (horizontelAd.equals("vertical")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_main_home_feature_verticall_ads, viewGroup, false);
        }
        return new ItemSearchFeatureAdsAdapter.CustomViewHolder(view);
    }
//    @Override
////    public int getItemViewType(int position) {
////        return position;
////    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, final int position) {

        final catSubCatlistModel feedItem = list.get(position);

        holder.titleTextView.setText(list.get(position).getCardName());
        holder.priceTV.setText(list.get(position).getPrice());
        holder.locationTV.setText(list.get(position).getLocation());
//        if(isMultiLine){
//            holder.featureText.setVisibility(View.GONE);
//        }
//        else{
        holder.featureText.setVisibility(View.VISIBLE);
        holder.featureText.setText(list.get(position).getAddTypeFeature());
//        }
        holder.featureText.setBackgroundColor(Color.parseColor("#E52D27"));
//        setScaleAnimation(holder.itemView);
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
//        holder.saveTV.setText(list.get(position).getFavBtnText());
//        if (list.get(position).getIsfav() == 1) {
//            holder.saveTV.setTextColor(Color.parseColor( settingsMain.getMainColor()));
//
//            Drawable drawableCardViewMessage = DrawableColorChanger.changeDrawableColor(mContext,
//                    R.drawable.ic_favorite_border, Color.parseColor(settingsMain.getMainColor()));
//            holder.saveTV.setCompoundDrawablesWithIntrinsicBounds(drawableCardViewMessage, null, null, null);
//        } else {
//            holder.saveTV.setTextColor(mContext.getResources().getColor(R.color.white_greyish));
//        }

//        View.OnClickListener listener1 = new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                oNItemClickListener.addToFavClick(v, list.get(position).getId());
//            }
//        };
//        holder.saveTV.setOnClickListener(listener1);


        View.OnClickListener listener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                oNItemClickListener.onItemClick(feedItem);
            }
        };
//        View.OnTouchListener onTouchListener = new View.OnTouchListener() {
//            @Override
//            public boolean onTouch(View v, MotionEvent event) {
//                if (event.getAction() == MotionEvent.ACTION_UP) {
//                    oNItemClickListener.onItemTouch(feedItem);
//                }
//                return true;
//            }
//        };
        if (horizontelAd.equals("horizental")) {
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
        private TextView titleTextView, priceTV, locationTV, featureText;
        private ImageView mainImage;
        private RelativeLayout linearLayoutMain;
        private LinearLayout linearLayoutMain1;

        CustomViewHolder(View v) {
            super(v);

            titleTextView = v.findViewById(R.id.text_view_name);
            featureText = v.findViewById(R.id.textView4);
            priceTV = v.findViewById(R.id.prices);
            locationTV = v.findViewById(R.id.location);
            mainImage = v.findViewById(R.id.image_view);
            cv_countdownView = v.findViewById(R.id.cv_countdownView);

            priceTV.setTextColor(Color.parseColor(settingsMain.getMainColor()));
            if (horizontelAd.equals("horizental")) {
                linearLayoutMain1 = v.findViewById(R.id.linear_layout_card_view);
            } else {

                linearLayoutMain = v.findViewById(R.id.linear_layout_card_view);
            }
        }
    }
}