package com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels

import com.google.gson.annotations.SerializedName

data class BlockedStatus(
        @SerializedName("blocked_id")
        val blocked_id: String,
        @SerializedName("blocker_id")
        val blocker_id: Int,
        @SerializedName("chat_session")
        val chat_session: String,
        @SerializedName("current_session")
        val current_session: Int,
        @SerializedName("id")
        val id: Int,
        @SerializedName("is_blocked")
        val is_blocked: Boolean,
        @SerializedName("post_id")
        val post_id: String
)