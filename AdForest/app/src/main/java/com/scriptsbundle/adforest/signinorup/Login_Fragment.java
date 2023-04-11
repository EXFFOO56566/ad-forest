package com.scriptsbundle.adforest.signinorup;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.ColorStateList;
import android.content.res.XmlResourceParser;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffColorFilter;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AlertDialog;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.util.Patterns;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.facebook.AccessToken;
import com.facebook.AccessTokenTracker;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.FacebookSdk;
import com.facebook.GraphRequest;
import com.facebook.GraphResponse;
import com.facebook.LoggingBehavior;
import com.facebook.login.LoginManager;
import com.facebook.login.LoginResult;
import com.facebook.shimmer.ShimmerFrameLayout;

import com.google.android.gms.auth.api.Auth;
import com.google.android.gms.auth.api.signin.GoogleSignInAccount;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.gms.auth.api.signin.GoogleSignInResult;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.common.api.OptionalPendingResult;

import com.google.gson.JsonObject;

import com.scriptsbundle.adforest.LinkedIn.LinkedInAuthenticationActivity;
import com.scriptsbundle.adforest.LinkedIn.LinkedInBuilder;
import com.scriptsbundle.adforest.LinkedIn.helpers.LinkedInUser;

import com.hbb20.CountryCodePicker;
import com.squareup.picasso.Picasso;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.Arrays;
import java.util.Random;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.profile.FragmentProfile;
import com.scriptsbundle.adforest.profile.Model.OTPModel;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.OTPVerification;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static android.app.Activity.RESULT_OK;
import static android.content.Context.MODE_PRIVATE;
import static android.widget.Toast.LENGTH_SHORT;
import static com.scriptsbundle.adforest.R.anim;
import static com.scriptsbundle.adforest.R.drawable;
import static com.scriptsbundle.adforest.R.id;

public class Login_Fragment extends Fragment implements OnClickListener,
        GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener {

    public static final int RC_SIGN_IN = 0;
    protected static String user_email;
    private static Animation shakeAnimation;
    private static FragmentManager fragmentManager;
    View view;
    Activity activity;
    EditText emailid, password;
    Button loginButton, fbloginButton, gmailLoginButton, linkedinLoginButton;
    //    private SignInButton gmailLoginButton;
    TextView forgotPassword, signUp, startExplore;
    LinearLayout loginLayout;
    SettingsMain settingsMain;
    LinearLayout linearLayoutLogo, passwordLayout;
    ImageView imageViewLogo, showPassword;
    TextView textViewWelcome, textViewOR;
    LinearLayout guestLayout;
    LinearLayout leftSideAttributLayout, linkedinLayout;
    boolean is_verify_on = false;
    private boolean mIntentInProgress = true;
    private CallbackManager callbackManager;
    private GoogleApiClient mGoogleApiClient;
    boolean back_pressed = false;
    String clientId, redirectUri, scope;
    private String state, linedinText;
    static final String STATE = "state";
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    NestedScrollView nestedScroll;
    RestService restService;
    String usernameForPhone = "";
    CountryCodePicker ccp;
    public Login_Fragment() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        view = inflater.inflate(R.layout.login_layout, container, false);
        activity = getActivity();
        settingsMain = new SettingsMain(activity);

        restService =
                UrlController.createService(RestService.class);
        adforest_initViews();
        adforest_setDataToViews();
        fbSetup();
        setListeners();

        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestEmail()
                .build();

        mGoogleApiClient = new GoogleApiClient.Builder(getActivity())
                .addOnConnectionFailedListener(this)
                .addConnectionCallbacks(this)
                .addApi(Auth.GOOGLE_SIGN_IN_API, gso)
                .build();
        return view;
    }

    // Initiate Views
    @SuppressLint("LongLogTag")
    private void adforest_initViews() {
        fragmentManager = getActivity().getSupportFragmentManager();
        nestedScroll = view.findViewById(R.id.nestedScroll);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        emailid = view.findViewById(id.login_emailid);
        passwordLayout = view.findViewById(R.id.passwordLayout);
        password = view.findViewById(id.login_password);
        showPassword = view.findViewById(R.id.showPwd);
        loginButton = view.findViewById(id.loginBtn);
        forgotPassword = view.findViewById(id.forgot_password);
        signUp = view.findViewById(id.createAccount);
        fbloginButton = view.findViewById(id.fbLogin);
        gmailLoginButton = view.findViewById(id.gmailLogin);
        fbloginButton.setVisibility(View.INVISIBLE);
        gmailLoginButton.setVisibility(View.INVISIBLE);
        loginLayout = view.findViewById(id.login_layout);
        startExplore = view.findViewById(id.startExplore);
        guestLayout = view.findViewById(R.id.guestLayout);
        textViewOR = view.findViewById(id.or);
        textViewWelcome = view.findViewById(id.welcomeTV);
        imageViewLogo = view.findViewById(id.logoimage);
        linearLayoutLogo = view.findViewById(id.logo);
        linkedinLoginButton = view.findViewById(id.linkedin);
        linkedinLoginButton.setVisibility(View.GONE);
        ccp = view.findViewById(R.id.ccp);
//        emailid.setText("+"+ccp.getSelectedCountryCode());
        linedinText = settingsMain.getLinkednText();
//        linkedinLoginButton.setText(linedinText);
        linearLayoutLogo.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
        startExplore.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
        startExplore.setBackground(CustomBorderDrawable.customButton(3, 3, 3, 3, SettingsMain.getMainColor(), "#00000000", SettingsMain.getMainColor(), 2));

        loginButton.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
        loginButton.setBackground(CustomBorderDrawable.customButton(3, 3, 3, 3, SettingsMain.getMainColor(), "#00000000", SettingsMain.getMainColor(), 2));

        showPassword.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (password.getInputType() == 129) {
                    showPassword.setColorFilter(Color.parseColor(SettingsMain.getMainColor()), PorterDuff.Mode.SRC_IN);
                    password.setInputType(InputType.TYPE_CLASS_TEXT);
                } else {
                    showPassword.setColorFilter(new PorterDuffColorFilter(getResources().getColor(R.color.white_greyish), PorterDuff.Mode.SRC_IN));
                    password.setInputType(129);
                }
            }
        });
        // Load ShakeAnimation
        shakeAnimation = AnimationUtils.loadAnimation(getActivity(),
                anim.shake);

