package com.scriptsbundle.adforest.messages.WhizzChat.Adapters

import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Handler
import android.os.Looper
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import com.bumptech.glide.Glide
import com.google.gson.Gson
import com.google.gson.JsonObject
import com.squareup.picasso.Picasso
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import okhttp3.*
import com.scriptsbundle.adforest.R
import com.scriptsbundle.adforest.ad_detail.full_screen_image.FullScreenViewActivity
import com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels.ChatItem
import com.scriptsbundle.adforest.utills.Helpers.Companion.downloadFile
import com.scriptsbundle.adforest.utills.SettingsMain
import java.io.IOException


class ChatAdapter(var list: List<ChatItem>, val context: Context) : BaseAdapter() {
    override fun getCount(): Int {
        return list.size
    }

    override fun getItem(p0: Int): Any {
        TODO("Not yet implemented")
    }

    override fun getItemId(p0: Int): Long {
        return p0.toLong()
    }

    val settingsMain = SettingsMain(context)
    override fun getView(position: Int, p1: View?, p2: ViewGroup?): View {
        lateinit var vi: View
        var chat = list[position]



        if (settingsMain.getRTL()){

            if (chat.is_reply == "message-sender-box") {
                vi = LayoutInflater.from(context).inflate(R.layout.whizzchat_item_chat_rtl, null)
            } else {
                vi = LayoutInflater.from(context).inflate(R.layout.whizzchat_item_chat_received_rtl, null)
            }
        }else{

            if (chat.is_reply == "message-sender-box") {
                vi = LayoutInflater.from(context).inflate(R.layout.whizzchat_item_chat_layout, null)
            } else {
                vi = LayoutInflater.from(context).inflate(R.layout.whizzchat_item_chat_received_layout, null)
            }
        }


        val tv_message = vi.findViewById<TextView>(R.id.message)
        val tv_date = vi.findViewById<TextView>(R.id.tv_date)
        val fileDate = vi.findViewById<TextView>(R.id.fileDate)
        val mapDate = vi.findViewById<TextView>(R.id.mapDate)
        val imageDate = vi.findViewById<TextView>(R.id.imageDate)
        val imageView = vi.findViewById<ImageView>(R.id.profile_image)
        val bubbleLayout = vi.findViewById<LinearLayout>(R.id.chat_bubble)
        val imagesLayout = vi.findViewById<LinearLayout>(R.id.imageLayout)
        val fileLayout = vi.findViewById<LinearLayout>(R.id.filesLayout)
        val mapView = vi.findViewById<ImageView>(R.id.map_view)
        val mapLayout = vi.findViewById<LinearLayout>(R.id.mapLayout)
        val collageImageView = vi.findViewById<LinearLayout>(R.id.imageInner)

        tv_date.text = chat.time_chat
        fileDate.text = chat.time_chat
        imageDate.text = chat.time_chat
        mapDate.text = chat.time_chat
        if (chat.message_type=="text"){
            bubbleLayout.visibility = View.VISIBLE
            imagesLayout.visibility = View.GONE
            mapLayout.visibility = View.GONE
            fileLayout.visibility = View.GONE
            tv_message.text = chat.msg
        }else if (chat.message_type=="image"){

            bubbleLayout.visibility = View.GONE
            imagesLayout.visibility = View.VISIBLE
            fileLayout.visibility = View.GONE
            mapLayout.visibility = View.GONE
            when (chat.image_url.size) {
                1 -> {
                    val v = LayoutInflater.from(context).inflate(R.layout.collage_one, null)
                    val imageView1 = v.findViewById<ImageView>(R.id.image1)
                    loadImage(chat.image_url.get(0), imageView1)
                    collageImageView.addView(v)
                    v.setOnClickListener {
                        val i = Intent(context, FullScreenViewActivity::class.java)
                        i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                        i.putExtra("imageUrls", chat.image_url)
                        i.putExtra("position", 0)
                        context.startActivity(i)
                    }
                }
                2 -> {
                    val v = LayoutInflater.from(context).inflate(R.layout.collage_two, null)
                    val imageView1 = v.findViewById<ImageView>(R.id.image1)
                    loadImage(chat.image_url.get(0), imageView1)
                    val imageView2 = v.findViewById<ImageView>(R.id.image2)
                    loadImage(chat.image_url.get(1), imageView2)
                    collageImageView.addView(v)
                    v.setOnClickListener {
                        val i = Intent(context, FullScreenViewActivity::class.java)
                        i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                        i.putExtra("imageUrls", chat.image_url)
                        i.putExtra("position", 0)
                        context.startActivity(i)
                    }
                }
                3 -> {
                    try {
                        val v = LayoutInflater.from(context).inflate(R.layout.collage_three, null)
                        val imageView1 = v.findViewById<ImageView>(R.id.image1)
                        loadImage(chat.image_url.get(0), imageView1)
                        val imageView2 = v.findViewById<ImageView>(R.id.image2)
                        loadImage(chat.image_url.get(1), imageView2)
                        val imageView3 = v.findViewById<ImageView>(R.id.image3)
                        loadImage(chat.image_url.get(2), imageView3)
                        collageImageView.addView(v)
                        v.setOnClickListener {
                            val i = Intent(context, FullScreenViewActivity::class.java)
                            i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                            i.putExtra("imageUrls", chat.image_url)
                            i.putExtra("position", 0)
                            context.startActivity(i)
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                }
                else -> {
                    val v = LayoutInflater.from(context).inflate(R.layout.collage_four, null)
                    val imageView1 = v.findViewById<ImageView>(R.id.image1)
                    loadImage(chat.image_url.get(0), imageView1)
                    val imageView2 = v.findViewById<ImageView>(R.id.image2)
                    loadImage(chat.image_url.get(1), imageView2)
                    val imageView3 = v.findViewById<ImageView>(R.id.image3)
                    loadImage(chat.image_url.get(2), imageView3)
                    val imageView4 = v.findViewById<ImageView>(R.id.image4)
                    loadImage(chat.image_url.get(3), imageView4)
                    collageImageView.addView(v)
                    v.setOnClickListener {
                        val i = Intent(context, FullScreenViewActivity::class.java)
                        i.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                        i.putExtra("imageUrls", chat.image_url)
                        i.putExtra("position", 0)
                        context.startActivity(i)
                    }
                }
            }

        }else if (chat.message_type == "file"){
            bubbleLayout.visibility = View.GONE
            imagesLayout.visibility = View.GONE
            fileLayout.visibility = View.VISIBLE
            mapLayout.visibility = View.GONE

            chat.file_url.let {
                val urlStr: String = it.get(0)
                val fileName = urlStr.substring(urlStr.lastIndexOf('/') + 1, urlStr.length)
                val fileNameTv = vi.findViewById<TextView>(R.id.fileName)
                fileNameTv.text = fileName
                fileLayout.setOnClickListener { downloadFile(context, chat.file_url.get(0), fileName) }

            }
        }else if(chat.message_type == "map"){
            bubbleLayout.visibility = View.GONE
            imagesLayout.visibility = View.GONE
            fileLayout.visibility = View.GONE
            mapLayout.visibility = View.VISIBLE

            val convertedObject: JsonObject = Gson().fromJson(chat.attachments, JsonObject::class.java)
            val appInfo = context.packageManager.getApplicationInfo(context.packageName,PackageManager.GET_META_DATA)
            val apiKey = appInfo.metaData.getString("com.google.android.geo.API_KEY")
            Glide.with(context).load("http://maps.googleapis.com/maps/api/staticmap?zoom=16&size=300x160&markers=size:mid%7Ccolor:red|"+
                    convertedObject.get("latitude")+","+convertedObject.get("longitude")+"&key=$apiKey").into(mapView)
            mapView.setOnClickListener{
                val gmmIntentUri = Uri.parse("geo:0,0?q="+convertedObject.get("latitude")+","+convertedObject.get("longitude"))
                val mapIntent = Intent(Intent.ACTION_VIEW, gmmIntentUri)
                mapIntent.setPackage("com.google.android.apps.maps")
                mapIntent.flags = Intent.FLAG_ACTIVITY_NEW_TASK
                context.startActivity(mapIntent)
            }

        }

        return vi
    }

    private fun loadImage(url: String, imageView: ImageView){
        CoroutineScope(Dispatchers.Main).launch {
            Picasso.get().load(url).into(imageView)
        }
    }
}