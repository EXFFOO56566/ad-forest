package com.scriptsbundle.adforest.packages;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.graphics.Color;
import android.net.http.SslError;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.webkit.SslErrorHandler;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.utills.CustomBorderDrawable;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

public class Thankyou extends AppCompatActivity implements View.OnClickListener {
    ImageView imageView, logoThankyou;
    Button contineBuyPackage;
    SettingsMain settingsMain;
    Activity activity;
    RestService restService;
    WebView webView;
    TextView tv_thankyou_title;
    String webViewData, titleData, buttonData;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);

        this.setContentView(R.layout.activity_thankyou);
        settingsMain = new SettingsMain(this);
        activity = Thankyou.this;

        webViewData = getIntent().getStringExtra("data");
        titleData = getIntent().getStringExtra("order_thankyou_title");
        buttonData = getIntent().getStringExtra("order_thankyou_btn");

        imageView = (ImageView) findViewById(R.id.closeIcon);
        logoThankyou = (ImageView) findViewById(R.id.logoThankyou);
        webView = (WebView) findViewById(R.id.webViewThankyou);
        webView.setWebViewClient(new WebViewClient(){
            @Override
            public void onReceivedSslError(WebView view, SslErrorHandler handler, SslError error) {
                final AlertDialog.Builder builder = new AlertDialog.Builder(Thankyou.this);
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
        tv_thankyou_title = (TextView) findViewById(R.id.thankyourText);
        contineBuyPackage = (Button) findViewById(R.id.contineBuyPackage);

        imageView.setOnClickListener(this);
        contineBuyPackage.setOnClickListener(this);

        restService = UrlController.createService(RestService.class, settingsMain.getUserEmail(), settingsMain.getUserPassword(), activity);

        contineBuyPackage.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
        tv_thankyou_title.setTextColor(Color.parseColor(SettingsMain.getMainColor()));
        contineBuyPackage.setBackground(CustomBorderDrawable.customButton(3, 3, 3, 3, SettingsMain.getMainColor(), "#00000000", SettingsMain.getMainColor(), 2));

        adforest_loadData();

    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.closeIcon:
                finish();
                break;
            case R.id.contineBuyPackage:
                finish();
                break;
        }
    }

    public void adforest_loadData() {

        if (!settingsMain.getAppLogo().equals(""))
            Picasso.get().load(settingsMain.getAppLogo())
                    .error(R.drawable.logo)
                    .placeholder(R.drawable.logo)
                    .fit().centerInside()
                    .into(logoThankyou);
        webView.setScrollContainer(false);
        webView.loadDataWithBaseURL(null, webViewData, "text/html", "UTF-8", null);
        tv_thankyou_title.setText(titleData);
        contineBuyPackage.setText(buttonData);

        Toast.makeText(Thankyou.this, settingsMain.getpaymentCompletedMessage(), Toast.LENGTH_SHORT).show();
    }

}
