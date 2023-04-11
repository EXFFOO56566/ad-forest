package com.scriptsbundle.adforest.Images;


import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Color;
import android.os.Build;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.utills.SettingsMain;

public class Nokri_PopupManager {

    private Context context;
    public interface ConfirmInterface {
        void onConfirmClick(Dialog dialog);
    }

    public interface NoInternetInterface {
        void onButtonClick(DialogInterface dialog);

        void onNoClick(DialogInterface dialog);
    }

    private ConfirmInterface confirmInterface;

    public Nokri_PopupManager(Context context, ConfirmInterface confirmInterface) {
        this.context = context;
        this.confirmInterface = confirmInterface;
    }


    //requires only exit text


    //Set By Server Response needs successfully text
    private void showSuccessDialog(String message) {

        final Dialog dialog = new Dialog(context);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setCancelable(false);
        dialog.setContentView(R.layout.popup_saved_jobs_success);
        TextView successTextView = dialog.findViewById(R.id.txt_success);
        successTextView.setText("Success");
        TextView successTextViewData = dialog.findViewById(R.id.txt_success_data);
        ImageView closeImageView = dialog.findViewById(R.id.img_close);
        successTextViewData.setText(message);
//        fontManager.nokri_setMonesrratSemiBioldFont(successTextView, context.getAssets());
//        fontManager.nokri_setOpenSenseFontTextView(successTextViewData, context.getAssets());

        closeImageView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                dialog.dismiss();
            }
        });
        dialog.show();
        dialog.getWindow().setLayout(RelativeLayout.LayoutParams.MATCH_PARENT, (int) context.getResources().getDimension(R.dimen.saved_jobs_popup_height));
    }

    public void nokri_showSuccessPopup(String message) {
        showSuccessDialog(message);
    }

//    public void nokri_showDeletePopup() {
//        showDeleteDialog();
//    }
//
//    public void nokri_showPopupWithCustomMessage(String message) {
//        showCustomTitleDialog(message);
//    }


    public static void nokri_showNoInternetAlert(Context context, final NoInternetInterface noInternetInterface) {
        AlertDialog.Builder builder;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            builder = new AlertDialog.Builder(context, android.R.style.Theme_Material_Light_Dialog_Alert);
        } else {
            builder = new AlertDialog.Builder(context);
        }
        builder.setTitle("Connection Lost!")
                .setMessage("Close App?").setCancelable(false)
                .setPositiveButton(android.R.string.yes, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {

                        noInternetInterface.onButtonClick(dialog);
                    }
                }).setNegativeButton(android.R.string.no, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                noInternetInterface.onNoClick(dialog);
            }
        })
                .setIcon(android.R.drawable.ic_dialog_alert)
                .show();

    }


}

