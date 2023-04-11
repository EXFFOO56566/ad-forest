package com.scriptsbundle.adforest.signinorup;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.ColorStateList;
import android.content.res.XmlResourceParser;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.net.http.SslError;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.text.method.LinkMovementMethod;
import android.util.Log;
import android.util.Patterns;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.inputmethod.InputMethodManager;
import android.webkit.SslErrorHandler;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
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
import com.google.android.gms.common.api.ResultCallback;
import com.google.android.gms.common.api.Status;
import com.google.gson.JsonObject;
import com.hbb20.CountryCodePicker;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.SocketTimeoutException;
import java.util.Arrays;
import java.util.Random;
import java.util.concurrent.TimeoutException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.LinkedIn.LinkedInAuthenticationActivity;
import com.scriptsbundle.adforest.LinkedIn.LinkedInBuilder;
import com.scriptsbundle.adforest.LinkedIn.helpers.LinkedInUser;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.profile.Model.OTPModel;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.OTPVerification;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static android.app.Activity.RESULT_OK;
import static android.content.Context.MODE_PRIVATE;

public class SignUp_Fragment extends Fragment implements OnClickListener
        , GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener {

    public static final int RC_SIGN_IN = 0;
    private static FragmentManager fragmentManager;
    Activity activity;
    RestService restService;
    boolean is_verify_on = false;

    boolean subscribe_on = false;
    String term_page_id;
    LinearLayout leftSideAttributLayout, linkedinLayout;
    private boolean mIntentInProgress = true;
    private View view;
    private EditText fullName, emailId, mobileNumber,
            password;

    private TextView login, titleTV, orTV, terms_conditionsText, subscribeText, tex;
    private Button signUpButton, fbloginButton, gmailLoginButton, linkedinLoginButton;
    //    private SignInButton gmailLoginButton;
    private CheckBox terms_conditions, subscribeCheckbox;
    private CallbackManager callbackManager;
    private SettingsMain settingsMain;
    private GoogleApiClient mGoogleApiClient;
    private String state, linedinText, subscribePOSTValue;
    static final String STATE = "state";
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    NestedScrollView nestedScroll;
    boolean registerWithOTP = true;

    CountryCodePicker ccp;
//    LinearLayout otpToggle,emailToggle;
//    TextView otpToggleText,emailToggleText;
//    View otpToggleView,emailToggleView;

    public SignUp_Fragment() {

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.signup_layout, container, false);
        activity = getActivity();
        settingsMain = new SettingsMain(activity);
        restService = UrlController.createService(RestService.class);

        adforest_initViews();
        adforest_setDataToViews();
        fbSetup();
        mIntentInProgress = true;
        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestEmail()
                .build();

        mGoogleApiClient = new GoogleApiClient.Builder(getActivity())
                .addOnConnectionFailedListener(this)
                .addConnectionCallbacks(this)
                .addApi(Auth.GOOGLE_SIGN_IN_API, gso)
                .build();

        setListeners();
        return view;
    }

    // Initialize all views
    @SuppressWarnings("ResourceType")
    private void adforest_initViews() {
        fragmentManager = getActivity().getSupportFragmentManager();

//        emailToggleText = view.findViewById(R.id.emailToggleText);
//        otpToggleText = view.findViewById(R.id.otpToggleText);
//        emailToggleView = view.findViewById(R.id.emailToggleView);
//        otpToggleView = view.findViewById(R.id.otpToggleView);
//        emailToggleView.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
//        otpToggleView.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));
//        otpToggle = view.findViewById(R.id.otpToggle);
//        emailToggle = view.findViewById(R.id.emailToggle);
//        otpToggle.setOnClickListener(new OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                otpToggleText.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
//                emailToggleText.setTextColor(Color.parseColor("#cccccc"));
//                otpToggleView.setVisibility(View.VISIBLE);
//                emailToggleView.setVisibility(View.GONE);
//                emailId.setVisibility(View.GONE);
//                password.setVisibility(View.GONE);
//            }
//        });
//        emailToggle.setOnClickListener(new OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                emailToggleText.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
//                otpToggleText.setTextColor(Color.parseColor("#cccccc"));
//                emailToggleView.setVisibility(View.VISIBLE);
//                otpToggleView.setVisibility(View.GONE);
//                emailId.setVisibility(View.VISIBLE);
//                password.setVisibility(View.VISIBLE);
//            }
//        });
        nestedScroll = view.findViewById(R.id.nestedScroll);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        fullName = view.findViewById(R.id.fullName);
        emailId = view.findViewById(R.id.userEmailId);
        mobileNumber = view.findViewById(R.id.mobileNumber);
        password = view.findViewById(R.id.password);
        signUpButton = view.findViewById(R.id.signUpBtn);
        login = view.findViewById(R.id.already_user);
        subscribeCheckbox = view.findViewById(R.id.subscribeCheckbox);
        subscribeText = view.findViewById(R.id.subscribeText);

        terms_conditions = view.findViewById(R.id.terms_conditions);
        terms_conditionsText = view.findViewById(R.id.terms_conditionsText);

        fbloginButton = view.findViewById(R.id.fbLogin);
        gmailLoginButton = view.findViewById(R.id.gmailLogin);

        titleTV = view.findViewById(R.id.title);
        orTV = view.findViewById(R.id.or);
        linkedinLoginButton = view.findViewById(R.id.linkedin);
        linkedinLoginButton.setVisibility(View.INVISIBLE);
//        linedinText = settingsMain.getLinkednText();
        linkedinLoginButton.setText(linedinText);
        signUpButton.setTextColor(Color.parseColor(settingsMain.getMainColor()));
        signUpButton.setBackground(CustomBorderDrawable.customButton(3, 3, 3, 3, settingsMain.getMainColor(), "#00000000", settingsMain.getMainColor(), 2));



        // Setting text selector over textviews
        XmlResourceParser xrp = getResources().getXml(R.drawable.text_selector);
        try {
            @SuppressWarnings("deprecation") ColorStateList csl = ColorStateList.createFromXml(getResources(),
                    xrp);

            login.setTextColor(csl);
            terms_conditions.setTextColor(csl);
        } catch (Exception ignored) {
        }

        leftSideAttributLayout = view.findViewById(R.id.btnLL);
//        linkedinLayout = view.findViewById(R.id.btnL);
//        linkedinLayout.removeAllViews();
        leftSideAttributLayout.removeAllViews();
        orTV.setVisibility(View.GONE);
        leftSideAttributLayout.setVisibility(View.GONE);

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
//            orTV.setVisibility(View.GONE);
//            leftSideAttributLayout.setVisibility(View.GONE);
//        }
    }

    void adforest_setDataToViews() {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            adforest_getRegisterViews();

        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), "Internet error", Toast.LENGTH_SHORT).show();
        }

    }

    public void adforest_getRegisterViews() {
        Call<ResponseBody> myCall = restService.getRegisterView(UrlController.AddHeaders(getActivity()));
        myCall.enqueue(new Callback<ResponseBody>() {
            @Override
            public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                try {
                    if (responseObj.isSuccessful()) {
                        Log.d("info Register Responce", "" + responseObj.toString());

                        JSONObject response = new JSONObject(responseObj.body().string());
                        if (response.getBoolean("success")) {
                            nestedScroll.setVisibility(View.VISIBLE);
                            Log.d("info Register object", "" + response.getJSONObject("data"));

                            orTV.setText(response.getJSONObject("data").getString("separator"));
                            titleTV.setText(response.getJSONObject("data").getString("heading"));
                            password.setHint(response.getJSONObject("data").getString("password_placeholder"));
                            fullName.setHint(response.getJSONObject("data").getString("name_placeholder"));
                            mobileNumber.setHint(response.getJSONObject("data").getString("phone_placeholder"));
                            emailId.setHint(response.getJSONObject("data").getString("email_placeholder"));

                            signUpButton.setText(response.getJSONObject("data").getString("form_btn"));
//                            fbloginButton.setText(response.getJSONObject("data").getString("facebook_btn"));
//                            gmailLoginButton.setText(response.getJSONObject("data").getString("google_btn"));
                            login.setText(response.getJSONObject("data").getString("login_text"));
                            is_verify_on = response.getJSONObject("data").getBoolean("is_verify_on");
                            subscribeText.setText(response.getJSONObject("data").getString("subscriber_checkbox_text"));
                            subscribe_on = response.getJSONObject("data").getBoolean("subscriber_is_show");
                            if (subscribe_on) {
                                subscribeCheckbox.setVisibility(View.VISIBLE);
                                subscribeText.setVisibility(View.VISIBLE);
                            } else {
                                subscribeCheckbox.setVisibility(View.GONE);
                                subscribeText.setVisibility(View.GONE);
                            }


                            subscribePOSTValue = response.getJSONObject("data").getString("subscriber_checkbox");
                            if (!response.getJSONObject("data").getString("term_page_id").isEmpty()) {
                                term_page_id = response.getJSONObject("data").getString("term_page_id");
                                terms_conditionsText.setText(
                                        Html.fromHtml(response.getJSONObject("data").getString("terms_text")));
                                terms_conditionsText.setClickable(true);
                                terms_conditionsText.setMovementMethod(LinkMovementMethod.getInstance());
                                terms_conditionsText.setOnClickListener((View v) -> {
                                    adforest_showTermsDialog();
                                });
                            } else
                                terms_conditionsText.setText(response.getJSONObject("data").getString("terms_text"));
                            shimmerFrameLayout.stopShimmer();
                            shimmerFrameLayout.setVisibility(View.GONE);
                            loadingLayout.setVisibility(View.GONE);

                        } else {
                            Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                        }
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                } catch (IOException e) {
                    e.printStackTrace();
                }
                shimmerFrameLayout.stopShimmer();
                shimmerFrameLayout.setVisibility(View.GONE);
                loadingLayout.setVisibility(View.GONE);

            }

            @Override
            public void onFailure(Call<ResponseBody> call, Throwable t) {
                shimmerFrameLayout.stopShimmer();
                shimmerFrameLayout.setVisibility(View.GONE);
                loadingLayout.setVisibility(View.GONE);
                Log.d("info Register error", String.valueOf(t));
                Log.d("info Register error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
            }
        });
    }

    private void adforest_showTermsDialog() {

        final Dialog dialog;
        dialog = new Dialog(getActivity(), R.style.customDialog);
        dialog.setCanceledOnTouchOutside(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_termsand_condition);
        final TextView terms_conditionsTitle = dialog.findViewById(R.id.terms_conditionsTitle);
        final WebView webView = dialog.findViewById(R.id.webViewTermsAndCondition);
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                final AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
                builder.setMessage("SSL certificate is not safe!");
                builder.setPositiveButton("continue", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        handler.proceed();
                    }
                });
                builder.setNegativeButton("cancel", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        handler.cancel();
                    }
                });
                final AlertDialog dialog = builder.create();
                dialog.show();
            }
        });
        Button button = dialog.findViewById(R.id.cancel_button);
        button.setText(settingsMain.getGenericAlertCancelText());
        button.setBackgroundColor(Color.parseColor(settingsMain.getMainColor()));


        dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.parseColor("#00000000")));

        webView.setScrollContainer(false);
        WebSettings webSettings = webView.getSettings();
        webSettings.setTextSize(WebSettings.TextSize.SMALLER);

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            JsonObject params = new JsonObject();
            params.addProperty("page_id", term_page_id);
            Log.d("info Send terms id =", "" + params.toString());

            Call<ResponseBody> myCall = restService.postGetCustomePages(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info terms responce ", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());
                            if (response.getBoolean("success")) {
                                Log.d("info terms object", "" + response.getJSONObject("data"));
                                terms_conditionsTitle.setText(response.getJSONObject("data").getString("page_title"));
                                webView.loadDataWithBaseURL(null, response.getJSONObject("data").getString("page_content"), "text/html", "UTF-8", null);
                                dialog.show();
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
                    shimmerFrameLayout.stopShimmer();
                    shimmerFrameLayout.setVisibility(View.GONE);
                    loadingLayout.setVisibility(View.GONE);
                }

                @Override
                public void onFailure(Call<ResponseBody> call, Throwable t) {
                    if (t instanceof TimeoutException) {
                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);

                    }
                    if (t instanceof SocketTimeoutException || t instanceof NullPointerException) {

                        Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);

                    }
                    if (t instanceof NullPointerException || t instanceof UnknownError || t instanceof NumberFormatException) {
                        Log.d("info CustomPages ", "NullPointert Exception" + t.getLocalizedMessage());
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);

                    } else {
                        shimmerFrameLayout.stopShimmer();
                        shimmerFrameLayout.setVisibility(View.GONE);
                        loadingLayout.setVisibility(View.GONE);
                        Log.d("info CustomPages err", String.valueOf(t));
                        Log.d("info CustomPages err", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
                    }
                }
            });
        } else {
            shimmerFrameLayout.stopShimmer();
            shimmerFrameLayout.setVisibility(View.GONE);
            loadingLayout.setVisibility(View.GONE);
            Toast.makeText(getActivity(), settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
        }
        button.setOnClickListener(v -> dialog.dismiss());


    }

    // Set Listeners
    private void setListeners() {
        signUpButton.setOnClickListener(this);
        login.setOnClickListener(this);
        fbloginButton.setOnClickListener(this);
        gmailLoginButton.setOnClickListener(this);
        linkedinLoginButton.setOnClickListener(this);

    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.fbLogin:
                adforest_loginToFacebook();


                break;
            case R.id.signUpBtn:
                // Call adforest_checkValidation method
                adforest_checkValidation();
                InputMethodManager inputManager = (InputMethodManager) activity.getSystemService(Context.INPUT_METHOD_SERVICE);
                inputManager.hideSoftInputFromWindow(v.getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
                break;


            case R.id.already_user:

                // Replace login fragment
                new MainActivity().adforest_replaceLoginFragment();
                break;
            case R.id.gmailLogin:

                adforest_signInForSociel();
                Log.e("asdfsd", "display name: ");

                break;
            case R.id.linkedin:
                Intent i = new Intent(getActivity(), LinkedInAuthenticationActivity.class);
                i.putExtra("client_id", UrlController.LINKEDIN_CLIENT_ID);
                i.putExtra("client_secret", UrlController.LINKEDIN_CLIENT_SECRET);
                i.putExtra("redirect_uri", UrlController.LINKEDIN_REDIRECT_URL);
                generateState(i);
                startActivityForResult(i, 25);
                break;
        }

    }

    String generatedUsername = "";
    public void checkUniqueUserPhone() {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("phone", emailId.getText().toString());
            Call<ResponseBody> myCall = restService.checkUniquePhone(params, UrlController.AddHeaders(getActivity()));
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
                                Intent i = new Intent(activity, OTPVerification.class);
                                i.putExtra("phone", emailId.getText().toString());
                                i.putExtra("calledFromAuth",true);
                                startActivityForResult(i,311);
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

        if (!emailId.getText().toString().isEmpty()) {
            if (Patterns.EMAIL_ADDRESS.matcher(emailId.getText().toString()).matches()) {
                if (password.getText().toString().equals("")) {
                    password.setError("!");
                    return false;
                }
                registerWithOTP = false;
            } else if (Patterns.PHONE.matcher(emailId.getText().toString()).matches()) {
                registerWithOTP = true;
            } else {
                emailId.setError("!");
                return false;
            }
        } else {
            emailId.setError("!");
            emailId.requestFocus();
            return false;
        }
        if (!terms_conditions.isChecked()) {
            terms_conditions.setError("!");
            return false;
        }

        return true;
//        if (!emailId.getText().toString().equals("")) {
//            if (Patterns.EMAIL_ADDRESS.matcher(emailId.getText().toString()).matches() && otpLayout) {
//                otpLayout = false;
//                password.setVisibility(View.VISIBLE);
//                fullName.setVisibility(View.VISIBLE);
//                Toast.makeText(activity, "Registering with email requires password", Toast.LENGTH_SHORT).show();
//                return false;
//            }
//        } else {
//            Toast.makeText(activity, "Please enter valid email/phone number", Toast.LENGTH_SHORT).show();
//            return false;
//        }
//        if (fullName.getText().toString().equals("")) {
//            Toast.makeText(activity, "Please enter valid username", Toast.LENGTH_SHORT).show();
//            return false;
//        }
//
//        return true;
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

    // Check Validation Method
    private void adforest_checkValidation() {

        // Get all edittext texts
        String getFullName = fullName.getText().toString();
        String getEmailId = emailId.getText().toString();
        String getMobileNumber = mobileNumber.getText().toString();
        String getPassword = password.getText().toString();

        // Pattern match for email id
        Pattern p = Pattern.compile(Utils.regEx);
        Matcher m = p.matcher(getEmailId);

        // Check if all strings are null or not
        if (getFullName.equals("") || getFullName.length() == 0)
            fullName.setError("!");
        else if (getEmailId.equals("") || getEmailId.length() == 0)
            emailId.setError("!");
        else if (getMobileNumber.equals("") || getMobileNumber.length() == 0)
            mobileNumber.setError("!");
        else if (getPassword.equals("") || getPassword.length() == 0)
            password.setError("!");
            // Check if email id valid or not
        else if (!m.find())
            emailId.setError("!");
            // Make sure user should check Terms and Conditions checkbox
        else if (!terms_conditions.isChecked())
            terms_conditions.setError("!");
        else {
            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("isSocial", "false");
            editor.apply();
            if(subscribeCheckbox.isChecked()){
                adforest_signUp(getFullName, getEmailId, getPassword, getMobileNumber, subscribePOSTValue);
            }
            else{
                adforest_signUp(getFullName, getEmailId, getPassword, getMobileNumber, subscribePOSTValue);
            }

        }
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
                        fragmentManager
                                .beginTransaction()
                                .setCustomAnimations(R.anim.right_enter, R.anim.left_out)
                                .replace(R.id.frameContainer,
                                        new MainViewRegisterFragment(),
                                        Utils.MainViewRegisterFragment).commit();
                        break;
                }
                return true;
            }
        });
    }

    void adforest_signUp( final String name,final String email, final String pswd,final String phone, final String subscriberPostVal) {
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("name", name);
            params.addProperty("email", email);
            params.addProperty("phone", phone);
            params.addProperty("password", pswd);

            Log.d("info Register", "" + params.toString());
            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("otp", "false");
            editor.apply();
            if (subscribeCheckbox.isChecked()) {
                params.addProperty(subscriberPostVal, subscribePOSTValue);
            }
            Log.d("info Register", "" + params.toString());
            Call<ResponseBody> myCall = restService.postRegister(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            nestedScroll.setVisibility(View.VISIBLE);
                            Log.d("info SignUp Responce", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                if (is_verify_on) {
                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_LONG).show();
                                    Utils.user_id = response.getJSONObject("data").getString("id");
                                    Log.d("info SignUp Data", "" + response.getJSONObject("data"));
                                    Handler handler = new Handler();
                                    handler.postDelayed(new Runnable() {
                                        @Override
                                        public void run() {
                                            fragmentManager
                                                    .beginTransaction()
                                                    .setCustomAnimations(R.anim.right_enter, R.anim.left_out)
                                                    .replace(R.id.frameContainer,
                                                            new VerifyAccount_Fragment(),
                                                            Utils.VerifyAccount_Fragment).commit();
                                        }
                                    }, 1000);

                                } else {
                                    Log.d("info SignUp Data", "" + response.getJSONObject("data"));
                                    Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();
                                    settingsMain.setUserLogin(response.getJSONObject("data").getString("id"));
                                    settingsMain.setUserImage(response.getJSONObject("data").getString("profile_img"));
                                    settingsMain.setUserEmail(email);
                                    settingsMain.setUserPassword(pswd);
                                    settingsMain.setUserName(name);
                                    settingsMain.isAppOpen(false);
                                    Intent intent = new Intent(getActivity(), HomeActivity.class);
                                    startActivity(intent);
                                    activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
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

                    Log.d("info SignUp error", String.valueOf(t));
                    Log.d("info SignUp error", String.valueOf(t.getMessage() + t.getCause() + t.fillInStackTrace()));
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

    private void adforest_loginToFacebook() {

        if (SettingsMain.isConnectingToInternet(activity)) {
            LoginManager.getInstance().logInWithReadPermissions(this,
                    Arrays.asList("public_profile", "email"));
        } else {
            Toast.makeText(activity, "Sorry .No internet connectivity found.",
                    Toast.LENGTH_LONG).show();
        }
    }

    public void getFBStats(AccessToken accessToken) {
        // Application code
        Log.i("tag_Here", "getFb");

        GraphRequest request = GraphRequest.newMeRequest(accessToken,
                new GraphRequest.GraphJSONObjectCallback() {
                    @Override
                    public void onCompleted(JSONObject object,
                                            GraphResponse response) {
                        // Application code
                        try {
                            Log.i("tag_Here", response.toString());
                            Log.i("tag", "Obj " + object.toString());

                            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                            editor.putString("isSocial", "true");
                            editor.apply();

                            adforest_loginSocialMedia(object.getString("email"), null);

                        } catch (Exception e) {
                            // TODO Auto-generated catch block
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
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == RC_SIGN_IN) {
            GoogleSignInResult result = Auth.GoogleSignInApi.getSignInResultFromIntent(data);
            handleSignInResult(result);
        }

        callbackManager.onActivityResult(requestCode, resultCode, data);

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
                String profileUrl = user.getProfileUrl();
                Log.d("email ", email);
                Log.d("profileUrl ", profileUrl);
                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                editor.putString("isSocial", "true");
                editor.apply();

                adforest_loginSocialMedia(email, profileUrl);


            } else {

                if (data.getIntExtra("err_code", 0) == LinkedInBuilder.ERROR_USER_DENIED) {
                    //Handle : user denied access to account

                } else if (data.getIntExtra("err_code", 0) == LinkedInBuilder.ERROR_FAILED) {

                    //Handle : Error in API : see logcat output for details
                    Log.e("LINKEDIN ERROR", data.getStringExtra("err_message"));
                }
            }
        }if (requestCode==311){
            if (resultCode == RESULT_OK){
                String status = data.getStringExtra("status");
                if (status.equals("verified")){
                    registerUser();
                }
            }
        }


    }

    public void registerUser(){
        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
            editor.putString("otp", "true");
            editor.apply();
            JsonObject params = new JsonObject();
            params.addProperty("phone", emailId.getText().toString());
            restService = UrlController.createService(RestService.class);
            Call<ResponseBody> myCall = restService.registerOTPUser(params, UrlController.AddHeaders(getActivity()));
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
                    loadingLayout.setVisibility(View.GONE);
                    shimmerFrameLayout.setVisibility(View.GONE);
                    nestedScroll.setVisibility(View.VISIBLE);
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
                    new ResultCallback<Status>() {
                        @Override
                        public void onResult(@NonNull Status status) {
                        }
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
//            AnalyticsTrackers.getInstance().trackScreenView("Sign Up");
        super.onResume();
    }

    @Override
    public void onStop() {
        super.onStop();
        if (mGoogleApiClient.isConnected()) {
            mGoogleApiClient.disconnect();
        }
    }

    private void adforest_signInForSociel() {

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
                opr.setResultCallback(new ResultCallback<GoogleSignInResult>() {
                    @Override
                    public void onResult(@NonNull GoogleSignInResult googleSignInResult) {
                        handleSignInResult(googleSignInResult);
                    }
                });
            }
        }
    }

    private void adforest_loginSocialMedia(final String email, final String profileUrl) {

        if (SettingsMain.isConnectingToInternet(getActivity())) {

            loadingLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.setVisibility(View.VISIBLE);
            shimmerFrameLayout.startShimmer();
            nestedScroll.setVisibility(View.GONE);
            JsonObject params = new JsonObject();
            params.addProperty("LinkedIn_img", profileUrl);
            params.addProperty("email", email);
            params.addProperty("type", "social");
            RestService restService = UrlController.createService(RestService.class, email, "1122", getContext());
            Call<ResponseBody> myCall = restService.postLogin(params, UrlController.AddHeaders(getActivity()));
            myCall.enqueue(new Callback<ResponseBody>() {
                @Override
                public void onResponse(Call<ResponseBody> call, Response<ResponseBody> responseObj) {
                    try {
                        if (responseObj.isSuccessful()) {
                            Log.d("info LoginScoial respon", "" + responseObj.toString());

                            JSONObject response = new JSONObject(responseObj.body().string());

                            if (response.getBoolean("success")) {
                                nestedScroll.setVisibility(View.VISIBLE);

                                Log.d("info", "" + response.getJSONObject("data"));
                                Toast.makeText(getActivity(), response.get("message").toString(), Toast.LENGTH_SHORT).show();

                                settingsMain.setUserLogin(response.getJSONObject("data").getString("id"));
                                settingsMain.setUserImage(response.getJSONObject("data").getString("profile_img"));
                                settingsMain.setUserName(response.getJSONObject("data").getString("display_name"));
                                settingsMain.setUserPhone(response.getJSONObject("data").getString("phone"));
                                settingsMain.setUserEmail(email);
                                settingsMain.setUserPassword("1122");
                                settingsMain.isAppOpen(false);
                                SharedPreferences.Editor editor = getActivity().getSharedPreferences("com.adforest", MODE_PRIVATE).edit();
                                editor.putString("isSocial", "true");
                                editor.apply();

                                Intent intent = new Intent(getActivity(), HomeActivity.class);
                                startActivity(intent);
                                activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
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
