package com.scriptsbundle.adforest.signinorup;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.core.widget.NestedScrollView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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
import com.google.android.gms.common.api.ResultCallback;
import com.google.android.gms.common.api.Status;
import com.google.gson.JsonObject;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.Arrays;
import java.util.Random;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import com.scriptsbundle.adforest.LinkedIn.LinkedInAuthenticationActivity;
import com.scriptsbundle.adforest.LinkedIn.LinkedInBuilder;
import com.scriptsbundle.adforest.LinkedIn.helpers.LinkedInUser;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static android.app.Activity.RESULT_OK;
import static android.content.Context.MODE_PRIVATE;


public class MainViewRegisterFragment extends Fragment implements GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener {
    private View view;
    private static FragmentManager fragmentManager;
    public static final int RC_SIGN_IN = 0;
    Activity activity;
    ImageView imageView;
    TextView txtWelcomeHeading, txtSubHeading, TxtEmail,
            TxtFacebook, txtGoogle, txtPhone, txtLinkedIn;
    private CallbackManager callbackManager;
    private SettingsMain settingsMain;
    private boolean mIntentInProgress = true;
    private GoogleApiClient mGoogleApiClient;
    private String state;
    static final String STATE = "state";
    RestService restService;
    String phVerifcation, otpTxt, submit, placeholderField;
    LinearLayout linearLayoutFb, linearLayoutGoogle, linearLayoutLinkedIn, linearLayoutGuest, linearLayoutOTP;
    ShimmerFrameLayout shimmerFrameLayout;
    LinearLayout loadingLayout;
    NestedScrollView nestedScroll;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_main_view_register, container, false);
        adforest_initializeView();
        return view;
    }

    private void adforest_initializeView() {
        fragmentManager = getActivity().getSupportFragmentManager();
        activity = getActivity();
        settingsMain = new SettingsMain(activity);
        restService = UrlController.createService(RestService.class);
        nestedScroll = view.findViewById(R.id.nestedScroll);
        shimmerFrameLayout = view.findViewById(R.id.shimmerFrameLayout);
        loadingLayout = view.findViewById(R.id.shimmerMain);
        imageView = view.findViewById(R.id.imgHeader);
        txtWelcomeHeading = view.findViewById(R.id.txt_welcomeHeading);
        txtSubHeading = view.findViewById(R.id.txt_sub_Heading);
        TxtEmail = view.findViewById(R.id.txt_continue_with_email);
        TxtFacebook = view.findViewById(R.id.txt_continue_with_facebook);
        txtGoogle = view.findViewById(R.id.txt_continue_with_google);
        txtLinkedIn = view.findViewById(R.id.txt_continue_with_linkedin);
        txtPhone = view.findViewById(R.id.txt_continue_with_phone);
        linearLayoutFb = view.findViewById(R.id.ll_facebook);
        linearLayoutGoogle = view.findViewById(R.id.ll_google);
        linearLayoutLinkedIn = view.findViewById(R.id.ll_linkedin);
        linearLayoutGuest = view.findViewById(R.id.ll_Guest);
        linearLayoutOTP = view.findViewById(R.id.ll_phone);
        fbSetup();
        mIntentInProgress = true;
        getActivity().setTitle("LoginOr Register");
        Bundle bundle = this.getArguments();
        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestEmail()
                .build();

        mGoogleApiClient = new GoogleApiClient.Builder(getActivity())
                .addOnConnectionFailedListener(this)
                .addConnectionCallbacks(this)
                .addApi(Auth.GOOGLE_SIGN_IN_API, gso)
                .build();

        TxtEmail.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                fragmentManager
                        .beginTransaction()
                        .replace(R.id.frameContainer, new SignUp_Fragment(),
                                Utils.SignUp_Fragment).commit();
            }
        });
        TxtFacebook.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                adforest_loginToFacebook();
            }
        });
        txtGoogle.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                adforest_signInForSociel();

            }
        });
        txtLinkedIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent i = new Intent(getActivity(), LinkedInAuthenticationActivity.class);
                i.putExtra("client_id", UrlController.LINKEDIN_CLIENT_ID);
                i.putExtra("client_secret", UrlController.LINKEDIN_CLIENT_SECRET);
                i.putExtra("redirect_uri", UrlController.LINKEDIN_REDIRECT_URL);
                generateState(i);
                startActivityForResult(i, 25);
            }
        });
        txtPhone.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                OTPSignInOrSignUp otpSignInOrSignUp = new OTPSignInOrSignUp();
                Bundle bundle = new Bundle();
                bundle.putBoolean("login", false);
                bundle.putString("phonetxt", phVerifcation);
                bundle.putString("otpTxt", otpTxt);
                bundle.putString("submit", submit);
                bundle.putString("placeHolder", placeholderField);
                otpSignInOrSignUp.setArguments(bundle);
                FragmentTransaction fragmentTransaction = getFragmentManager().beginTransaction();
                fragmentTransaction.replace(R.id.frameContainer, otpSignInOrSignUp).addToBackStack(null).commit();
                activity.overridePendingTransition(R.anim.right_enter, R.anim.left_out);
            }
        });
        adforest_setDataToViews();
        if (settingsMain.getfbButn()) {
            linearLayoutFb.setVisibility(View.VISIBLE);
        } else {
            linearLayoutFb.setVisibility(View.GONE);
        }
        if (settingsMain.getLinkedinButn()) {
            linearLayoutLinkedIn.setVisibility(View.VISIBLE);
        } else {
            linearLayoutLinkedIn.setVisibility(View.GONE);
        }
        if (settingsMain.getGooglButn()) {
            linearLayoutGoogle.setVisibility(View.VISIBLE);
        } else {
            linearLayoutGoogle.setVisibility(View.GONE);
        }
        if (settingsMain.getOTPButn()) {
            linearLayoutOTP.setVisibility(View.VISIBLE);
        } else {
            linearLayoutOTP.setVisibility(View.GONE);
        }
        if (!settingsMain.getfbButn() && !settingsMain.getGooglButn() && !settingsMain.getLinkedinButn() && !settingsMain.getOTPButn()) {
            linearLayoutLinkedIn.setVisibility(View.GONE);
            linearLayoutFb.setVisibility(View.GONE);
            linearLayoutGoogle.setVisibility(View.GONE);
            linearLayoutOTP.setVisibility(View.GONE);
        }
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

    private void generateState(Intent intent) {
        String ALLOWED_CHARACTERS = "0123456789qwertyuiopasdfghjklzxcvbnmMNBVCXZLKJHGFDSAQWERTYUIOP";
        final Random random = new Random();
        final StringBuilder sb = new StringBuilder(16);
        for (int i = 0; i < 16; ++i)
            sb.append(ALLOWED_CHARACTERS.charAt(random.nextInt(ALLOWED_CHARACTERS.length())));
        this.state = sb.toString();
        intent.putExtra(STATE, state);
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

    //GetRegister Data
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
                            TxtEmail.setText(response.getJSONObject("data").getString("email_btn"));
                            TxtFacebook.setText(response.getJSONObject("data").getString("facebook_btn"));
                            txtGoogle.setText(response.getJSONObject("data").getString("google_btn"));
                            txtLinkedIn.setText(response.getJSONObject("data").getString("linkedin_btn"));
                            txtPhone.setText(response.getJSONObject("data").getString("phone_btn"));
                            phVerifcation = response.getJSONObject("data").getString("phone_verification");
                            otpTxt = response.getJSONObject("data").getString("otp_text");
                            submit = response.getJSONObject("data").getString("form_btn");
                            placeholderField = response.getJSONObject("data").getString("phone_number");
                            txtWelcomeHeading.setText(response.getJSONObject("data").getString("welcome_txt"));
                            txtSubHeading.setText(response.getJSONObject("data").getString("intro_text"));

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
                                editor.putString("otp", "false");
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
        }
//        if (requestCode==311){
//            if (resultCode == RESULT_OK){
//                String status = data.getStringExtra("status");
//                if (status.equals("verified")){
//                    registerUser();
//                }
//            }
//        }


    }

    void replaceFragment(Fragment someFragment, String tag) {
        FragmentTransaction transaction = getFragmentManager().beginTransaction();
        transaction.setCustomAnimations(R.anim.right_enter, R.anim.left_out, R.anim.left_enter, R.anim.right_out);
        transaction.replace(R.id.frameContainer, someFragment, tag);
        transaction.addToBackStack(tag);
        transaction.commit();
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

}