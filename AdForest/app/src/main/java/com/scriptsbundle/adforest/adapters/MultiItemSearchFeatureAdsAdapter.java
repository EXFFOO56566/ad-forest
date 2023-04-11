package com.scriptsbundle.adforest.adapters;

import android.content.Context;
import android.graphics.Color;
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

import androidx.recyclerview.widget.RecyclerView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import cn.iwgang.countdownview.CountdownView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.CatSubCatOnclicklinstener;
import com.scriptsbundle.adforest.modelsList.catSubCatlistModel;
import com.scriptsbundle.adforest.utills.AdsTimerConvert;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class MultiItemSearchFeatureAdsAdapter extends RecyclerView.Adapter<MultiItemSearchFeatureAdsAdapter.CustomViewHolder> {

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

    public MultiItemSearchFeatureAdsAdapter(Context context, ArrayList<catSubCatlistModel> feedItemList) {
        this.list = feedItemList;
        this.mContext = context;
    }

    @Override
    public CustomViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        //        View view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.itemof_feature_search, null);
        settingsMain = new SettingsMain(mContext);
        featuredAdsLayout = settingsMain.getfeaturedAdsLayout();

        View view = null;
        if (featuredAdsLayout.equals("horizental")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.multi_itemof_feature_search, viewGroup, false);
        } else if (featuredAdsLayout.equals("default")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.multi_itemof_feature_search_default, viewGroup, false);
        } else if (featuredAdsLayout.equals("vertical")) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.multi_itemof_feature_search_vertical, viewGroup, false);
        }
        return new MultiItemSearchFeatureAdsAdapter.CustomViewHolder(view);
    }

    @Override
    public void onBindViewHolder(CustomViewHolder holder, final int position) {

        final catSubCatlistModel feedItem = list.get(position);

        holder.titleTextView.setText(list.get(position).getCardName());
        holder.priceTV.setText(list.get(position).getPrice());
        holder.locationTV.setText(list.get(position).getLocation());

        holder.featureText.setVisibility(View.VISIBLE);
        setScaleAnimation(holder.itemView);
        if (feedItem.isIs_show_countDown()) {
            holder.cv_countdownView.setVisibility(View.VISIBLE);
            holder.cv_countdownView.start(AdsTimerConvert.adforest_bidTimer(feedItem.getTimer_array()));
        } else {
            holder.cv_countdownView.setVisibility(View.GONE);
        }
        if (settingsMain.getRTL()) {
        holder.featureText.setImageResource(R.drawable.stars_left_rtl);
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
        if (featuredAdsLayout.equals("horizental")) {
            holder.linearLayoutMain1.setOnClickListener(listener);
        } else {
            holder.linearLayoutMain.setOnClickListener(listener);
        }
    }

    private final static int FADE_DURATION = 500; //FADE_DURATION in milliseconds

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
        private TextView titleTextView, priceTV, locationTV;
        private ImageView mainImage, featureText;
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
            if (featuredAdsLayout.equals("horizental")) {
                linearLayoutMain1 = v.findViewById(R.id.linear_layout_card_view);
            } else {

                linearLayoutMain = v.findViewById(R.id.linear_layout_card_view);
            }
        }
    }
}