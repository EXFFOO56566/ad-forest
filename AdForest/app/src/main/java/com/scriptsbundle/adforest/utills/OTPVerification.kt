package com.scriptsbundle.adforest.utills

import android.annotation.SuppressLint
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.Drawable
import android.graphics.drawable.GradientDrawable
import android.os.Bundle
import android.os.CountDownTimer
import android.text.Editable
import android.text.TextWatcher
import android.util.Log
import android.view.KeyEvent
import android.view.MenuItem
import android.view.View
import android.view.WindowManager
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import com.google.firebase.FirebaseException
import com.google.firebase.auth.*
import com.google.gson.JsonObject
import okhttp3.ResponseBody
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import com.scriptsbundle.adforest.R
import com.scriptsbundle.adforest.databinding.ActivityOtpVerificationBinding
import com.scriptsbundle.adforest.profile.Model.OTPModel
import com.scriptsbundle.adforest.utills.Network.RestService
import java.util.concurrent.TimeUnit


@SuppressLint("LogNotTimber")
class OTPVerification : AppCompatActivity(), View.OnFocusChangeListener, View.OnKeyListener, PhoneCallbacks.PhoneCallbacksListener {
    lateinit var binding: ActivityOtpVerificationBinding
    lateinit var phoneNumber: String
    lateinit var call: Call<ResponseBody>
    lateinit var restService: RestService
    var otpModel = OTPModel.getInstance()
    lateinit var verificationId: String
    private var calledFromAuth: Boolean = false
    val auth = FirebaseAuth.getInstance()

    @SuppressLint("ShowToast")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityOtpVerificationBinding.inflate(layoutInflater)
        val view = binding.root
        setContentView(view)


        binding.submit.isEnabled = false
        binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))

        val settingsMain = SettingsMain(this)


        var otpModel = OTPModel.getInstance()
        phoneNumber = intent.getStringExtra("phone").toString()
        calledFromAuth = intent.getBooleanExtra("calledFromAuth", false)
        binding.submit.setText(otpModel.phoneDialogStrings.btn_confirm)
        var codeSentString  = otpModel.getCode_sent()
        binding.codeSent.setText(codeSentString + " " + phoneNumber)
        binding.notReceived.setText(otpModel.getNot_received())
        binding.tryAgain.setText(otpModel.getTry_again())


        restService = UrlController.createService(RestService::class.java);
        setupActionBar()


        manageOTPEditText()

        Toast.makeText(applicationContext, otpModel.getIs_number_verified_text(), Toast.LENGTH_SHORT)

        verifyPhone()


        binding.submit.setOnClickListener {
            if(!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                binding.progress.visibility = View.VISIBLE
                binding.submit.visibility = View.GONE
                verificationId.let {
                    var code = binding.editTextone.text.toString() + binding.editTexttwo.text.toString() + binding.editTextthree.text.toString() + binding.editTextFour.text.toString() + binding.editTextFive.text.toString() + binding.editTextSix.text.toString()
                    val credential = PhoneAuthProvider.getCredential(it, code)
                    auth.signInWithCredential(credential).addOnCompleteListener(this) { task ->
                        if (task.isSuccessful) {
                            if(calledFromAuth){
                                val intent = Intent()
                                intent.putExtra("status","verified")
                                setResult(RESULT_OK,intent)
                                finish()
                            }else{
                                sendVerificationToServer()
                            }
                        } else {
                            binding.progress.visibility = View.GONE
                            binding.submit.visibility = View.VISIBLE
                            Toast.makeText(applicationContext, task.exception?.message, Toast.LENGTH_LONG).show()
                        }

                    }
                }
            }else{
                Toast.makeText(this,"Please enter valid otp verification code",Toast.LENGTH_SHORT).show()
            }

        }

        binding.tryAgain.setOnClickListener {
            verifyPhone()
        }
    }

    fun setupActionBar() {
//        binding.messageLoading.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))

//        binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
        binding.toolbar.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        val window = window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS)
        window.statusBarColor = Color.parseColor(SettingsMain.getMainColor())

        val background: Drawable = binding.editTextone.background
        var gradientDrawable = background as GradientDrawable
        gradientDrawable.setStroke(5, Color.parseColor(SettingsMain.getMainColor()))
        binding.editTexttwo.background = gradientDrawable
        binding.editTextthree.background = gradientDrawable
        binding.editTextFour.background = gradientDrawable
        binding.editTextFive.background = gradientDrawable
        binding.editTextSix.background = gradientDrawable


