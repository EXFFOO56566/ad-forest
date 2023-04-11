package com.scriptsbundle.adforest.messages.WhizzChat.View

import android.annotation.SuppressLint
import android.graphics.Color
import android.graphics.drawable.ShapeDrawable
import android.graphics.drawable.shapes.OvalShape
import android.os.Bundle
import android.util.Log
import android.view.MenuItem
import android.view.View
import android.view.WindowManager
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.content.res.AppCompatResources
import androidx.core.graphics.drawable.DrawableCompat
import com.google.gson.JsonObject
import io.socket.client.IO
import io.socket.client.Socket
import io.socket.emitter.Emitter
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import okhttp3.MultipartBody
import okhttp3.RequestBody
import okhttp3.ResponseBody
import org.json.JSONException
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import com.scriptsbundle.adforest.R
import com.scriptsbundle.adforest.databinding.ActivityWhizzChatScreenBinding
import com.scriptsbundle.adforest.helper.SocketSSL
import com.scriptsbundle.adforest.messages.AttachmentModel
import com.scriptsbundle.adforest.messages.ChatBottomSheet
import com.scriptsbundle.adforest.messages.WhizzChat.Adapters.ChatAdapter
import com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels.ChatItem
import com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatScreenModels.ChatModel
import com.scriptsbundle.adforest.utills.Network.RestService
import com.scriptsbundle.adforest.utills.SettingsMain
import com.scriptsbundle.adforest.utills.UrlController
import timber.log.Timber


@SuppressLint("LogNotTimber")
class WhizzChatScreen : AppCompatActivity(), ChatBottomSheet.ChatInterface, ChatBottomSheet.WhizzChatInterface {

    lateinit var binding: ActivityWhizzChatScreenBinding
    lateinit var restService: RestService
    lateinit var settingsMain: SettingsMain
    lateinit var adapter: ChatAdapter
    lateinit var mSocket: Socket
    lateinit var chatId: String
    lateinit var roomId: String
    lateinit var chatModel: ChatModel
    lateinit var attachmentModel: AttachmentModel
    lateinit var call : Call<ChatModel>
    var chatList = mutableListOf<ChatItem>()
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityWhizzChatScreenBinding.inflate(layoutInflater)
        val view = binding.root
        setContentView(view)


        //gettingChatId
        chatId = intent.getStringExtra("chatId").toString()
        binding.adTitle.text = intent.getStringExtra("adTitle").toString()
        binding.adAuthor.text = intent.getStringExtra("adAuthor").toString()


        //Network and Utilities
        settingsMain = SettingsMain(this)
        restService = UrlController.createServiceNoTimeoutUP(RestService::class.java, settingsMain.userEmail, settingsMain.userPassword, this);

        setupActionBar()


        binding.sendMessageButton.setOnClickListener {
            if (!binding.messageEditText.text.isEmpty()){
                binding.messageLoading.visibility = View.VISIBLE
                binding.sendMessageButton.visibility = View.GONE
                sendMessage(binding.messageEditText.text.toString())
                CoroutineScope(Dispatchers.IO).launch {
                    saveMessageOnAPI(binding.messageEditText.text.toString(), false, null, null);
                }
            }
        }

        binding.pickerView.setOnClickListener {

            val bottomSheet = ChatBottomSheet(this, restService, this, attachmentModel)
            bottomSheet.setWhizzChatInterface(this)
            bottomSheet.calledFrom(chatId, chatModel.data.communication_id, chatModel.data.sender_id, chatModel.data.post_id, chatList.get(chatList.size - 1).chat_message_id!!)
            bottomSheet.show(supportFragmentManager, "chatSheet")
        }


