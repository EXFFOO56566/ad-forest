package com.scriptsbundle.adforest.Images;

import android.app.Dialog;
import android.content.Context;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.scriptsbundle.adforest.R;

/**
 * Created by GlixenTech on 3/22/2018.
 */

public class Nokri_UploadProgressDialolque implements Nokri_PopupManager.ConfirmInterface {

    private Context context;
    private Dialog dialog;
    private ProgressBar progressBar;
    TextView titleTextView;
    TextView messageTextView;
    TextView percentageTextView;
    TextView outofTextView;
    ImageButton closeImageButton;
    private CloseClickListener closeClickListener;
    private Nokri_PopupManager popupManager;

    public Nokri_UploadProgressDialolque(Context context) {
        this.context = context;
        dialog = new Dialog(context);
    }

    @Override
    public void onConfirmClick(Dialog dialog) {
        this.dialog.dismiss();
        dialog.dismiss();
        if(closeClickListener!=null)
            closeClickListener.onCloseClick();
    }

    public interface CloseClickListener{
        void onCloseClick();
    }
    public void setCloseClickListener(CloseClickListener closeClickListener){
        this.closeClickListener = closeClickListener;
        if(closeImageButton!=null)
            closeImageButton.setVisibility(View.VISIBLE);
    }


    public void showUploadDialogue(){

        try {

            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        }catch (Exception e){
            e.printStackTrace();
        }
//        dialog.setCancelable(false);
        dialog.setContentView(R.layout.upload_file_popup);
        //  dialog.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

        titleTextView = dialog.findViewById(R.id.txt_title);
        messageTextView = dialog.findViewById(R.id.txt_message);
        progressBar = dialog.findViewById(R.id.progress);
        percentageTextView = dialog.findViewById(R.id.txt_progress);
        outofTextView = dialog.findViewById(R.id.txt_outof);
        closeImageButton = dialog.findViewById(R.id.img_btn_close);
        titleTextView.setText("Uploading");

        dialog.show();

//        dialog.getWindow().setLayout(RelativeLayout.LayoutParams.MATCH_PARENT, (int) closeButton.getResources().getDimension(R.dimen.saved_jobs_popup_height));

        closeImageButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(dialog!=null && dialog.isShowing())
                {

                    popupManager = new Nokri_PopupManager(context,Nokri_UploadProgressDialolque.this);
                    dialog.dismiss();

                }
            }
        });
    }
    public void updateProgress(int progess,int currentFile,int totalFiles){
        String outOfText = currentFile+"/"+totalFiles;
        outofTextView.setText(outOfText);
        outofTextView.setVisibility(View.VISIBLE);
        progressBar.setProgress(progess);
        percentageTextView.setVisibility(View.VISIBLE);
        percentageTextView.setText(progess+"%");

    }
    public void handleSuccessScenario(){

        closeImageButton.setVisibility(View.GONE);
        progressBar.setVisibility(View.INVISIBLE);
        outofTextView.setVisibility(View.INVISIBLE);
        titleTextView.setVisibility(View.VISIBLE);
        titleTextView.setText("Success");
        messageTextView.setVisibility(View.VISIBLE);
        messageTextView.setText("Successfully Uploaded");
        percentageTextView.setText("100%");
        percentageTextView.setVisibility(View.VISIBLE);
        final Handler handler = new Handler(Looper.getMainLooper());
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                dialog.dismiss();
            }
        }, 2000);

    }
    public void handleFailedScenario(){
        closeImageButton.setVisibility(View.GONE);
        percentageTextView.setVisibility(View.GONE);
        progressBar.setVisibility(View.INVISIBLE);
        outofTextView.setVisibility(View.INVISIBLE);
        titleTextView.setVisibility(View.VISIBLE);
        titleTextView.setText("Upload Failed");
        messageTextView.setVisibility(View.VISIBLE);
        messageTextView.setText("File not uploaded");
        final Handler handler = new Handler(Looper.getMainLooper());
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                dialog.dismiss();
            }
        }, 2000);
    }
}
