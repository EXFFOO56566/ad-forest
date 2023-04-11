package com.scriptsbundle.adforest.home.adapter;

import android.content.Context;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.RecyclerView;

import com.squareup.picasso.Picasso;
import com.yarolegovich.discretescrollview.DSVOrientation;
import com.yarolegovich.discretescrollview.DiscreteScrollLayoutManager;

import java.util.ArrayList;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.Search.FragmentCatSubNSearch;
import com.scriptsbundle.adforest.helper.OnItemClickListener;
import com.scriptsbundle.adforest.home.helper.CustomLocationFilter;
import com.scriptsbundle.adforest.home.helper.MultiCustomLocationFilter;
import com.scriptsbundle.adforest.modelsList.homeCatListModel;
import com.scriptsbundle.adforest.profile.FragmentProfile;
import com.scriptsbundle.adforest.utills.RoundedTransformation;
import com.scriptsbundle.adforest.utills.SettingsMain;

import static android.view.Gravity.CENTER;

/**
 * Created by taimu on 2/6/2018.
 */

public class MultiItemMainAllLocationAds extends RecyclerView.Adapter<MultiItemMainAllLocationAds.MyViewHolder> implements Filterable {
    public ArrayList<homeCatListModel> list;
    MultiCustomLocationFilter filter;
    private ArrayList<homeCatListModel> feedItemListFiltered;
    private OnItemClickListener onItemClickListener;
    private Context mContext;
    private SettingsMain settingsMain;
    private int noOfCol;
    DiscreteScrollLayoutManager layoutManager;
    private static final int CENTER = Integer.MAX_VALUE / 2;

    public MultiItemMainAllLocationAds(Context context, ArrayList<homeCatListModel> Data, int noOfCol) {
        this.list = Data;
        this.feedItemListFiltered = Data;
        this.mContext = context;
        this.noOfCol = noOfCol;
        settingsMain = new SettingsMain(context);
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.multi_itemof_location_ads, parent, false);

        return new MyViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final MyViewHolder holder, final int position) {
        layoutManager = new DiscreteScrollLayoutManager(mContext, null, DSVOrientation.HORIZONTAL);
//        (DiscreteScrollLayoutManager) recyclerView.getLayoutManager();
        int resetPosition = CENTER + mapPositionToReal(layoutManager.getCurrentPosition());
        setPosition(resetPosition);
        //                    .transform(new RoundedTransformation(10, 0))
        resetPosition = position;
        final homeCatListModel feedItem = list.get(resetPosition);
        if (!TextUtils.isEmpty(feedItem.getThumbnail())) {
              Picasso.get().load(feedItem.getThumbnail())
                    .resize(270, 270)
                    .error(R.drawable.placeholder)
                    .placeholder(R.drawable.placeholder)
                    .into(holder.imageView);
            holder.textViewTitle.setText(feedItem.getTitle());
            holder.textViewAllAds.setText(feedItem.getAdsCount());

        }
        holder.relativeLayout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                AppCompatActivity activity = (AppCompatActivity) v.getContext();
                FragmentCatSubNSearch fragment_search = new FragmentCatSubNSearch();
                Bundle bundle = new Bundle();
                bundle.putString("ad_country", feedItem.getId());
                fragment_search.setArguments(bundle);
                FragmentTransaction fragmentTransaction = activity.getSupportFragmentManager().beginTransaction();
                fragmentTransaction.replace(R.id.frameContainer, fragment_search,"FragmentCatSubNSearch").addToBackStack(null).commit();
            }
        });
    }



    @Override
    public int getItemCount() {
        return list.size();
    }

    private void setPosition(int position) {
        layoutManager.scrollToPosition(position);
    }


    private int mapPositionToReal(int position) {
        if (position < CENTER) {
            int rem = (CENTER - position) % getItemCount();
            return rem == 0 ? 0 : getItemCount() - rem;
        } else {
            return (position - CENTER) % getItemCount();
        }
    }

    @Override
    public Filter getFilter() {
        if (filter == null) {
            filter = new MultiCustomLocationFilter(feedItemListFiltered, this);
        }
        return filter;
    }

    public void setOnItemClickListener(OnItemClickListener onItemClickListener) {
        this.onItemClickListener = onItemClickListener;
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {

        TextView textViewTitle, textViewAllAds;
        ImageView imageView, adsLocation;
        CardView relativeLayout;

        MyViewHolder(View v) {
            super(v);

            textViewTitle = v.findViewById(R.id.tv_locationName);
            textViewAllAds = v.findViewById(R.id.tv_locationAdsCount);
            imageView = v.findViewById(R.id.imageViewLocation);
            relativeLayout = v.findViewById(R.id.locationLayout);


        }
    }

}

