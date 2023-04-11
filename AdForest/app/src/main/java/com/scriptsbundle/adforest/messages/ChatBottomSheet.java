package com.scriptsbundle.adforest.messages;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.media.ExifInterface;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.ParcelFileDescriptor;
import android.provider.MediaStore;
import android.provider.OpenableColumns;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.annotation.RequiresApi;

import com.esafirm.imagepicker.features.ImagePicker;
import com.esafirm.imagepicker.model.Image;
import com.google.android.material.bottomsheet.BottomSheetDialogFragment;
import com.opensooq.supernova.gligar.GligarPicker;

import org.apache.commons.io.FilenameUtils;
import org.apache.commons.io.IOUtils;
import org.jetbrains.annotations.NotNull;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.List;
import java.util.Queue;

import br.com.onimur.handlepathoz.HandlePathOz;
import br.com.onimur.handlepathoz.HandlePathOzListener;
import br.com.onimur.handlepathoz.model.PathOz;
import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Response;
import com.scriptsbundle.adforest.Callback;
import com.scriptsbundle.adforest.JavaCompressor;
import com.scriptsbundle.adforest.R;
import com.scriptsbundle.adforest.helper.Helpers;
import com.scriptsbundle.adforest.utills.GPSTracker;
import com.scriptsbundle.adforest.utills.Network.RestService;
import com.scriptsbundle.adforest.utills.RuntimePermissionHelper;
import com.scriptsbundle.adforest.utills.SettingsMain;
import com.scriptsbundle.adforest.utills.UrlController;

import static android.app.Activity.RESULT_OK;

public class ChatBottomSheet extends BottomSheetDialogFragment implements View.OnClickListener, RuntimePermissionHelper.permissionInterface, HandlePathOzListener.SingleUri {

    Context context;
    LinearLayout pickImage, pickFiles, pickLocation;
    RuntimePermissionHelper runtimePermissionHelper;
    SettingsMain settingsMain;
    ArrayList<String> imagePaths;
    RestService restService;
    AttachmentModel attachmentModel;
    ChatInterface chatInterface;
    View viewS;
    TextView heading,imageText,filesText,mapText;
    ImageView imageIcon, fileIcon, mapIcon;
    String chatId,commId,session,postId,messageId,messageType = "image",uploadType = "image";
    boolean fromWhizzChat= false;
    WhizzChatInterface whizzChatInterface;
    public ChatBottomSheet(Context context, RestService restService, ChatInterface chatInterface, AttachmentModel attachmentModel) {
        this.context = context;
        this.restService = restService;
        this.chatInterface = chatInterface;
        this.attachmentModel = attachmentModel;
        this.restService = restService;
    }

    public void calledFrom(String chatId, String commId, String session, String postId, String messageId){
        fromWhizzChat = true;
        this.chatId = chatId;
        this.commId = commId;
        this.session = session;
        this.postId = postId;
        this.messageId = messageId;

    }
    public void setWhizzChatInterface(WhizzChatInterface whizzChatInterface){
        this.whizzChatInterface = whizzChatInterface;
    }

    HandlePathOz handlePathOz;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = LayoutInflater.from(context).inflate(R.layout.bottom_sheet_picker, null);

        handlePathOz = new HandlePathOz(getActivity(), this);
        settingsMain = new SettingsMain(context);
        runtimePermissionHelper = new RuntimePermissionHelper(getActivity(), this);
        pickImage = view.findViewById(R.id.imagesLayout);
        pickFiles = view.findViewById(R.id.filesLayout);
        pickLocation = view.findViewById(R.id.mapLayout);
        pickImage.setOnClickListener(this);
        pickFiles.setOnClickListener(this);
        runtimePermissionHelper.requestStorageCameraPermission(1);

