package com.scriptsbundle.adforest.messages.WhizzChat.Models

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

data class Chat(
        @SerializedName("chat_id")
        @Expose
        val chat_id: String,
        @SerializedName("image_url")
        @Expose
        val image_url: String,
        @SerializedName("last_active_time")
        @Expose
        val last_active_time: String,
        @SerializedName("message_count")
        @Expose
        val message_count: String,
        @SerializedName("message_for")
        @Expose
        val message_for: String,
        @SerializedName("new_message")
        @Expose
        val new_message: String,
        @SerializedName("post_title")
        @Expose
        val post_title: String,
        @SerializedName("receiver_name")
        @Expose
        val receiver_name: String
)