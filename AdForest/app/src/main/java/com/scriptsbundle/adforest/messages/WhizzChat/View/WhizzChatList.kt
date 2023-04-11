package com.scriptsbundle.adforest.messages.WhizzChat.View

import android.annotation.SuppressLint
import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.util.Log
import android.view.MenuItem
import android.view.View
import android.view.WindowManager
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.DividerItemDecoration
import androidx.recyclerview.widget.LinearLayoutManager
import com.scriptsbundle.adforest.databinding.ActivityWhizzChatListBinding
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import okhttp3.ResponseBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import com.scriptsbundle.adforest.messages.WhizzChat.Adapters.WhizzChatListAdapter
import com.scriptsbundle.adforest.messages.WhizzChat.Models.Chat
import com.scriptsbundle.adforest.messages.WhizzChat.Models.ChatListModel
import com.scriptsbundle.adforest.utills.Network.RestService
import com.scriptsbundle.adforest.utills.SettingsMain
import com.scriptsbundle.adforest.utills.UrlController
import com.scriptsbundle.adforest.utills.UrlController.createServiceNoTimeoutUP

class WhizzChatList : AppCompatActivity() ,WhizzChatListAdapter.OnItemClickListener{


    lateinit var call: Call<ChatListModel>
    lateinit var adapter: WhizzChatListAdapter
    lateinit var restService: RestService
    lateinit var settingsMain: SettingsMain
    private lateinit var binding: ActivityWhizzChatListBinding
    lateinit var chatList: ChatListModel
    lateinit var chat_list: List<Chat>

    @SuppressLint("WrongConstant")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityWhizzChatListBinding.inflate(layoutInflater)
        val view = binding.root
        setContentView(view)

        // Toolbar Stuff
        setupActionBar()

        //Network and Utilities
        settingsMain = SettingsMain(this)
        restService = createServiceNoTimeoutUP(RestService::class.java, settingsMain.userEmail, settingsMain.userPassword, this);


        binding.recyclerView.layoutManager = LinearLayoutManager(this, LinearLayout.VERTICAL, false)
        binding.recyclerView.addItemDecoration(DividerItemDecoration(this,DividerItemDecoration.VERTICAL))
        CoroutineScope(Dispatchers.IO).launch {
            getData()
        }
    }

    fun getData() {
        binding.shimmerMain.visibility = View.VISIBLE
        UrlController.loading = true
        call = restService.getWhizzChatList(UrlController.AddHeaders(this))
        call.enqueue(object : Callback<ChatListModel> {
            override fun onResponse(call: Call<ChatListModel>, response: Response<ChatListModel>) {
                binding.shimmerMain.visibility = View.GONE
                UrlController.loading = false
                binding.mainLayout.visibility = View.VISIBLE
                Log.d("Response ", response.body()?.success.toString())
                if (response.body()!!.success) {

                    chatList = response.body()!!
                    if (chatList.success) {
                        if (chatList.data.chat_list.isNotEmpty()) {
                            chat_list = chatList.data.chat_list;
                            adapter = WhizzChatListAdapter(chat_list, applicationContext,this@WhizzChatList)
                            binding.recyclerView.adapter = adapter
                            adapter.notifyDataSetChanged()
                        }else{
                            binding.recyclerView.visibility = View.GONE
                            binding.noChatText.visibility = View.VISIBLE
                            binding.noChatText.text = chatList.message
                        }
                    }
                } else {
                    Toast.makeText(applicationContext, response.body()!!.message, Toast.LENGTH_SHORT).show()
                    binding.recyclerView.visibility = View.GONE
                    binding.noChatText.visibility = View.VISIBLE
                    binding.noChatText.text = response.body()!!.message
                }
            }

            override fun onFailure(call: Call<ChatListModel>, t: Throwable) {
                Toast.makeText(applicationContext,t.localizedMessage,Toast.LENGTH_SHORT)
                finish()
            }

        })
    }

    fun setupActionBar() {

        binding.toolbar.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        val window = window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS)
        window.statusBarColor = Color.parseColor(SettingsMain.getMainColor())
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId==android.R.id.home){
            finish()
        }
        return super.onOptionsItemSelected(item)
    }

    override fun onItemClick(chat: Chat) {
        val intent = Intent(this,WhizzChatScreen::class.java)
        intent.putExtra("chatId",chat.chat_id)
        intent.putExtra("adTitle",chat.post_title)
        intent.putExtra("adAuthor",chat.receiver_name)
        startActivity(intent)
    }
}