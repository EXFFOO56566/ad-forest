package com.scriptsbundle.adforest.messages.WhizzChat.Models

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

data class Data(
        @SerializedName("chat_list")
        @Expose
        val chat_list: List<Chat>
)