        imageIcon = view.findViewById(R.id.imageIcon);
        fileIcon = view.findViewById(R.id.fileIcon);
        mapIcon = view.findViewById(R.id.mapIcon);
        heading = view.findViewById(R.id.heading);
        viewS = view.findViewById(R.id.view);
        imageText = view.findViewById(R.id.imageText);
        filesText = view.findViewById(R.id.filesText);
        mapText = view.findViewById(R.id.mapText);
        imageIcon.setColorFilter(Color.parseColor(SettingsMain.getMainColor()), android.graphics.PorterDuff.Mode.MULTIPLY);
        fileIcon.setColorFilter(Color.parseColor(SettingsMain.getMainColor()), android.graphics.PorterDuff.Mode.MULTIPLY);
//        mapIcon.setColorFilter(Color.parseColor(SettingsMain.getMainColor()), android.graphics.PorterDuff.Mode.MULTIPLY);
        imageText.setText(attachmentModel.upload_image);
        filesText.setText(attachmentModel.upload_doc);
        mapText.setText(attachmentModel.upload_loc);
        heading.setText(attachmentModel.upload_txt);
        if (fromWhizzChat){
            if (!attachmentModel.attachment_allow)
                pickFiles.setVisibility(View.GONE);
            if (!attachmentModel.imageAllow)
                pickImage.setVisibility(View.GONE);
            if (!attachmentModel.locationAllow)
                pickLocation.setVisibility(View.GONE);
            pickLocation.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    GPSTracker tracker = new GPSTracker(getActivity());
                    if (!tracker.canGetLocation())
                        tracker.showSettingsAlert();
                    else {
                        if (tracker.getLatitude()!=0&&tracker.getLongitude()!=0){
                            whizzChatInterface.getLocation(tracker.getLatitude(),tracker.getLongitude());
                            tracker.stopUsingGPS();
                            dismiss();  
                        }
                    }
                }
            });
        }else{
            //Removing Location Picker in normal chat
            pickLocation.setVisibility(View.GONE);

            if (attachmentModel.attachment_type.equals("images")){
                pickFiles.setVisibility(View.GONE);
            }if (attachmentModel.attachment_type.equals("attachments")){
                pickImage.setVisibility(View.GONE);
            }
        }
        viewS.setBackgroundColor(Color.parseColor(SettingsMain.getMainColor()));

        return view;
    }


    @SuppressLint("NewApi")
    @Override
    public void onClick(View view) {
        if (view.getId() == pickImage.getId()) {
            if (imageLimit > 0) {
                new GligarPicker().limit(per_limit).requestCode(111).withFragment(this).show();
            }
            Toast.makeText(context, stringImageLimitText, Toast.LENGTH_SHORT).show();
        }
        if (view.getId() == pickFiles.getId()) {
            pickFile();
        }
    }


    int imageLimit = 10, per_limit = 10;
    String stringImageLimitText = "";

    @Override
    public void onSuccessPermission(int code) {
//

    }

    @SuppressLint("NewApi")
    @Override
    public void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        Toast.makeText(context, "This", Toast.LENGTH_SHORT).show();
        if (resultCode == RESULT_OK && requestCode==111) {
            String pathsList[] = data.getExtras().getStringArray(GligarPicker.IMAGES_RESULT);

            List<File> files = new ArrayList<>();
            final int[] uploadingCount = {0};
            List<MultipartBody.Part> parts = new ArrayList<>();
            for (int i = 0; i < pathsList.length; i++) {

                File file1 = new File(pathsList[i]);
                JavaCompressor.compress(getActivity(), file1, new Callback() {
                    @Override
                    public void onComplete(boolean status, @org.jetbrains.annotations.Nullable File file) {
                        if (file != null) {
                            try {
                                if (file.length() > Double.parseDouble(attachmentModel.image_size)){
                                    Toast.makeText(context, attachmentModel.image_limit_txt, Toast.LENGTH_SHORT).show();
                                    dismiss();
                                    return;
                                }
                                String mimeType = URLConnection.guessContentTypeFromName(file.getName());
                                RequestBody requestFile =
                                        RequestBody.create(
                                                MediaType.parse(mimeType),
                                                file);

                                files.add(file);
                                MultipartBody.Part imageRequest = MultipartBody.Part.createFormData("chat_media[]", file.getName(), requestFile);
                                parts.add(imageRequest);


                                uploadingCount[0]++;
                            } catch (Exception e) {
                                e.printStackTrace();
                            }
                            if (SettingsMain.isConnectingToInternet(context)) {
                                if (uploadingCount[0] == pathsList.length) {
                                    if (whizzChatInterface!=null){
                                        RequestBody chat =
                                                RequestBody.create(
                                                        MediaType.parse("text/plain"), chatId);
                                        RequestBody sessionq = RequestBody.create(
                                                MediaType.parse("text/plain"), session);
                                        RequestBody post = RequestBody.create(
                                                MediaType.parse("text/plain"), postId);
                                        RequestBody comm = RequestBody.create(
                                                MediaType.parse("text/plain"), commId);
                                        RequestBody mId = RequestBody.create(
                                                MediaType.parse("text/plain"), messageId);
                                        RequestBody mType = RequestBody.create(
                                                MediaType.parse("text/plain"), messageType);
                                        RequestBody uType = RequestBody.create(
                                                MediaType.parse("text/plain"), uploadType);

                                        whizzChatInterface.getFiles(chat,sessionq,post,comm,mId,mType,uType,parts);
                                    }else {
                                        chatInterface.getFiles(parts, pathsList.length);
                                    }
                                    dismiss();
                                }
                            } else {
                                Toast.makeText(context, settingsMain.getAlertDialogMessage("internetMessage"), Toast.LENGTH_SHORT).show();
                            }

                        }
                    }
                });
            }
        }
        if (ImagePicker.shouldHandle(requestCode, resultCode, data)) {
            // Get a list of picked images
            List<Image> images = ImagePicker.getImages(data);
            // or get a single image only
//            Image image = ImagePicker.getFirstImageOrNull(data);

            boolean checkDimensions = true;
            boolean checkImageSize = true;

            ArrayList<File> imageFiles = new ArrayList<>();
            for (int i = 0; i < images.size(); i++) {

                String[] filePathColumn = {MediaStore.Images.Media.DATA};
                Cursor cursor = getActivity().getContentResolver().query(images.get(i).getUri(), filePathColumn, null, null, null);
                cursor.moveToFirst();
                int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
                String filePath = cursor.getString(columnIndex);
                cursor.close();

                Bitmap yourSelectedImage = BitmapFactory.decodeFile(filePath);


                ExifInterface exif = null;
                try {
                    exif = new ExifInterface(filePath);
                } catch (IOException e) {
                    e.printStackTrace();
                }
                int orientation = exif.getAttributeInt(ExifInterface.TAG_ORIENTATION,
                        ExifInterface.ORIENTATION_UNDEFINED);

                yourSelectedImage = Helpers.rotateBitmap(yourSelectedImage,orientation);
                File file = new File(filePath);
                file.getName();
                /* Now you have choosen image in Bitmap format in object "yourSelectedImage". You can use it in way you want! */

                File filesDir = getActivity().getFilesDir();
                File imageFile = new File(filesDir, file.getName());
                OutputStream os;
                try {
                    os = new FileOutputStream(imageFile);
                    yourSelectedImage.compress(Bitmap.CompressFormat.JPEG, 100, os);
                    os.flush();
                    os.close();
                } catch (Exception e) {
                    Log.e(getClass().getSimpleName(), "Error writing bitmap", e);
                }
//                String pathsList[] = {
//                        String.valueOf(yourSelectedImage)
////                        imageFile.getAbsolutePath()
//                }; // return list of selected images paths.
//                List<MultipartBody.Part> parts = null;
//
//                imagePaths = new ArrayList<>();
//                imagePaths.clear();
//
//                long fileSize = imageFile.length();
//                if (fileSize > Integer.parseInt(adPostImageModel.getImg_size())) {
//                    checkImageSize = false;
//                    Log.d("falsewalasize", String.valueOf(checkImageSize));
//                    Toast.makeText(context, adPostImageModel.getImg_message(), Toast.LENGTH_SHORT).show();
//                } else {
//                    checkImageSize = true;
//                    Log.d("truewalasize", String.valueOf(checkImageSize));
//                }
//                if (checkDimensions && checkImageSize)
                imageFiles.add(imageFile);
//                Collections.addAll(imagePaths, pathsList);
//                btnSelectPix.setEnabled(false);
//            }


//            showUploadProgress();


            }

            for (int i = 0; i < imageFiles.size(); i++) {
                int finalI = i;
                JavaCompressor.compress(getActivity(), imageFiles.get(i), new Callback() {
                    @Override
                    public void onComplete(boolean status, @org.jetbrains.annotations.Nullable File file) {

                    }
                });


            }


        } else if (requestCode == 321) {

            if (data!=null){
                if (data.getData() == null) return;

                context.getContentResolver().takePersistableUriPermission(data.getData(), Intent.FLAG_GRANT_READ_URI_PERMISSION);
                Context context = getActivity();


                File imageFile = null;
                ParcelFileDescriptor descriptor = null;
                try {
                    descriptor = getActivity().getContentResolver().openFileDescriptor(data.getData(), "r", null);

                    InputStream inputStream = new FileInputStream(descriptor.getFileDescriptor());
                    imageFile = new File(getActivity().getCacheDir(), getFileName(data.getData()));
                    OutputStream os;
                    try {
                        os = new FileOutputStream(imageFile);
                        IOUtils.copy(inputStream,os);
                        os.flush();
                        os.close();
                    } catch (Exception e) {
                        Log.e(getClass().getSimpleName(), "Error writing bitmap", e);
                    }
                    String mimeType = URLConnection.guessContentTypeFromName(imageFile.getName());
                    RequestBody requestFile =
                            RequestBody.create(
                                    MediaType.parse(mimeType),
                                    imageFile);
                    MultipartBody.Part imageRequest = MultipartBody.Part.createFormData("chat_media[]", imageFile.getName(), requestFile);
                    ArrayList<MultipartBody.Part> parts = new ArrayList<>();
                    parts.add(imageRequest);
                    String extension = imageFile.getName().substring(imageFile.getName().lastIndexOf("."));
                    Toast.makeText(context, extension, Toast.LENGTH_SHORT).show();
                    if (imageFile.length()>Double.parseDouble(attachmentModel.attachment_size)){
                        Toast.makeText(context, attachmentModel.doc_limit_txt, Toast.LENGTH_SHORT).show();
                    }
                    else{
                        boolean goodToGO = false;
                        for (int i = 0;i<attachmentModel.attachment_format.size();i++){
                            if (FilenameUtils.getExtension(imageFile.getName()).equals(attachmentModel.attachment_format.get(i))){
                                goodToGO = true;
                                break;
                            }
                        }
                        if (goodToGO){

                            if (whizzChatInterface!=null){

                                RequestBody chat =
                                        RequestBody.create(
                                                MediaType.parse("text/plain"), chatId);
                                RequestBody sessionq = RequestBody.create(
                                        MediaType.parse("text/plain"), session);
                                RequestBody post = RequestBody.create(
                                        MediaType.parse("text/plain"), postId);
                                RequestBody comm = RequestBody.create(
                                        MediaType.parse("text/plain"), commId);
                                RequestBody mId = RequestBody.create(
                                        MediaType.parse("text/plain"), messageId);
                                RequestBody mType = RequestBody.create(
                                        MediaType.parse("text/plain"), "file");
                                RequestBody uType = RequestBody.create(
                                        MediaType.parse("text/plain"), "file");

                                whizzChatInterface.getFiles(chat,sessionq,post,comm,mId,mType,uType,parts);
                            }else {
                                chatInterface.getFiles(parts, 1);
                            }
                        }
                        else
                            Toast.makeText(context, attachmentModel.doc_format_txt, Toast.LENGTH_SHORT).show();
                    }
                    dismiss();
                } catch (FileNotFoundException e) {
                    e.printStackTrace();
                }
            }
        }
    }


    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void pickFile() {
//        File cacheFile = new
//        File(context.getCacheDir(), "image/yoo.jpg");
//
//        Uri uri = FileProvider.getUriForFile(context, getActivity().getPackageName() + ".provider", cacheFile);
        Intent intent = new Intent(Intent.ACTION_OPEN_DOCUMENT);
        intent.addCategory(Intent.CATEGORY_OPENABLE);
        intent.setType("*/*");

        // Optionally, specify a URI for the file that should appear in the
        // system file picker when it loads.
        startActivityForResult(intent, 321);
    }
    @SuppressLint("NewApi")
    @Override
    public void onRequestHandlePathOz(@NotNull PathOz pathOz, @org.jetbrains.annotations.Nullable Throwable throwable) {
        File file = new File(pathOz.getPath());

//        File filesDir = getActivity().getCacheDir();
//        File imageFile = new File(filesDir, file.getName());
//        File imagePath = new File(getActivity().getFilesDir(), "external_files");
//        imagePath.mkdir();
//        File imageFile1 = new File(imagePath.getPath(), file.getName());


//        try {
//
//            Uri contentUri = FileProvider.getUriForFile(getContext(), getActivity().getApplication().getPackageName()+".provider", imageFile1);
//
//            ParcelFileDescriptor descriptor = getActivity().getContentResolver().openFileDescriptor(contentUri, "r", null);
//            InputStream inputStream = new FileInputStream(descriptor.getFileDescriptor());
//            imageFile = new File(getActivity().getCacheDir(), getFileName(Uri.parse(pathOz.getPath())));
//
//            OutputStream os = new FileOutputStream(imageFile1);
//            os.flush();
//            os.close();
//            IOUtils.copy(inputStream,os);
//        } catch (FileNotFoundException e) {
//            e.printStackTrace();
//        } catch (IOException e) {
//            e.printStackTrace();
//        }

    }


    public static interface ChatInterface {
        void getFiles(List<MultipartBody.Part> parts, int count);
    }

    public String getFileName(Uri fileUri) {
        String name;

        Cursor cursor = context.getContentResolver().query(fileUri, null, null, null, null);

        int nameIndex = cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME);
        cursor.moveToFirst();
        name = cursor.getString(nameIndex);
        cursor.close();

        return name;
    }




    public static interface WhizzChatInterface{
        void getLocation(Double latitude,Double longitude);
        void getFiles(RequestBody chatId,RequestBody session, RequestBody postId, RequestBody commId,  RequestBody messageId, RequestBody messageType, RequestBody uploadType,List<MultipartBody.Part> parts);
    }


}
