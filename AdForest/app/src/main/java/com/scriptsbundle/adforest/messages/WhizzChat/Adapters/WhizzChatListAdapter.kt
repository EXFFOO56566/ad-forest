package com.scriptsbundle.adforest.messages.WhizzChat.Adapters

import android.annotation.SuppressLint
import android.content.Context
import android.content.Intent
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import android.widget.Toast
import androidx.recyclerview.widget.RecyclerView
import com.squareup.picasso.Picasso
import de.hdodenhof.circleimageview.CircleImageView
import com.scriptsbundle.adforest.LinkedIn.MyInterface
import com.scriptsbundle.adforest.R
import com.scriptsbundle.adforest.helper.OnItemClickListener
import com.scriptsbundle.adforest.messages.WhizzChat.Models.Chat
import com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatListModel
import com.scriptsbundle.adforest.messages.WhizzChat.View.WhizzChatScreen

class WhizzChatListAdapter(val list : List<Chat>, val context : Context,val myInterface : OnItemClickListener) :RecyclerView.Adapter<WhizzChatListAdapter.ViewHolder>() {


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val v = LayoutInflater.from(context).inflate(R.layout.whizzchat_list_view, parent, false)
        return ViewHolder(v)
    }

    @SuppressLint("ShowToast")
    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.date.text = list[position].last_active_time
        holder.adName.text = list[position].post_title
        Picasso.get().load(list[position].image_url).into(holder.circleImageView)
        holder.userName.text = list[position].receiver_name
//        holder.bindItems(userList[position])

        holder.itemView.setOnClickListener {
            myInterface.onItemClick(list[position])
        }
    }



    override fun getItemCount(): Int {
        return list.size
    }


    class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        var adName : TextView = itemView.findViewById(R.id.adName)
        var userName : TextView = itemView.findViewById(R.id.userName)
        var date : TextView = itemView.findViewById(R.id.lastSeen)
        var circleImageView : CircleImageView = itemView.findViewById(R.id.circleImageView)

    }

    public interface OnItemClickListener{
        fun onItemClick(chat : Chat)
    }
}