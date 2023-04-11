package com.scriptsbundle.adforest.signinorup;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.auth.api.Auth;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.gson.JsonObject;
import com.hbb20.CountryCodePicker;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.LinkedIn.LinkedInAuthenticationActivity;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.signinorup.MainViewLoginFragment;
import com.scriptsbundle.adforest.signinorup.MainViewRegisterFragment;
import com.scriptsbundle.adforest.signinorup.SignUp_Fragment;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.OTPVerification;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static android.app.Activity.RESULT_OK;
import static android.content.Context.MODE_PRIVATE;


public class OTPSignInOrSignUp extends Fragment {
    Button Submit;
    EditText ed_Phone;
    private View view;
    private SettingsMain settingsMain;
    private static FragmentManager fragmentManager;
    Activity activity;
    CountryCodePicker ccp;
    RestService restService;
    Boolean isFromLogin = false;
    String usernameForPhone = "";
    String phVerifcation, otpTxt, submit, placeholderField;
    TextView txtHeading, txtSubheading;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_o_t_p_sign_in_or_sign_up, container, false);
        adforest_initializeView();
        return view;
    }

    private void adforest_initializeView() {
        fragmentManager = getActivity().getSupportFragmentManager();
        activity = getActivity();
        settingsMain = new SettingsMain(activity);
        restService = UrlController.createService(RestService.class);

        Submit = view.findViewById(R.id.btnSubmit);
        ed_Phone = view.findViewById(R.id.ed_PhoneNumber);
        txtHeading = view.findViewById(R.id.txt_welcomeHeading);
        txtSubheading = view.findViewById(R.id.txt_sub_Heading);
//        ccp = view.findViewById(R.id.ccp);
//        ccp.getSelectedCountryCode();
//        ed_Phone.setText("+" + ccp.getSelectedCountryCode());

        Drawable drawable = getResources().getDrawable(R.drawable.ed_otp_border).mutate();
        drawable.setColorFilter(Color.parseColor(SettingsMain.getMainColor()), PorterDuff.Mode.SRC_ATOP);
        Submit.setBackground(drawable);
        Bundle bundle = this.getArguments();
        if (bundle != null) {
            isFromLogin = bundle.getBoolean("login");
            phVerifcation = bundle.getString("phonetxt");
            otpTxt = bundle.getString("otpTxt");
            submit = bundle.getString("submit");
            placeholderField = bundle.getString("placeHolder");

            Log.d("chalo", String.valueOf(isFromLogin));
        }
        Submit.setText(submit);
        txtHeading.setText(phVerifcation);
        txtSubheading.setText(otpTxt);
        ed_Phone.setHint(placeholderField);

        Submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (isFromLogin) {
                    checkUniqueLoginUserPhone();
                } else {
                    checkUniqueUserPhone();
                }

            }
        });
    }

    @Override
    public void onActivityCreated(@Nullable Bundle savedInstanceState) {
        super.onActivityCreated(savedInstanceState);
        this.getView().setFocusableInTouchMode(true);
        this.getView().requestFocus();
        this.getView().setOnKeyListener(new View.OnKeyListener() {
            @Override
            public boolean onKey(View v, int keyCode, KeyEvent event) {

//This is the filter
                if (event.getAction() != KeyEvent.ACTION_DOWN)
                    return true;
                switch (keyCode) {
                    case KeyEvent.KEYCODE_BACK:
//                        backPressed();
                        if (isFromLogin) {
                            MainViewLoginFragment mainViewRegisterFragment = new MainViewLoginFragment();
                            FragmentTransaction fragmentTransaction = getFragmentManager().beginTransaction();
                            fragmentTransaction.replace(R.id.frameContainer, mainViewRegisterFragment).addToBackStack(null).commit();
                            activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);

                        } else {
                            MainViewRegisterFragment mainViewRegisterFragment = new MainViewRegisterFragment();
                            FragmentTransaction fragmentTransaction = getFragmentManager().beginTransaction();
                            fragmentTransaction.replace(R.id.frameContainer, mainViewRegisterFragment).addToBackStack(null).commit();
                            activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);

                        }

                        break;
                }
                return true;
            }
        });
    }

    public void checkUniqueLoginUserPhone() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {


            JsonObject params = new JsonObject();
            params.addProperty("phone", ed_Phone.getText().toString());
            Call<ResponseBody> myCall = restService.checkUniquePhoneLogin(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {

                    if (response.isSuccessful()) {
                        try {
                            JSONObject jsonObject = new JSONObject(response.body().string());
                            if (jsonObject.getBoolean("success")) {
                                usernameForPhone = jsonObject.getJSONObject("data").getString("user_login");
                                Intent i = new Intent(activity, OTPVerification.class);
                                i.putExtra("phone", ed_Phone.getText().toString());
                                i.putExtra("calledFromAuth", true);
                                startActivityForResult(i, 311);
                            } else {
                                Toast.makeText(activity, jsonObject.getString("message"), Toast.LENGTH_SHORT).show();
                            }

                        } catch (JSONException | IOException e) {
                            e.printStackTrace();
                        }
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {

                }
            });
        }
    }

    public void checkUniqueUserPhone() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {


            JsonObject params = new JsonObject();
            params.addProperty("phone", ed_Phone.getText().toString());
            Log.d(" infor params", params.toString());
            Call<ResponseBody> myCall = restService.checkUniquePhone(params, UrlController.AddHeaders(getActivity()));
            Log.d(" infor mycall", myCall.toString());
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {

                    if (response.isSuccessful()) {
                        try {
                            JSONObject jsonObject = new JSONObject(response.body().string());
                            Log.d(" info Json", jsonObject.toString());
                            if (jsonObject.getBoolean("success")) {

                                Intent i = new Intent(activity, OTPVerification.class);
                                i.putExtra("phone", ed_Phone.getText().toString());
                                i.putExtra("calledFromAuth", true);
                                startActivityForResult(i, 311);
                            } else {
                                Toast.makeText(activity, jsonObject.getString("message"), Toast.LENGTH_SHORT).show();
                            }

                        } catch (JSONException | IOException e) {
                            e.printStackTrace();
                        }
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {

                }
            });
        }
    }

    public void registerUser() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {


            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("otp", "true");
            editor.apply();
            JsonObject params = new JsonObject();
            params.addProperty("phone", ed_Phone.getText().toString());
            restService = UrlController.createService(RestService.class);
            Call<ResponseBody> myCall = restService.registerOTPUser(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {

                    if (response.isSuccessful()) {
                        try {
                            JSONObject jsonObject = new JSONObject(response.body().string());
                            if (jsonObject.getBoolean("success")) {
                                Log.d("info SignUp Data", "" + jsonObject.getJSONObject("data"));
                                Toast.makeText(getActivity(), jsonObject.get("message").toString(), Toast.LENGTH_SHORT).show();
                                settingsMain.setUserLogin(jsonObject.getJSONObject("data").getString("id"));
                                settingsMain.setUserImage(jsonObject.getJSONObject("data").getString("profile_img"));
                                settingsMain.setUserEmail(jsonObject.getJSONObject("data").getString("user_login"));
                                settingsMain.setUserPassword("1122");
                                settingsMain.setUserName(jsonObject.getJSONObject("data").getString("display_name"));
                                settingsMain.isAppOpen(false);
                                Intent intent = new Intent(getActivity(), HomeActivity.class);
                                startActivity(intent);
                                activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                activity.finish();
                            } else {
                                Toast.makeText(activity, jsonObject.getString("message"), Toast.LENGTH_SHORT).show();
                            }

                        } catch (JSONException | IOException e) {
                            e.printStackTrace();
                        }
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {

                }
            });
        }
    }

    public void loginUser() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            JsonObject params = new JsonObject();
            params.addProperty("name", usernameForPhone);
            params.addProperty("phone", ed_Phone.getText().toString());
            Call<ResponseBody> myCall = restService.loginOTPUser(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info LoginPost responce", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                                editor.putString("otp", "true");
                                editor.apply();

                                Log.d("info Login Post", "" + response.getJSONObject("data"));
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                                settingsMain.setUserLogin(response.getJSONObject("data").getString("id"));
                                settingsMain.setUserImage(response.getJSONObject("data").getString("profile_img"));
                                settingsMain.setUserName(response.getJSONObject("data").getString("display_name"));
                                settingsMain.setUserPhone(response.getJSONObject("data").getString("user_email"));
                                settingsMain.setUserEmail(response.getJSONObject("data").getString("user_login"));
                                settingsMain.setUserPassword("1122");
                                settingsMain.isAppOpen(false);
                                Intent intent = new Intent(getActivity(), HomeActivity.class);
                                startActivity(intent);
                                activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                                activity.finish();

                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }


                    } catch (JSONException e) {

                        e.printStackTrace();
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {

                }
            });
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, @Nullable @org.jetbrains.annotations.Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 311) {
            if (resultCode == RESULT_OK) {
                String status = data.getStringExtra("status");
                if (status.equals("verified")) {
                    if (isFromLogin) {
                        loginUser();
                    } else {
                        registerUser();
                    }
                }
            }
        }
    }
}