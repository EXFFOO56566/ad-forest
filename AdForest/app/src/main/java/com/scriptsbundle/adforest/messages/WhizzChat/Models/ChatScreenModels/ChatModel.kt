package com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels

import com.google.gson.annotations.SerializedName
import com.scriptsbundle.adforest.messages.WhizzChat.Models.Extra

data class ChatModel(
        @SerializedName("data")
        val `data`: Data,
        @SerializedName("message")
        val message: String,
        @SerializedName("success")
        val success: Boolean,
        @SerializedName("extra")
        val extra: Extra
)