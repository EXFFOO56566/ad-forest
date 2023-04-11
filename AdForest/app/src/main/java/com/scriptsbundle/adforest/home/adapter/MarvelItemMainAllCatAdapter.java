package com.scriptsbundle.adforest.home.adapter;

import android.content.Context;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.graphics.drawable.GradientDrawable;
import android.text.TextUtils;
import android.util.TypedValue;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.view.animation.ScaleAnimation;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import de.hdodenhof.circleimageview.CircleImageView;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.OnItemClickListener;
import com.scriptsbundle.adforest.modelsList.homeCatListModel;
import com.scriptsbundle.adforest.utills.CircleTransform;
import com.scriptsbundle.adforest.utills.SettingsMain;

import static com.scriptsbundle.adforest.utills.SettingsMain.getMainColor;

public class MarvelItemMainAllCatAdapter extends RecyclerView.Adapter<MarvelItemMainAllCatAdapter.CustomViewHolder> {

    private ArrayList<homeCatListModel> feedItemList;
    private Context mContext;
    private OnItemClickListener onItemClickListener;
    private int noOfCol;


    public MarvelItemMainAllCatAdapter(Context context, ArrayList<homeCatListModel> feedItemList, int noOFCol) {
        this.feedItemList = feedItemList;
        this.mContext = context;
        this.noOfCol = noOFCol;
    }

    @NonNull
    @Override
    public MarvelItemMainAllCatAdapter.CustomViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.marvel_item_main_all_cat, parent, false);
        return new MarvelItemMainAllCatAdapter.CustomViewHolder(view);
    }

    @Override
    public void onBindViewHolder(MarvelItemMainAllCatAdapter.CustomViewHolder customViewHolder, int i) {

//        int dimensionInPixel = 90;
//
//        if (noOfCol == 2) {
//            dimensionInPixel = 110;
//        }
//        if (noOfCol == 3) {
//            dimensionInPixel = 90;
//        }
//        if (noOfCol == 4) {
//            dimensionInPixel = 70;
//        }
//
//        customViewHolder.imageView.getLayoutParams().height = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, dimensionInPixel, mContext.getResources().getDisplayMetrics());

        final homeCatListModel feedItem = feedItemList.get(i);

        //Download image using picasso library
        if (!TextUtils.isEmpty(feedItem.getThumbnail())) {
              Picasso.get().load(feedItem.getThumbnail())
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(customViewHolder.imageView);
        }
        setScaleAnimation(customViewHolder.imageView);
             customViewHolder.textView.setText(feedItem.getTitle());
        View.OnClickListener listener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onItemClickListener.onItemClick(feedItem);
            }
        };
        customViewHolder.imageView.setOnClickListener(listener);
        customViewHolder.textView.setOnClickListener(listener);
    }

    private final static int FADE_DURATION = 500; //FADE_DURATION in milliseconds

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
        return feedItemList.size();
    }

    public void setOnItemClickListener(OnItemClickListener onItemClickListener) {
        this.onItemClickListener = onItemClickListener;
    }

    class CustomViewHolder extends RecyclerView.ViewHolder {
        ImageView imageView, imageView2;
        TextView textView;
        CardView cardView, cardViewROunded;
        RelativeLayout containerView;
        public int[] colors = {Color.parseColor(getMainColor()), Color.parseColor(getMainColor())};

        CustomViewHolder(View view) {
            super(view);
            this.cardViewROunded = view.findViewById(R.id.roundCardView);
            this.cardView = view.findViewById(R.id.card_items_cat);
            this.imageView = view.findViewById(R.id.thumbnail);
            this.textView = view.findViewById(R.id.title);
            this.containerView = view.findViewById(R.id.container);
//            this.containerView.setBackgroundColor(Color.parseColor("#77ffffff"));


        }
    }
}
