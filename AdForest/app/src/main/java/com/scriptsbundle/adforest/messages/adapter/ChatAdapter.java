package com.scriptsbundle.adforest.messages.adapter;


import android.app.Activity;
import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.drawable.Drawable;
import android.media.Image;
import android.net.Uri;
import android.os.Environment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.androidnetworking.AndroidNetworking;
import com.androidnetworking.common.Priority;
import com.bumptech.glide.Glide;
import com.squareup.picasso.Picasso;
import com.squareup.picasso.Target;

import java.io.File;
import java.util.ArrayList;

//import com.scriptsbundle.adforest.CollageImageView;
import io.reactivex.rxjava3.core.Observable;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.Settings.Settings;
import com.scriptsbundle.adforest.ad_detail.FragmentAdDetail;
import com.scriptsbundle.adforest.ad_detail.full_screen_image.FullScreenViewActivity;
import com.scriptsbundle.adforest.messages.ChatFragment;
import com.scriptsbundle.adforest.modelsList.ChatMessage;
import com.scriptsbundle.adforest.utills.CollageView;
import com.scriptsbundle.adforest.utills.Helpers;
import com.scriptsbundle.adforest.utills.SettingsMain;

import static android.content.Context.DOWNLOAD_SERVICE;

public class ChatAdapter extends BaseAdapter {

    private static LayoutInflater inflater = null;
    SettingsMain settingsMain;
    Context context;
    private ArrayList<ChatMessage> chatMessageList;

    public ChatAdapter(Activity activity, ArrayList<ChatMessage> list) {
        chatMessageList = list;
        context = activity;
        inflater = (LayoutInflater) activity
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        settingsMain = new SettingsMain(context);

    }

    @Override
    public int getCount() {
        return chatMessageList.size();
    }

    @Override
    public Object getItem(int position) {
        return position;
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        final ChatMessage message = chatMessageList.get(position);
        View vi = convertView;

        if (settingsMain.getRTL()){
            if (message.isMine()) {
                vi = inflater.inflate(R.layout.item_chat_layout_rtl, null);
            } else {
                vi = inflater.inflate(R.layout.item_chat_received_layout_rtl, null);
            }
        }else{
            if (message.isMine()) {
                vi = inflater.inflate(R.layout.item_chat_layout, null);
            } else {
                vi = inflater.inflate(R.layout.item_chat_received_layout, null);
            }
        }

        TextView tv_message = vi.findViewById(R.id.message);
        TextView tv_date = vi.findViewById(R.id.tv_date);
        ImageView imageView = vi.findViewById(R.id.profile_image);
        LinearLayout bubbleLayout = vi.findViewById(R.id.chat_bubble);
        LinearLayout imagesLayout = vi.findViewById(R.id.imageLayout);
        LinearLayout fileLayout = vi.findViewById(R.id.filesLayout);
        LinearLayout collageImageView = vi.findViewById(R.id.imageInner);


        Picasso.get().load(message.getImage())
                .error(R.drawable.placeholder)
                .placeholder(R.drawable.placeholder)
                .into(imageView);
        tv_message.setText(message.getBody());
        tv_date.setText(message.getDate());

        if (message.getBody() == null || message.getBody().equals("")) {
            bubbleLayout.setVisibility(View.GONE);
        }

        if (message.getImages() != null && message.getImages().size()!=0) {
            if (message.getImages().size()==1){
                View v = LayoutInflater.from(context).inflate(R.layout.collage_one,null);
                ImageView imageView1 = v.findViewById(R.id.image1);
                Glide.with(context).load(message.getImages().get(0)).into(imageView1);
                collageImageView.addView(v);
                v.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Intent i = new Intent(context, FullScreenViewActivity.class);
                        i.putExtra("imageUrls", message.getImages());
                        i.putExtra("position", 0);
                        context.startActivity(i);
                    }
                });
            }else if (message.getImages().size()==2){
                View v = LayoutInflater.from(context).inflate(R.layout.collage_two,null);
                ImageView imageView1 = v.findViewById(R.id.image1);
                Glide.with(context).load(message.getImages().get(0)).into(imageView1);
                ImageView imageView2 = v.findViewById(R.id.image2);
                Glide.with(context).load(message.getImages().get(1)).into(imageView2);
                collageImageView.addView(v);
                v.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Intent i = new Intent(context, FullScreenViewActivity.class);
                        i.putExtra("imageUrls", message.getImages());
                        i.putExtra("position", 0);
                        context.startActivity(i);
                    }
                });
            }else if (message.getImages().size()==3){
                try{

                    View v = LayoutInflater.from(context).inflate(R.layout.collage_three,null);
                    ImageView imageView1 = v.findViewById(R.id.image1);
                    Glide.with(context).load(message.getImages().get(0)).into(imageView1);
                    ImageView imageView2 = v.findViewById(R.id.image2);
                    Glide.with(context).load(message.getImages().get(1)).into(imageView2);
                    ImageView imageView3 = v.findViewById(R.id.image3);
                    Glide.with(context).load(message.getImages().get(2)).into(imageView3);
                    collageImageView.addView(v);
                    v.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {
                            Intent i = new Intent(context, FullScreenViewActivity.class);
                            i.putExtra("imageUrls", message.getImages());
                            i.putExtra("position", 0);
                            context.startActivity(i);
                        }
                    });
                }catch (Exception e){
                    e.printStackTrace();
                }
            }else {
                View v = LayoutInflater.from(context).inflate(R.layout.collage_four,null);
                ImageView imageView1 = v.findViewById(R.id.image1);
                Glide.with(context).load(message.getImages().get(0)).into(imageView1);
                ImageView imageView2 = v.findViewById(R.id.image2);
                Glide.with(context).load(message.getImages().get(1)).into(imageView2);
                ImageView imageView3 = v.findViewById(R.id.image3);
                Glide.with(context).load(message.getImages().get(2)).into(imageView3);
                ImageView imageView4 = v.findViewById(R.id.image4);
                Glide.with(context).load(message.getImages().get(3)).into(imageView4);
                collageImageView.addView(v);
                v.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        Intent i = new Intent(context, FullScreenViewActivity.class);
                        i.putExtra("imageUrls", message.getImages());
                        i.putExtra("position", 0);
                        context.startActivity(i);
                    }
                });
            }
        } else {
            imagesLayout.setVisibility(View.GONE);
        }

        if (message.getFile() != null) {
            String urlStr = message.getFile().get(0);
            String fileName = urlStr.substring(urlStr.lastIndexOf('/')+1, urlStr.length());
            TextView fileNameTv = vi.findViewById(R.id.fileName);
            fileNameTv.setText(fileName);
            fileLayout.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Helpers.Companion.downloadFile(context,message.getFile().get(0),fileName);
                }
            });
        } else {
            fileLayout.setVisibility(View.GONE);
        }


        // if message is mine then align to right
        if (settingsMain.getRTL()) {
            if (message.isMine()) {
                tv_message.setPaddingRelative(10, 5, 30, 0);
                tv_date.setPaddingRelative(10, 10, 30, 0);
                bubbleLayout.setBackgroundResource(R.drawable.ic_rtl_send_message);

            }
            // If not mine then align to left
            else {
                bubbleLayout.setBackgroundResource(R.drawable.ic_rtl_received_message);
                tv_message.setPaddingRelative(30, 5, 10, 0);
                tv_date.setPaddingRelative(30, 10, 10, 0);
            }
        }

        return vi;

    }




    public void add(ChatMessage object) {
        chatMessageList.add(object);
    }
}