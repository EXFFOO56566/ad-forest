package com.scriptsbundle.adforest.home.adapter;

import android.content.Context;

import androidx.recyclerview.widget.RecyclerView;

import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.OnItemClickListener;
import com.scriptsbundle.adforest.home.helper.CustomCategoriesFilter;
import com.scriptsbundle.adforest.modelsList.homeCatListModel;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class ItemHomeAllCategories extends RecyclerView.Adapter<ItemHomeAllCategories.CustomViewHolder>
        implements Filterable {

    public List<homeCatListModel> feedItemList;
    public ArrayList<homeCatListModel> feedItemListFiltered;
    SettingsMain settingsMain;
    CustomCategoriesFilter filter;
    private Context mContext;
    private OnItemClickListener oNItemClickListener;
    public boolean fromMulti = false;

    public ItemHomeAllCategories(Context context, ArrayList<homeCatListModel> feedItemList) {
        this.feedItemList = feedItemList;
        this.feedItemListFiltered = feedItemList;
        this.mContext = context;
        settingsMain = new SettingsMain(context);

    }

    @Override
    public ItemHomeAllCategories.CustomViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View view = null;
        if (fromMulti) {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.multi_item_viewall_categories, null);
        } else {
            view = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.item_viewall_categories, null);
        }
        return new ItemHomeAllCategories.CustomViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ItemHomeAllCategories.CustomViewHolder customViewHolder, int i) {
        final homeCatListModel feedItem = feedItemList.get(i);
        customViewHolder.tv_cat_value.setText(feedItem.getTitle());

        View.OnClickListener listener = new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                oNItemClickListener.onItemClick(feedItem);
            }
        };

        customViewHolder.view.setOnClickListener(listener);

        if (fromMulti) {
            customViewHolder.tv_cat_count.setText(feedItem.getAdsCount());
            if (!TextUtils.isEmpty(feedItem.getThumbnail())) {
                  Picasso.get().load(feedItem.getThumbnail())
                        .resize(50, 50).centerCrop()
                        .error(R.drawable.placeholder)
                        .placeholder(R.drawable.placeholder)
                        .into(customViewHolder.imageView);
            }
        } else {
            if (settingsMain.getRTL()) {
                customViewHolder.imageView.setBackgroundResource(R.drawable.ic_left_arrow);
            } else
                customViewHolder.imageView.setBackgroundResource(R.drawable.ic_right_arrow_angle);
        }

    }

    @Override
    public int getItemCount() {
        return (null != feedItemList ? feedItemList.size() : 0);
    }

    public OnItemClickListener getOnItemClickListener() {
        return oNItemClickListener;
    }

    public void setOnItemClickListener(OnItemClickListener onItemClickListener) {
        this.oNItemClickListener = onItemClickListener;
    }

    public Filter getFilter() {
        if (filter == null) {
            filter = new CustomCategoriesFilter(feedItemListFiltered, this);
        }
        return filter;
    }

    class CustomViewHolder extends RecyclerView.ViewHolder {
        TextView tv_cat_value, tv_cat_count;
        ImageView imageView;
        RelativeLayout view, relativeCats;

        CustomViewHolder(View view) {
            super(view);
            this.relativeCats = view.findViewById(R.id.relativeCats);
            this.view = view.findViewById(R.id.layoutCategories);
            this.tv_cat_value = view.findViewById(R.id.tv_cat_value);
            this.imageView = view.findViewById(R.id.imageView);
            this.tv_cat_count = view.findViewById(R.id.tv_cat_count);
        }
    }
}
