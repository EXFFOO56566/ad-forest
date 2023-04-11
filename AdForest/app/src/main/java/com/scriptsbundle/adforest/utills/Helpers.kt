package com.scriptsbundle.adforest.utills

import android.app.DownloadManager
import android.content.Context
import android.net.Uri
import android.os.Environment
import android.widget.Toast
import com.scriptsbundle.adforest.messages.ChatFragment

class Helpers {

    companion object {
        fun downloadFile(context: Context, url: String, filename: String) {
            Toast.makeText(context, "Download Started", Toast.LENGTH_SHORT).show()
            val request = DownloadManager.Request(Uri.parse(url))
            request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, filename)
            request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
            val downloadManager = arrayOf(context.getSystemService(Context.DOWNLOAD_SERVICE) as DownloadManager)
            ChatFragment.downloadId = downloadManager[0].enqueue(request)
        }
    }

}