package com.scriptsbundle.adforest.messages.WhizzChat.Models

import com.google.gson.annotations.SerializedName

data class ChatListModel(
        @SerializedName("data")
        val `data`: Data,
        @SerializedName("message")
        val message: String,
        @SerializedName("success")
        val success: Boolean,
)