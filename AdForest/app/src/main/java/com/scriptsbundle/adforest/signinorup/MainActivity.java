package com.scriptsbundle.adforest.signinorup;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.appcompat.app.AppCompatActivity;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.LocaleHelper;
import com.scriptsbundle.adforest.home.ChooseLanguageActivity;
import com.scriptsbundle.adforest.home.HomeActivity;
import com.scriptsbundle.adforest.utills.Adforest_PopupManager;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class MainActivity extends AppCompatActivity {
    private static FragmentManager fragmentManager;
    SettingsMain settingsMain;
    boolean back_pressed = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getSupportActionBar() != null) {
            getSupportActionBar().hide();
        }
        settingsMain = new SettingsMain(this);
        setContentView(R.layout.activity_main);
        fragmentManager = getSupportFragmentManager();

        // If savedinstnacestate is null then replace login fragment
        if (savedInstanceState == null) {
            if (getIntent().getBooleanExtra("page", false)) {
                fragmentManager
                        .beginTransaction()
                        .replace(R.id.frameContainer, new MainViewRegisterFragment(),
                                Utils.MainViewRegisterFragment).commit();
            } else
                fragmentManager
                        .beginTransaction()
                        .replace(R.id.frameContainer, new MainViewLoginFragment(),
                                Utils.MainViewLoginFragment).commit();
        }
        updateViews(settingsMain.getAlertDialogMessage("gmap_lang"));
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(LocaleHelper.onAttach(base));
    }

    private void updateViews(String languageCode) {
        LocaleHelper.setLocale(this, languageCode);
    }

    // Replace Login Fragment with animation
    protected void adforest_replaceLoginFragment() {
        fragmentManager
                .beginTransaction()
                .setCustomAnimations(R.anim.left_enter, R.anim.right_out)
                .replace(R.id.frameContainer, new MainViewLoginFragment(),
                        Utils.MainViewLoginFragment).commit();
    }

    @Override
    public void onBackPressed() {

        // Find the tag of signup and forgot password fragment
        Fragment SignUp_Fragment = fragmentManager
                .findFragmentByTag(Utils.MainViewRegisterFragment);
        Fragment Login_Fragment = fragmentManager
                .findFragmentByTag(Utils.MainViewLoginFragment);
        Fragment ForgotPassword_Fragment = fragmentManager
                .findFragmentByTag(Utils.ForgotPassword_Fragment);
        Fragment VerifyAccount_Fragment = fragmentManager
                .findFragmentByTag(Utils.VerifyAccount_Fragment);



        // Check if both are null or not
        // If both are not null then replace login fragment else do back pressed
        // task

        if (SignUp_Fragment != null){
            if(settingsMain.getAppOpen()){
                Intent intent = new Intent(getApplicationContext(), HomeActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);

            }else{
                if (SignUp_Fragment.isVisible()) {
                    if (!back_pressed) {
                        Toast.makeText(getApplicationContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
                        back_pressed = true;
                        android.os.Handler mHandler = new android.os.Handler();
                        mHandler.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                back_pressed = false;
                            }
                        }, 2000L);
                    } else {
                        AlertDialog.Builder alert = new AlertDialog.Builder(MainActivity.this);
                        alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                        alert.setCancelable(false);
                        alert.setMessage(settingsMain.getExitApp("exit"));
                        alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                            finishAffinity();
//                        finish();
                            dialog.dismiss();
                            overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                        });
                        alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                        alert.show();
                    }
                }
            }
        }
//            adforest_replaceLoginFragment();
        else if (ForgotPassword_Fragment != null)
            adforest_replaceLoginFragment();
        else if (VerifyAccount_Fragment != null)
            adforest_replaceLoginFragment();
        else if (Login_Fragment != null){
            if(settingsMain.getAppOpen()){
                Intent intent = new Intent(getApplicationContext(), HomeActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                overridePendingTransition(R.anim.right_enter, R.anim.left_out);

            }else{
                if (Login_Fragment != null && Login_Fragment.isVisible()) {
                    if (!back_pressed) {
                        Toast.makeText(getApplicationContext(), settingsMain.getExitApp("exit"), Toast.LENGTH_SHORT).show();
                        back_pressed = true;
                        android.os.Handler mHandler = new android.os.Handler();
                        mHandler.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                back_pressed = false;
                            }
                        }, 2000L);
                    } else {
                        AlertDialog.Builder alert = new AlertDialog.Builder(MainActivity.this);
                        alert.setTitle(settingsMain.getAlertDialogTitle("info"));
                        alert.setCancelable(false);
                        alert.setMessage(settingsMain.getExitApp("exit"));
                        alert.setPositiveButton(settingsMain.getAlertOkText(), (dialog, which) -> {
                            finishAffinity();
//                        finish();
                            dialog.dismiss();
                            overridePendingTransition(R.anim.right_enter, R.anim.left_out);
                        });
                        alert.setNegativeButton(settingsMain.getAlertCancelText(), (dialogInterface, i) -> dialogInterface.dismiss());
                        alert.show();
                    }
                }
            }
        }

        else {
            super.onBackPressed();
            overridePendingTransition(R.anim.left_enter, R.anim.right_out);
        }
    }
}