        CoroutineScope(Dispatchers.IO).launch {
            getChat(true);
        }



    }


    fun drawCircle(width: Int, height: Int, color: Int): ShapeDrawable? {

        //////Drawing oval & Circle programmatically /////////////
        val oval = ShapeDrawable(OvalShape())
        oval.intrinsicHeight = height
        oval.intrinsicWidth = width
        oval.paint.color = color
        return oval
    }
    fun setupActionBar() {
//        binding.messageLoading.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))

        binding.messageLoading.background = drawCircle(50,50,Color.parseColor(SettingsMain.getMainColor()))
        binding.toolbar.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        val window = window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS)
        window.statusBarColor = Color.parseColor(SettingsMain.getMainColor())

        val unwrappedDrawable = AppCompatResources.getDrawable(applicationContext, R.drawable.fieldradius)
        val wrappedDrawable = DrawableCompat.wrap(unwrappedDrawable!!)
        DrawableCompat.setTint(wrappedDrawable, Color.parseColor(SettingsMain.getMainColor()))
        binding.sendMessageButton.background = wrappedDrawable
    }

    private fun getChat(loading: Boolean) {

        if (loading){
            binding.shimmerMain.visibility = View.VISIBLE
            UrlController.loading = true
        }else{
            binding.shimmerMain.visibility = View.GONE
            binding.mainLayout.visibility = View.VISIBLE
        }

        var jsonObject = JsonObject()
        jsonObject.addProperty("chat_id", chatId)

        call = restService.getWhizzChat(jsonObject, UrlController.AddHeaders(this))
        call.enqueue(object : Callback<ChatModel> {
            override fun onResponse(call: Call<ChatModel>, response: Response<ChatModel>) {
                binding.shimmerMain.visibility = View.GONE
                UrlController.loading = false
                binding.mainLayout.visibility = View.VISIBLE
                binding.attachmentProgress.visibility = View.GONE
                binding.sendMessageButton.visibility = View.VISIBLE
                binding.messageLoading.visibility = View.GONE
                if (response.isSuccessful && response.body()!!.success) {
                    binding.messageEditText.setText("")
                    var extra = response.body()!!.extra
                    roomId = response.body()!!.data.live_room_data

                    attachmentModel = AttachmentModel()
                    attachmentModel.attachment_size = extra.file_size.toString();
                    attachmentModel.attachment_allow = extra.file_allow == "1"
                    attachmentModel.imageAllow = extra.image_allow == "1"
                    attachmentModel.locationAllow = extra.location_allow == "1"
                    attachmentModel.image_size = extra.image_size.toString()
                    attachmentModel.attachment_size = extra.file_size.toString()
                    attachmentModel.attachment_format = mutableListOf<String>()
                    for (i in extra.file_format) {
                        attachmentModel.attachment_format.add(i)
                    }
                    for (i in extra.image_format) {
                        attachmentModel.attachment_format.add(i)
                    }
                    attachmentModel.image_limit_txt = extra.image_limit_txt
                    attachmentModel.doc_limit_txt = extra.doc_limit_txt
                    attachmentModel.doc_format_txt = extra.doc_format_txt
                    attachmentModel.upload_image = extra.upload_image
                    attachmentModel.upload_doc = extra.upload_doc
                    attachmentModel.upload_loc = extra.upload_loc
                    attachmentModel.upload_map = extra.upload_loc

                    if (!attachmentModel.attachment_allow && !attachmentModel.imageAllow && !attachmentModel.locationAllow) {
                        binding.pickerView.visibility = View.GONE
                    }


                    chatModel = response.body()!!
                    chatList = response.body()!!.data.chat.toMutableList()
                    adapter = ChatAdapter(chatList, applicationContext)
                    binding.listView.adapter = adapter;

                    scrollMyListViewToBottom()
                    initiateSocket()
                    if (loading)
                        joinRoom(chatModel.data.live_room_data, chatModel.data.sender_id, chatModel.data.communication_id)

                } else {
                    Toast.makeText(applicationContext, response.message(), Toast.LENGTH_SHORT)
                    finish()
                }
            }

            override fun onFailure(call: Call<ChatModel>, t: Throwable) {
            }

        })
    }

    private fun scrollMyListViewToBottom() {
        binding.listView.post(Runnable { // Select the last row so it will scroll into view...
            binding.listView.setSelection(adapter.count - 1)
        })
    }

    fun initiateSocket() {
        try {
            val options = IO.Options()
            options.reconnection = true
            options.reconnectionAttempts = 3

            options.query = "apiKey="+SettingsMain.whizzChatAPIKey.trim()+"&website="+UrlController.IP_ADDRESS
            if (SettingsMain.whizzChatAPIKey.equals("")){
                Toast.makeText(applicationContext,"Please add agile pusher API key in wp-admin panel",Toast.LENGTH_LONG).show()
                finish()
                return
            }

            options.transports = arrayOf("websocket")
            options.forceNew = true
            SocketSSL.set(options)
            mSocket = IO.socket("https://socket.agilepusher.com", options)
            mSocket.on(Socket.EVENT_CONNECT, Emitter.Listener {args ->
                Log.d("Status ++++++++++","Connected")
                joinRoom(chatModel.data.live_room_data, chatModel.data.sender_id, chatModel.data.communication_id)
            })
                    .on(Socket.EVENT_DISCONNECT, Emitter.Listener { })
                    .on("agInfoMessage", Emitter.Listener { args ->
                        runOnUiThread {
                            val message = args[0]
                            Log.d("Ag Info Message ----- ", message.toString())
                        }
                    }).on("agInfoMessage_dev", Emitter.Listener { args ->
                        runOnUiThread {
                            val message = args[0]
                            Log.d("Ag Info Message ----- ", message.toString())
                        }
                    })
                    .on("agAskedToJoin", Emitter.Listener { args ->
                        runOnUiThread {
                            try {
                                val room = args[0] as String
                                if (args[1] is String) {

                                    val receiver = args[1] as String
                                    Log.d("Room Id----", room)
                                    Log.d("Receiver Id-----", receiver)
                                    if (args[1].toString().equals(chatModel.data.sender_id)) {
                                        joinRoom(room, receiver, "")
                                    }
                                }
                            } catch (e: Exception) {
                                e.printStackTrace()
                            }
                        }
                    })
                    .on("agGotNewMessage", Emitter.Listener { args ->
                        runOnUiThread {
                            getChat(false)
                        }
                    }).on(Socket.EVENT_DISCONNECT,Emitter.Listener {
                        Log.d("Status ++++++++++","Disconnected") })
                    .on("error", Emitter.Listener { args->
                        Log.d("Status ++++++++++","Error 1")
                    })
                    .on(Socket.EVENT_CONNECT_ERROR, Emitter.Listener {args->
                        Log.d("Status ++++++++++","Error 2") })
            mSocket.connect()
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }


    private fun sendMessage(message: String?) {
        mSocket.emit("agSendMessage", roomId, message, chatModel.data.communication_id, chatId)

    }

    private fun joinRoom(room: String, sender: String, receiver: String) {
        mSocket.emit("agRoomJoined", room, sender, receiver)
    }

    override fun onPause() {
        super.onPause()
        mSocket.disconnect()
    }

    private fun saveMessageOnAPI(message: String?, location: Boolean, latitude: Double?, longitude: Double?) {
        runOnUiThread{
            binding.attachmentProgress.visibility = View.VISIBLE
        }
        val jsonObject = JsonObject()
        if (location){
            jsonObject.addProperty("latitude", latitude)
            jsonObject.addProperty("longitude", longitude)
            jsonObject.addProperty("message_type", "map")
        }
        else{
            jsonObject.addProperty("msg", message)
            jsonObject.addProperty("message_type", "text")
        }
        jsonObject.addProperty("chat_id", chatId)
        jsonObject.addProperty("session", chatModel.data.sender_id)
        jsonObject.addProperty("post_id", chatModel.data.post_id)
        jsonObject.addProperty("comm_id", chatModel.data.communication_id)
        jsonObject.addProperty("messages_ids", chatList.get(chatList.size - 1).chat_message_id)

        val call = restService.sendWhizzChatMessage(jsonObject, UrlController.AddHeaders(this))
        call.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    try {
                        val json = JSONObject(response.body()!!.string())
                        Log.d("Sent Message Response", json.toString())
                        if (json.getBoolean("success")) {
                            if (location)
                                sendMessage("")
                            runOnUiThread {
                                getChat(false)
                            }
                        }
                    } catch (e: JSONException) {
                        e.printStackTrace()
                    }
                }

            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                binding.attachmentProgress.visibility = View.GONE
            }

        })
    }

    override fun getFiles(parts: MutableList<MultipartBody.Part>, count: Int) {

    }

    override fun getLocation(latitude: Double?, longitude: Double?) {
        saveMessageOnAPI("", true, latitude, longitude)
    }

    override fun getFiles(chatId: RequestBody, session: RequestBody, postId: RequestBody, commId: RequestBody, messageId: RequestBody, messageType: RequestBody, uploadType: RequestBody, parts: MutableList<MultipartBody.Part>) {
        binding.attachmentProgress.visibility = View.VISIBLE
        val req = restService.whizzChatSendFile(chatId, session, postId, commId, messageId, messageType, uploadType, parts, UrlController.UploadImageAddHeaders(this))
        req.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                binding.attachmentProgress.visibility = View.GONE
                if (response.isSuccessful) {
                    try {

                        val jsonObject = JSONObject(response.body()!!.string())

                        Toast.makeText(applicationContext, jsonObject.getString("message"), Toast.LENGTH_SHORT).show()
                        if (jsonObject.getBoolean("success")) {
                            sendMessage("")
                            getChat(false)
                        }
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                } else {
                    Log.d("info Upload", response.toString())
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                binding.attachmentProgress.visibility = View.GONE
                Log.e("info Upload Image Err:", t.toString())
                t.localizedMessage
                t.printStackTrace()
            }
        })
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if(item.itemId == android.R.id.home){
            finish()
        }
        return super.onOptionsItemSelected(item)
    }

    override fun onDestroy() {
        super.onDestroy()
        call.cancel()
    }


}