//        binding.background = wrappedDrawable
    }

    fun startTimer() {

        binding.timer.visibility = View.VISIBLE
        binding.notReceived.visibility = View.GONE
        binding.tryAgain.visibility = View.GONE
        object : CountDownTimer(60000, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                binding.timer.text = "00:" + millisUntilFinished / 1000
                //here you can have your logic to set text to edittext
            }

            override fun onFinish() {
                binding.timer.visibility = View.GONE
                binding.notReceived.visibility = View.VISIBLE
                binding.tryAgain.visibility = View.VISIBLE
            }
        }.start()
    }


    private fun verifyPhone() {
        binding.progress.visibility = View.VISIBLE
        binding.submit.visibility = View.GONE
        val phoneCallBacks = PhoneCallbacks(this)
        val auth = FirebaseAuth.getInstance()
        val options = PhoneAuthOptions.newBuilder(auth)
            .setPhoneNumber(phoneNumber)       // Phone number to verify
            .setTimeout(60L, TimeUnit.SECONDS) // Timeout and unit
            .setActivity(this)                 // Activity (for callback binding)
            .setCallbacks(phoneCallBacks)          // OnVerificationStateChangedCallbacks
            .build()
        PhoneAuthProvider.verifyPhoneNumber(options)
    }


    override fun onFocusChange(v: View?, hasFocus: Boolean) {
        if (hasFocus) {
            var e: EditText = v as EditText
//            e.setText("")
        }
    }

    override fun onKey(v: View?, keyCode: Int, event: KeyEvent?): Boolean {
        if (keyCode == KeyEvent.KEYCODE_DEL) {
            if (binding.editTexttwo.hasFocus() && binding.editTexttwo.text.length == 0) {
                binding.editTextone.requestFocus()
                return true
            }
            if (binding.editTextthree.hasFocus() && binding.editTextthree.text.length == 0) {
                binding.editTexttwo.requestFocus()
                return true
            }
            if (binding.editTextFour.hasFocus() && binding.editTextFour.text.length == 0) {
                binding.editTextthree.requestFocus()
                return true
            }
            if (binding.editTextFive.hasFocus() && binding.editTextFive.text.length == 0) {
                binding.editTextFour.requestFocus()
                return true
            }
            if (binding.editTextSix.hasFocus() && binding.editTextSix.text.length == 0) {
                binding.editTextFive.requestFocus()
                return true
            }
        }
        return false
    }


    fun manageOTPEditText() {
        binding.timer.visibility = View.GONE
        binding.notReceived.visibility = View.GONE
        binding.tryAgain.visibility = View.GONE
        binding.editTextone.onFocusChangeListener = this
        binding.editTexttwo.onFocusChangeListener = this
        binding.editTextthree.onFocusChangeListener = this
        binding.editTextFour.onFocusChangeListener = this
        binding.editTextFive.onFocusChangeListener = this
        binding.editTextSix.onFocusChangeListener = this
        binding.editTextone.setOnKeyListener(this)
        binding.editTexttwo.setOnKeyListener(this)
        binding.editTextthree.setOnKeyListener(this)
        binding.editTextFour.setOnKeyListener(this)
        binding.editTextFive.setOnKeyListener(this)
        binding.editTextSix.setOnKeyListener(this)
        binding.editTextone.addTextChangedListener(object : TextWatcher {
            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                if (binding.editTextone.text.toString().length == 1) {
                    binding.editTextone.clearFocus()
                    binding.editTexttwo.requestFocus()
                    binding.editTexttwo.setCursorVisible(true)
                }
            }

            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {

            }

            override fun afterTextChanged(s: Editable) {
                if (binding.editTextone.text.length == 0) {
                    binding.editTextone.requestFocus()
                }
                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.isEnabled = true
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }
        })


        binding.editTexttwo.addTextChangedListener(object : TextWatcher {
            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                if (binding.editTexttwo.text.toString().length == 1) {
                    binding.editTexttwo.clearFocus()
                    binding.editTextthree.requestFocus()
                    binding.editTextthree.setCursorVisible(true)

                }
            }

            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
            }

            override fun afterTextChanged(s: Editable) {
                if (binding.editTexttwo.text.length == 0) {
                    binding.editTexttwo.requestFocus()
                }


                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.isEnabled = true
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }
        })

        binding.editTextthree.addTextChangedListener(object : TextWatcher {
            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                if (binding.editTextthree.text.toString().length == 1) {
                    binding.editTextthree.clearFocus()
                    binding.editTextFour.requestFocus()
                    binding.editTextFour.setCursorVisible(true)
                }
            }

            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
            }

            override fun afterTextChanged(s: Editable) {
                if (binding.editTextthree.text.toString().length == 0) {
                    binding.editTextthree.requestFocus()
                }


                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.isEnabled = true
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }
        })

        binding.editTextFour.addTextChangedListener(object : TextWatcher {
            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                if (binding.editTextFour.text.toString().length == 1) {
                    binding.editTextFour.clearFocus()
                    binding.editTextFive.requestFocus()
                    binding.editTextFive.setCursorVisible(true)
                }
            }

            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
            }

            override fun afterTextChanged(s: Editable) {
                if (binding.editTextFour.text.toString().length == 0) {
                    binding.editTextFour.requestFocus()
                }


                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.isEnabled = true
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }
        })


        binding.editTextFive.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                if (binding.editTextFive.text.toString().length == 1) {
                    binding.editTextFour.clearFocus()
                    binding.editTextSix.requestFocus()
                    binding.editTextSix.isCursorVisible = true

                }
            }

            override fun afterTextChanged(s: Editable?) {
                if (binding.editTextFive.text.toString().length == 0) {
                    binding.editTextFive.requestFocus()
                }

                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                    binding.submit.isEnabled = true
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }
        })
        binding.editTextSix.addTextChangedListener(object : TextWatcher{
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

            }

            override fun afterTextChanged(s: Editable?) {

                if (!binding.editTextone.text.isEmpty() && !binding.editTexttwo.text.isEmpty()&&
                    !binding.editTextthree.text.isEmpty() && !binding.editTextFour.text.isEmpty()&&
                    !binding.editTextFive.text.isEmpty() && !binding.editTextSix.text.isEmpty()){
                    binding.submit.isEnabled = true
                    binding.submit.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
                }else{
                    binding.submit.setBackgroundColor(Color.parseColor("#e6e6e6"))
                    binding.submit.isEnabled = false
                }
            }


        })
    }


    override fun onVerificationCompleted(credential: PhoneAuthCredential?) {

        Log.d("FIREBASE++++++++++++", "onCodeSent:$credential")
    }


    override fun onVerificationFailed(exception: FirebaseException?) {
        Log.d("FIREBASE++++++++++++", "onException:$exception Error Code:")
        if ((exception as FirebaseAuthException).errorCode =="ERROR_APP_NOT_AUTHORIZED"){
            Toast.makeText(this,otpModel.getError1(),Toast.LENGTH_SHORT).show()
        }else{
            Toast.makeText(this,exception.message,Toast.LENGTH_SHORT).show()
        }
        finish()
    }

    override fun onCodeSent(verificationId: String?, token: PhoneAuthProvider.ForceResendingToken?) {
        startTimer()
        Log.d("Firebase", "onCodeSent:$verificationId")
        Log.d("Token", "onCodeSent:$token")
        this.verificationId = verificationId!!
        binding.submit.visibility = View.VISIBLE
        binding.progress.visibility = View.GONE
    }

    fun sendVerificationToServer() {
        var params = JsonObject();
        params.addProperty("phone_number", phoneNumber)
        call = restService.sendFBUserNumber(params, UrlController.AddHeaders(this))
        call.enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    try {
                        var res = JSONObject(response.body()!!.string())
                        if (res.getBoolean("success")) {
                            showDialog()
                        }

                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                }else{
                    binding.progress.visibility = View.GONE
                    binding.tryAgain.visibility = View.VISIBLE
                    Toast.makeText(applicationContext, "Failed",Toast.LENGTH_SHORT).show()
                }

            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {

            }

        })
    }

    fun showDialog() {
        val view = layoutInflater.inflate(R.layout.verified_successfully_dialog, null)
        var button = view.findViewById<Button>(R.id.ok)
        button.setText(otpModel.phoneDialogStrings.btn_confirm)
        button.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()))
        button.setOnClickListener {
            finish()
        }
        val dialog = AlertDialog.Builder(this).setView(view)
        val alert = dialog.create()
        alert.setCancelable(false)
        alert.show()
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId == android.R.id.home){
            finish()
        }
        return super.onOptionsItemSelected(item)
    }
}