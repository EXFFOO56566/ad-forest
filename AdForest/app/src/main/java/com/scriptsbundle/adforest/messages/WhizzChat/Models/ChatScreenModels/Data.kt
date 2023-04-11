package com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

data class Data(
        @SerializedName("author_id")
        @Expose
        val author_id: String,
        @SerializedName("blocked_status")
        @Expose
        val blocked_status: BlockedStatus,
        @SerializedName("chat")
        @Expose
        val chat: List<ChatItem>,
        @SerializedName("chat_id")
        @Expose
        val chat_id: String,
        @SerializedName("communication_id")
        @Expose
        val communication_id: String,
        @SerializedName("id")
        @Expose
        val id: String,
        @SerializedName("live_room_data")
        @Expose
        val live_room_data: String,
        @SerializedName("post-id")
        @Expose
        val post_id: String,
        @SerializedName("post_title")
        @Expose
        val post_title: String,
        @SerializedName("sender_id")
        @Expose
        val sender_id: String,
        @SerializedName("user_name")
        @Expose
        val user_name: String
)