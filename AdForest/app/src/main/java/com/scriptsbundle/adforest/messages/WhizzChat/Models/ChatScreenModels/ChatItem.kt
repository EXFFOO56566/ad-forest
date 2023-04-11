package com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels

import android.os.Parcelable
import com.google.gson.annotations.SerializedName

data class ChatItem(
        @SerializedName("attachments")
        var attachments: String? = "",
        @SerializedName("chat_message")
        var chat_message: String? = "",
        @SerializedName("chat_message_id")
        var chat_message_id: String? = "",
        @SerializedName("chat_post_author")
        var chat_post_author: String? = "",
        @SerializedName("chat_post_id")
        var chat_post_id: String? = "",
        @SerializedName("chat_sender_id")
        var chat_sender_id: String? = "",
        @SerializedName("chat_sender_name")
        var chat_sender_name: String? = "",
        @SerializedName("chat_time")
        var chat_time: String? = "",
        @SerializedName("time_chat")
        var time_chat: String? = "",
        @SerializedName("is_reply")
        var is_reply: String? = "",
        @SerializedName("message_type")
        var message_type: String? = "",
        @SerializedName("msg")
        var msg: String? = "",
        @SerializedName("rel")
        var rel: String? = "",
        @SerializedName("seen_at")
        var seen_at: Any?= "",
        @SerializedName("map_image_url")
        var map_image_url: String?= "",
        @SerializedName("image_url")
        var image_url: ArrayList<String> = ArrayList<String>(),
        @SerializedName("file_url")
        var file_url: ArrayList<String> = ArrayList<String>()
)