package com.scriptsbundle.adforest.Images;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.scriptsbundle.adforest.R;

public class Nokri_FullScreenAlertDialog extends Dialog {


    private String customTex = null;
    private ProgressBar progressBar;
    private TextView alertTexView;

    public Nokri_FullScreenAlertDialog(@NonNull Context context, int themeResId) {
        super(context, themeResId);
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.dialog_layout);
        alertTexView = findViewById(R.id.txt_alert);
        progressBar = findViewById(R.id.progress_bar);
        alertTexView.setText("Please Wait");
        if(customTex!=null)
        {
            alertTexView.setText(customTex);
        }

    }
    @Override
    public void onStart() {
        super.onStart();

     /*   Dialog dialog = this;
        if (dialog != null) {
            int width = ViewGroup.LayoutParams.MATCH_PARENT;
            int height = ViewGroup.LayoutParams.MATCH_PARENT;
            dialog.getWindow().setLayout(width, height);
        }*/
        this.setCancelable(false);}




    public void showError(){
        // ProgressBar progressBar = getView().findViewById(R.id.progress_bar);
        progressBar.setVisibility(View.INVISIBLE);
        //    TextView alertTexView = getView().findViewById(R.id.txt_alert);
        alertTexView.setText("Error");
        alertTexView.setTextColor(getContext().getResources().getColor(R.color.google_red));
    }

    public void showSuccess(){
        //  ProgressBar progressBar = getView().findViewById(R.id.progress_bar);
        progressBar.setVisibility(View.INVISIBLE);
        //  TextView alertTexView = getView().findViewById(R.id.txt_alert);
        alertTexView.setText("Successfull");
        alertTexView.setTextColor(Color.GREEN);
    }
    public void showCustomMessage(){
        //   ProgressBar progressBar = getView().findViewById(R.id.progress_bar);
        progressBar.setVisibility(View.INVISIBLE);
        //  TextView alertTexView = getView().findViewById(R.id.txt_alert);
        alertTexView.setText(customTex);
        alertTexView.setTextColor(getContext().getResources().getColor(R.color.google_red));
    }

    public void setCustomMessage(String customMessage){
        this.customTex = customMessage;
    }
}