// Setting text selector over textviews
        @SuppressWarnings("ResourceType") XmlResourceParser xrp = getResources().getXml(R.drawable.text_selector);
        try {
            @SuppressWarnings("deprecation") ColorStateList csl = ColorStateList.createFromXml(getResources(),
                    xrp);

            forgotPassword.setTextColor(csl);
            signUp.setTextColor(csl);
        } catch (Exception ignored) {
        }
        leftSideAttributLayout = view.findViewById(id.btnLL);
//        linkedinLayout = view.findViewById(id.btnL);
        leftSideAttributLayout.removeAllViews();
        leftSideAttributLayout.setVisibility(View.GONE);
        textViewOR.setVisibility(View.GONE);
//OldCOde
//        if (settingsMain.getfbButn()) {
//            fbloginButton.setVisibility(View.VISIBLE);
//            leftSideAttributLayout.addView(fbloginButton);
//        }
//        if (settingsMain.getLinkedinButn()) {
//            linkedinLoginButton.setVisibility(View.VISIBLE);
//            leftSideAttributLayout.addView(linkedinLoginButton);
//        }
//        if (settingsMain.getGooglButn()) {
//            gmailLoginButton.setVisibility(View.VISIBLE);
//            leftSideAttributLayout.addView(gmailLoginButton);
//        }
//        if (!settingsMain.getfbButn() && !settingsMain.getGooglButn() && !settingsMain.getLinkedinButn()) {
//            textViewOR.setVisibility(View.GONE);
//            leftSideAttributLayout.setVisibility(View.GONE);

//
//        }

