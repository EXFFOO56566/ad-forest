package com.scriptsbundle.adforest.messages.WhizzChat.Models

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

data class Extra(
        @SerializedName("doc_format_txt")
        @Expose
        val doc_format_txt: String,
        @SerializedName("doc_limit_txt")
        @Expose
        val doc_limit_txt: String,
        @SerializedName("file_allow")
        @Expose
        val file_allow: String,
        @SerializedName("file_format")
        @Expose
        val file_format: List<String>,
        @SerializedName("file_size")
        @Expose
        val file_size: Int,
        @SerializedName("image_allow")
        @Expose
        val image_allow: String,
        @SerializedName("image_format")
        @Expose
        val image_format: List<String>,
        @SerializedName("image_limit_txt")
        @Expose
        val image_limit_txt: String,
        @SerializedName("image_size")
        @Expose
        val image_size: Int,
        @SerializedName("location_allow")
        @Expose
        val location_allow: String,
        @SerializedName("upload_doc")
        @Expose
        val upload_doc: String,
        @SerializedName("upload_image")
        @Expose
        val upload_image: String,
        @SerializedName("upload_loc")
        @Expose
        val upload_loc: String
)