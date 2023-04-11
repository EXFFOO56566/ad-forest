package com.scriptsbundle.adforest.adapters;


import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.smarteist.autoimageslider.SliderViewAdapter;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.imageAdapterOnclicklistner;


public class SliderAdapterExample extends
        SliderViewAdapter<SliderAdapterExample.SliderAdapterVH> {

    private Context context;
    private List<SliderItem> mSliderItems = new ArrayList<>();
    private imageAdapterOnclicklistner onItemClickListener;
    private boolean carousel = false;

    public SliderAdapterExample(Context context) {
        this.context = context;
    }

    public void renewItems(List<SliderItem> sliderItems) {
        this.mSliderItems = sliderItems;
        notifyDataSetChanged();
    }

    public void deleteItem(int position) {
        this.mSliderItems.remove(position);
        notifyDataSetChanged();
    }

    public void addItem(SliderItem sliderItem) {
        this.mSliderItems.add(sliderItem);
        notifyDataSetChanged();
    }

    public void setCarousel(boolean carousel) {
        this.carousel = carousel;
    }

    @Override
    public SliderAdapterExample.SliderAdapterVH onCreateViewHolder(ViewGroup parent) {
        View inflate;
        if (carousel)
            inflate = LayoutInflater.from(parent.getContext()).inflate(R.layout.carousel_item, parent,false);
        else
            inflate = LayoutInflater.from(parent.getContext()).inflate(R.layout.image_slider_layout_item, parent,false);
        return new SliderAdapterExample.SliderAdapterVH(inflate);

    }

    @Override
    public void onBindViewHolder(SliderAdapterVH viewHolder, final int position) {

        SliderItem sliderItem = mSliderItems.get(position);

//        viewHolder.textViewDescription.setText(sliderItem.getDescription());
//        viewHolder.textViewDescription.setTextSize(16);
//        viewHolder.textViewDescription.setTextColor(Color.WHITE);
        if (carousel)
            Glide.with(viewHolder.itemView)
                    .load(sliderItem.getImageUrl())
                    .into(viewHolder.imageViewBackground);
        else
            Glide.with(viewHolder.itemView)
                .load(sliderItem.getImageUrl())
                .fitCenter()
                .into(viewHolder.imageViewBackground);

//        Picasso.get().load(sliderItem.getImageUrl()).into(viewHolder.imageViewBackground);

        View.OnClickListener listner = (new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onItemClickListener.onItemClick(sliderItem,position);
//                Toast.makeText(context, "This is item in position " + position, Toast.LENGTH_SHORT).show();
            }
        });
        viewHolder.itemView.setOnClickListener(listner);
    }
    public void setOnItemClickListener(imageAdapterOnclicklistner onItemClickListener) {
        this.onItemClickListener =  onItemClickListener;
    }
    @Override
    public int getCount() {
        //slider view count could be dynamic size
        return mSliderItems.size();
    }

     class SliderAdapterVH extends SliderViewAdapter.ViewHolder {

        View itemView;
        ImageView imageViewBackground;
        ImageView imageGifContainer;
        TextView textViewDescription;

        public SliderAdapterVH(View itemView) {
            super(itemView);
            imageViewBackground = itemView.findViewById(R.id.iv_auto_image_slider);
            imageGifContainer = itemView.findViewById(R.id.iv_gif_container);
            textViewDescription = itemView.findViewById(R.id.tv_auto_image_slider);

            this.itemView = itemView;
        }
    }

}