//        emailid.setOnFocusChangeListener(new View.OnFocusChangeListener() {
//            @Override
//            public void onFocusChange(View v, boolean hasFocus) {
//                if (!hasFocus) {
//                    if (Patterns.EMAIL_ADDRESS.matcher(emailid.getText().toString()).matches() ||
//                            Patterns.PHONE.matcher(emailid.getText().toString()).matches()) {
//                        if (Patterns.EMAIL_ADDRESS.matcher(emailid.getText().toString()).matches()) {
//                            passwordLayout.setVisibility(View.VISIBLE);
//                        } else if (Patterns.PHONE.matcher(emailid.getText().toString()).matches()) {
//                            passwordLayout.setVisibility(View.GONE);
//                        }
//                    }
//                }
//            }
//        });
//        emailid.addTextChangedListener(new TextWatcher() {
//            @Override
//            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
//
//            }
//
//            @Override
//            public void onTextChanged(CharSequence s, int start, int before, int count) {
//
//            }
//
//            @Override
//            public void afterTextChanged(Editable s) {
//                if (Patterns.EMAIL_ADDRESS.matcher(s.toString()).matches()) {
//                    passwordLayout.setVisibility(View.VISIBLE);
//                } else if (Patterns.PHONE.matcher(s.toString()).matches()) {
//                    passwordLayout.setVisibility(View.GONE);
//                } else {
//                    passwordLayout.setVisibility(View.GONE);
//                }
//            }
//        });


    }


    void adforest_setDataToViews() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            adforest_getLoginViews();
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }

    }

    public void adforest_getLoginViews() {
        RestService restService =
                UrlController.createService(RestService.class);
        Call<ResponseBody> myCall = restService.getLoginView(UrlController.AddHeaders(getActivity()));
        myCall.enqueue(new Callback<ResponseBody>() {
            @RequiresApi(api = Build.VERSION_CODES.O)
            @Override
            public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                try {
                    if (responseObj.isSuccessful()) {
                        Log.d("info Login responce", "" + responseObj.toString());

                        JSONObject response = new JSONObject(responseObj.body().string());
                        if (response.getBoolean("success")) {
                            nestedScroll.setVisibility(View.VISIBLE);
                            Log.d("info Login object", "" + response.getJSONObject("data"));

                            if (!response.getJSONObject("data").getString("logo").equals("")) {
                                Picasso.get().load(response.getJSONObject("data").getString("logo"))
                                        .error(R.drawable.logo)
                                        .placeholder(drawable.logo)
                                        .into(imageViewLogo);
                                settingsMain.setAppLogo(response.getJSONObject("data").getString("logo"));
                            }
                            textViewOR.setText(response.getJSONObject("data").getString("separator"));
                            textViewWelcome.setText(response.getJSONObject("data").getString("heading"));
                            password.setHint(response.getJSONObject("data").getString("password_placeholder"));
                            OTPModel otpModel = OTPModel.getInstance();
                            emailid.setHint(response.getJSONObject("data").getString("email_placeholder"));
                            loginButton.setText(response.getJSONObject("data").getString("form_btn"));
//                            fbloginButton.setText(response.getJSONObject("data").getString("facebook_btn"));
//                            gmailLoginButton.setText(response.getJSONObject("data").getString("google_btn"));

                            signUp.setText(response.getJSONObject("data").getString("register_text"));
                            forgotPassword.setText(response.getJSONObject("data").getString("forgot_text"));

                            is_verify_on = response.getJSONObject("data").getBoolean("is_verify_on");
                            shimmerFrameLayout.stopShimmer();
                            shimmerFrameLayout.setVisibility(View.GONE);
                            loadingLayout.setVisibility(View.GONE);
                            if (settingsMain.getAppOpen()) {
                                guestLayout.setVisibility(View.VISIBLE);
                                startExplore.setVisibility(View.VISIBLE);
                                startExplore.setText(response.getJSONObject("data").getString("guest_login"));
                                settingsMain.setUserName(response.getJSONObject("data").getString("guest_text"));
                            }
                        } else {
                            Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                        }
                    }
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                } catch (JSONException e) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    e.printStackTrace();
                } catch (IOException e) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(Call<ResponseBody> call, Throwable t) {
                shimmerFrameLayout.stopShimmer();
                shimmerFrameLayout.setVisibility(View.GONE);
                loadingLayout.setVisibility(View.GONE);
                Log.d("info Login error", String.valueOf(t));
                Log.d("info Login error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
            }
        });
    }

    // Set Listeners
    private void setListeners() {
        loginButton.setOnClickListener(this);
        fbloginButton.setOnClickListener(this);
        forgotPassword.setOnClickListener(this);
        signUp.setOnClickListener(this);
        gmailLoginButton.setOnClickListener(this);
        startExplore.setOnClickListener(this);
        linkedinLoginButton.setOnClickListener(this);
    }

    boolean loginWithOTP;

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case id.fbLogin:
                loginToFacebook();
                break;
            case id.loginBtn:
                adforest_checkValidation();
                InputMethodManager inputManager = (InputMethodManager) activity.getSystemService(Context.INPUT_METHOD_SERVICE);
                inputManager.hideSoftInputFromWindow(v.getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
                break;

            case id.forgot_password:

                // Replace forgot password fragment with animation
                fragmentManager
                        .beginTransaction()
                        .setCustomAnimations(anim.right_enter, anim.left_out)
                        .replace(id.frameContainer,
                                new ForgotPassword_Fragment(),
                                Utils.ForgotPassword_Fragment).commit();
                break;
            case id.createAccount:

                // Replace signup frgament with animation
                fragmentManager
                        .beginTransaction()
                        .setCustomAnimations(anim.right_enter, anim.left_out)
                        .replace(id.frameContainer, new MainViewRegisterFragment(),
                                Utils.MainViewRegisterFragment).commit();
                break;
            case id.linkedin:
                Intent i = new Intent(getActivity(), LinkedInAuthenticationActivity.class);
                i.putExtra("client_id", UrlController.LINKEDIN_CLIENT_ID);
                i.putExtra("client_secret", UrlController.LINKEDIN_CLIENT_SECRET);
                i.putExtra("redirect_uri", UrlController.LINKEDIN_REDIRECT_URL);
                generateState(i);
                startActivityForResult(i, 25);
                break;
            case id.gmailLogin:
                signIn();
                break;

            case id.startExplore:
                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                editor.putString("isSocial", "false");
                editor.apply();
                Intent intent = new Intent(getActivity(), HomeActivity.class);
                startActivity(intent);
                activity.overridePendingTransition(anim.right_enter, anim.left_out);
                activity.finish();
                settingsMain.setUserEmail("");
                settingsMain.setUserImage("");
                break;

        }
    }


    public void checkUniqueUserPhone() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("phone", emailid.getText().toString());
            Call<ResponseBody> myCall = restService.checkUniquePhoneLogin(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                    loadingLayout.setVisibility(View.GONE);
                    shimmerFrameLayout.setVisibility(View.GONE);
                    nestedScroll.setVisibility(View.VISIBLE);
                    if (response.isSuccessful()) {
                        try {
                            JSONObject jsonObject = new JSONObject(response.body().string());
                            if (jsonObject.getBoolean("success")) {
                                usernameForPhone = jsonObject.getJSONObject("data").getString("user_login");
                                Intent i = new Intent(activity, OTPVerification.class);
                                i.putExtra("phone", emailid.getText().toString());
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
                    loadingLayout.setVisibility(View.GONE);
                    shimmerFrameLayout.setVisibility(View.GONE);
                    nestedScroll.setVisibility(View.VISIBLE);
                }
            });
        }
    }

    public boolean checkValidationForOTP() {
        if (!emailid.getText().toString().isEmpty()) {
            if (Patterns.EMAIL_ADDRESS.matcher(emailid.getText().toString()).matches()) {
                if (password.getText().toString().equals("")) {
                    password.setError("!");
                    return false;
                }
                loginWithOTP = false;
            } else if (Patterns.PHONE.matcher(emailid.getText().toString()).matches()) {
                loginWithOTP = true;
            } else {
                emailid.setError("!");
                return false;
            }
        } else {
            emailid.setError("!");
            emailid.requestFocus();
            return false;
        }

        return true;
    }

    private void generateState(Intent intent) {
        String ALLOWED_CHARACTERS = "0123456789qwertyuiopasdfghjklzxcvbnmMNBVCXZLKJHGFDSAQWERTYUIOP";
        final Random random = new Random();
        final StringBuilder sb = new StringBuilder(16);
        for (int i = 0; i < 16; ++i)
            sb.append(ALLOWED_CHARACTERS.charAt(random.nextInt(ALLOWED_CHARACTERS.length())));
        this.state = sb.toString();
        intent.putExtra(STATE, state);
    }

    // Check Validation before adforest_login
    private void adforest_checkValidation() {
        // Get email id and password
        String getEmailId = emailid.getText().toString();
        String getPassword = password.getText().toString();

        // Check patter for email id
        Pattern p = Pattern.compile(Utils.regEx);

        Matcher m = p.matcher(getEmailId);

        // Check for both field is empty or not
        if (getEmailId.equals("") && getEmailId.length() == 0) {
            if (getPassword.equals("") && getPassword.length() == 0) {
                loginLayout.startAnimation(shakeAnimation);
                emailid.setError("!");
                password.setError("!");
            }
        } else if (getEmailId.equals("") && getEmailId.length() == 0) {
            loginLayout.startAnimation(shakeAnimation);
            emailid.requestFocus();
            emailid.setError("!");
        } else if (getPassword.equals("") && getPassword.length() == 0) {
            loginLayout.startAnimation(shakeAnimation);
            password.setError("!");
            password.requestFocus();
        }
        // Check if email id is valid or not
        else if (!m.find()) {
            emailid.requestFocus();
            emailid.setError("!");
        }
        // Else do adforest_login and do your stuff
        else {
            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("isSocial", "false");
            editor.apply();
            adforest_login(getEmailId, getPassword);
        }

    }

    private void adforest_login(final String email, final String pswd) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            Log.d("enteries are", "email=" + email + " passwrd =" + pswd);
            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);

            JsonObject params = new JsonObject();
            params.addProperty("email", email);
            params.addProperty("password", pswd);


            restService =
                    UrlController.createServiceNoTimeoutUP(RestService.class,email,pswd,getActivity());
            Call<ResponseBody> myCall = restService.postLogin(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {

                            Log.d("info LoginPost responce", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                nestedScroll.setVisibility(View.VISIBLE);
                                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                                editor.putString("otp", "false");
                                editor.apply();
                                if (is_verify_on && !response.getJSONObject("data").getBoolean("is_account_confirm")) {
                                    Log.d("info Login Resp", "" + response.getJSONObject("data"));
                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_LONG).show();
                                    Utils.user_id = response.getJSONObject("data").getString("id");
                                    Handler handler = new Handler();
                                    handler.postDelayed(() -> fragmentManager
                                            .beginTransaction()
                                            .setCustomAnimations(anim.right_enter, anim.left_out)
                                            .replace(id.frameContainer,
                                                    new VerifyAccount_Fragment(),
                                                    Utils.VerifyAccount_Fragment).commit(), 1000);

                                } else {
                                    Log.d("info Login Post", "" + response.getJSONObject("data"));
                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                                    settingsMain.setUserLogin(response.getJSONObject("data").getString("id"));
                                    settingsMain.setUserImage(response.getJSONObject("data").getString("profile_img"));
                                    settingsMain.setUserName(response.getJSONObject("data").getString("display_name"));
                                    settingsMain.setUserPhone(response.getJSONObject("data").getString("phone"));
                                    settingsMain.setUserEmail(email);
                                    settingsMain.setUserPassword(pswd);
                                    settingsMain.isAppOpen(false);
                                    Intent intent = new Intent(getActivity(), HomeActivity.class);
                                    startActivity(intent);
                                    activity.overridePendingTransition(anim.right_enter, anim.left_out);
                                    activity.finish();
                                }
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    nestedScroll.setVisibility(View.VISIBLE);

                    Log.d("info LoginPost error", String.valueOf(t));
                    Log.d("info LoginPost error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            nestedScroll.setVisibility(View.VISIBLE);

            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }

    }

    private void loginToFacebook() {

        if (SettingsMain.isConnectingToInternet(activity)) {
            LoginManager.getInstance().logInWithReadPermissions(this,
                    Arrays.asList("public_profile", "email"));
        } else {
            Toast.makeText(activity, "Sorry .No internet connectivity found.",
                    Toast.LENGTH_LONG).show();
        }
    }

    public void getFBStats(AccessToken accessToken) {
        GraphRequest request = GraphRequest.newMeRequest(
                accessToken,
                new GraphRequest.GraphJSONObjectCallback() {
                    @Override
                    public void onCompleted(JSONObject object, GraphResponse response) {
                        try {
                            Log.i("tag", "Obj " + object.toString());
                            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                            editor.putString("isSocial", "true");
                            editor.apply();


                            adforest_loginSocialMedia(object.getString("email"), null);

                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                });

        Bundle parameters = new Bundle();
        parameters.putString("fields",
                "id,first_name,last_name,email,gender, birthday");
        request.setParameters(parameters);
        request.executeAsync();
    }

    // FB SETUP CALLS
    private void fbSetup() {
        //noinspection deprecation
        FacebookSdk.sdkInitialize(activity.getApplicationContext());
        FacebookSdk.addLoggingBehavior(LoggingBehavior.REQUESTS);
        callbackManager = CallbackManager.Factory.create();
        new AccessTokenTracker() {

            @Override
            protected void onCurrentAccessTokenChanged(
                    AccessToken oldAccessToken, AccessToken currentAccessToken) {
                if (currentAccessToken != null) {
                    Log.i("tag", "In From ONcreate");
                    Log.i("tag", "go to home");
                } else {
                    Log.i("tag", "Else In From ONcreate");
                    Log.i("tag", "Goto splash");
                }
            }
        };

        LoginManager.getInstance().registerCallback(callbackManager,
                new FacebookCallback<LoginResult>() {

                    @Override
                    public void onSuccess(LoginResult result) {
                        // TODO Auto-generated method stub
                        Log.i("tag", "Success ");
                        getFBStats(result.getAccessToken());
                    }

                    @Override
                    public void onCancel() {
                        // TODO Auto-generated method stub
                        Log.i("tag", "On Cancel ");
                    }

                    @Override
                    public void onError(FacebookException error) {
                        // TODO Auto-generated method stub
                        Log.i("tag", "Error " + error);
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
                        MainViewLoginFragment mainViewLoginFragment = new MainViewLoginFragment();
                        FragmentTransaction fragmentTransaction = getFragmentManager().beginTransaction();
                        fragmentTransaction.replace(R.id.frameContainer, mainViewLoginFragment).addToBackStack(null).commit();
                        activity.overridePendingTransition(anim.right_enter, anim.left_out);

                        break;
                }
                return true;
            }
        });
    }

    private void backPressed() {
        if (!back_pressed) {
            Toast.makeText(getContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
            back_pressed = true;
            android.os.Handler mHandler = new android.os.Handler();
            mHandler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    back_pressed = false;
                }
            }, 2000L);
        } else {

            //Alert dialog for exit form login screen

            AlertDialog.Builder alert = new AlertDialog.Builder(getContext());
            alert.setTitle(settingsMain.getAlertDialogTitle("info"));
            alert.setCancelable(false);
            alert.setMessage(settingsMain.getExitApp("exit"));
            alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                getActivity().finishAffinity();
                dialog.dismiss();
            });
            alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
            alert.show();
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        callbackManager.onActivityResult(requestCode, resultCode, data);
        if (requestCode == RC_SIGN_IN) {
            GoogleSignInResult result = Auth.GoogleSignInApi.getSignInResultFromIntent(data);
            handleSignInResult(result);
        }
        if (requestCode == 25 && data != null) {
            if (resultCode == RESULT_OK) {

                //Successfully signed in
                LinkedInUser user = data.getParcelableExtra("social_login");

                //acessing user info
                Log.i("LinkedInLogin", user.getId());
                Log.i("LinkedInLogin", user.getFirstName());
                Log.i("LinkedInLogin", user.getLastName());
                Log.i("LinkedInLogin", user.getAccessToken());
                Log.i("LinkedInLogin", user.getProfileUrl());
                Log.i("LinkedInLogin", user.getEmail());

                //Passing User Email to login with social button LinkedIn.
                String email = user.getEmail();
                String profile_img = user.getProfileUrl();
                Log.d("emailLinkedIn", email);
                Log.d("profileLinkedIn", user.getProfileUrl());
                settingsMain.setLinkedinLogin(true);
                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                editor.putString("isSocial", "true");
                editor.apply();
                adforest_loginSocialMedia(email, profile_img);


            } else {

                if (data.getIntExtra("err_code", 0) == LinkedInBuilder.ERROR_USER_DENIED) {
                    //Handle : user denied access to account

                } else if (data.getIntExtra("err_code", 0) == LinkedInBuilder.ERROR_FAILED) {

                    //Handle : Error in API : see logcat output for details
                    Log.e("LINKEDIN ERROR", data.getStringExtra("err_message"));
                }
            }
        }
        if (requestCode == 311) {
            if (resultCode == RESULT_OK) {
                String status = data.getStringExtra("status");
                if (status.equals("verified")) {
                    loginUser();
                }
            }
        }

    }


    public void loginUser() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("name",usernameForPhone);
            params.addProperty("phone", emailid.getText().toString());
            Call<ResponseBody> myCall = restService.loginOTPUser(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info LoginPost responce", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                nestedScroll.setVisibility(View.VISIBLE);

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
                                activity.overridePendingTransition(anim.right_enter, anim.left_out);
                                activity.finish();

                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);

                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {

                }
            });
        }
    }

    private void handleSignInResult(GoogleSignInResult result) {
        Log.d("", "handleSignInResult:" + result.isSuccess());
        if (result.isSuccess()) {
            // Signed in successfully, show authenticated UI.
            GoogleSignInAccount acct = result.getSignInAccount();

            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("isSocial", "true");
            editor.apply();

            adforest_loginSocialMedia(acct != null ? acct.getEmail() : null, null);

        } else {
            Auth.GoogleSignInApi.signOut(mGoogleApiClient).setResultCallback(
                    status -> {
                    });
        }
    }

    @Override
    public void onConnected(Bundle bundle) {
    }

    @Override
    public void onConnectionSuspended(int i) {
        mGoogleApiClient.connect();
    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {

    }

    @Override
    public void onStart() {
        super.onStart();
        mGoogleApiClient.connect();
    }

    @Override
    public void onResume() {
//        if (settingsMain.getAnalyticsShow() && !settingsMain.getAnalyticsId().equals(""))
//            AnalyticsTrackers.getInstance().trackScreenView("Login");

        super.onResume();
    }


    @Override
    public void onStop() {
        super.onStop();
        if (mGoogleApiClient.isConnected()) {
            mGoogleApiClient.disconnect();
        }
    }

    private void signIn() {

        if (mIntentInProgress) {
            Intent signInIntent = Auth.GoogleSignInApi.getSignInIntent(mGoogleApiClient);
            startActivityForResult(signInIntent, RC_SIGN_IN);
            mIntentInProgress = false;
        } else {
            OptionalPendingResult<GoogleSignInResult> opr = Auth.GoogleSignInApi.silentSignIn(mGoogleApiClient);
            if (opr.isDone()) {
                Log.d("s", "Got cached sign-in");
                GoogleSignInResult result = opr.get();
                handleSignInResult(result);
            } else {
                // If the user has not previously signed in on this device or the sign-in has expired,
                // this asynchronous branch will attempt to sign in the user silently.  Cross-device
                // single sign-on will occur in this branch.
                opr.setResultCallback(googleSignInResult -> handleSignInResult(googleSignInResult));
            }
        }
    }


    private void adforest_loginSocialMedia(final String email, String profileImg) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("LinkedIn_img", profileImg);
            params.addProperty("email", email);
            params.addProperty("type", "social");
            Log.d("paramSocial", params.toString());

            RestService restService =
                    UrlController.createService(RestService.class, email, "1122", getActivity());
            Call<ResponseBody> myCall = restService.postLogin(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            nestedScroll.setVisibility(View.VISIBLE);
                            Log.d("info LoginScoial respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                Log.d("info LoginScoial", "" + response.getJSONObject("data"));
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                                settingsMain.setUserLogin(response.getJSONObject("data").getString("id"));
                                settingsMain.setUserImage(response.getJSONObject("data").getString("profile_img"));
                                settingsMain.setUserName(response.getJSONObject("data").getString("display_name"));
                                settingsMain.setUserPhone(response.getJSONObject("data").getString("phone"));
                                settingsMain.setUserEmail(email);
                                settingsMain.setUserPassword("1122");

                                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                                editor.putString("isSocial", "true");
                                editor.apply();
                                settingsMain.isAppOpen(false);
                                Intent intent = new Intent(getActivity(), HomeActivity.class);
                                startActivity(intent);
                                activity.overridePendingTransition(anim.right_enter, anim.left_out);
                                activity.finish();
                            } else {
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                            }
                        }
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);
                    } catch (JSONException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);
                        e.printStackTrace();
                    } catch (IOException e) {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        nestedScroll.setVisibility(View.VISIBLE);
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                    nestedScroll.setVisibility(View.VISIBLE);
                    Log.d("info LoginScoial error", String.valueOf(t));
                    Log.d("info LoginScoial error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            nestedScroll.setVisibility(View.VISIBLE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }

    }

}
