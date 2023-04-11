package com.scriptsbundle.adforest.home.adapter;

import android.content.Context;
import android.graphics.Color;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AccelerateInterpolator;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.view.animation.AnimationSet;
import android.view.animation.DecelerateInterpolator;
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

import static com.scriptsbundle.adforest.utills.SettingsMain.getMainColor;

public class MultiItemMainAllCatAdapter extends RecyclerView.Adapter<MultiItemMainAllCatAdapter.CustomViewHolder> {

    private ArrayList<homeCatListModel> feedItemList;
    private Context mContext;
    private OnItemClickListener onItemClickListener;
    private int noOfCol;


    public MultiItemMainAllCatAdapter(Context context, ArrayList<homeCatListModel> feedItemList, int noOFCol) {
        this.feedItemList = feedItemList;
        this.mContext = context;
        this.noOfCol = noOFCol;
    }

    @NonNull
    @Override
    public MultiItemMainAllCatAdapter.CustomViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.multi_item_main_all_cat, parent, false);
        return new MultiItemMainAllCatAdapter.CustomViewHolder(view);
    }

    @Override
    public void onBindViewHolder(MultiItemMainAllCatAdapter.CustomViewHolder customViewHolder, int i) {

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
        Animation fadeIn = new AlphaAnimation(0, 1);
        fadeIn.setInterpolator(new DecelerateInterpolator()); //add this
        fadeIn.setDuration(1000);

        Animation fadeOut = new AlphaAnimation(1, 0);
        fadeOut.setInterpolator(new AccelerateInterpolator()); //and this
        fadeOut.setStartOffset(1000);
        fadeOut.setDuration(1000);

        AnimationSet animation = new AnimationSet(false); //change to false
        animation.addAnimation(fadeIn);
        animation.addAnimation(fadeOut);
        customViewHolder.cardView.setAnimation(animation);

//        setFadeAnimation(customViewHolder.cardView);
        //Setting text view title
//create a new gradient color
//            GradientDrawable gd = new GradientDrawable(
//                    GradientDrawable.Orientation.LEFT_RIGHT, customViewHolder.colors);
//            gd.setCornerRadius(0f);
//
//            customViewHolder.imageView2.setBackground(gd);
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
        CircleImageView circleImageView;
        CardView cardView;
//        CircularImageView circleImageView;
        public int[] colors = {Color.parseColor(getMainColor()), Color.parseColor(getMainColor())};

        CustomViewHolder(View view) {
            super(view);
            this.cardView = view.findViewById(R.id.card_items_cat);
            this.imageView = view.findViewById(R.id.thumbnail);
            this.textView = view.findViewById(R.id.title);
//            this.circleImageView = view.findViewById(R.id.thumbnail);
//            this.imageView2 = view.findViewById(R.id.imageView2);


        }
    }
}
