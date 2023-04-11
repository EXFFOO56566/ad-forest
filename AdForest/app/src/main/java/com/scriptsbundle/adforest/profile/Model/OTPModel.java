package com.scriptsbundle.adforest.profile.Model;

public class OTPModel {
    private static OTPModel single_instance = null;


    public String phone;
    public String name;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public PhoneDialog phoneDialog;
    public SendSmsDialog smsDialog;
    public static OTPModel getInstance(){
        if (single_instance == null){
            single_instance = new OTPModel();
        }
        return single_instance;
    }


    public String code_sent;
    public String not_received;
    public String try_again;
    public String verify_number;
    public String sms_gateway;
    public String verify_success;
    public boolean is_number_verified;
    public String is_number_verified_text;
    public String error1;


    public String getError1() {
        return error1;
    }

    public void setError1(String error1) {
        this.error1 = error1;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getCode_sent() {
        return code_sent;
    }

    public void setCode_sent(String code_sent) {
        this.code_sent = code_sent;
    }

    public String getNot_received() {
        return not_received;
    }

    public void setNot_received(String not_received) {
        this.not_received = not_received;
    }

    public String getTry_again() {
        return try_again;
    }

    public void setTry_again(String try_again) {
        this.try_again = try_again;
    }

    public String getVerify_number() {
        return verify_number;
    }

    public void setVerify_number(String verify_number) {
        this.verify_number = verify_number;
    }

    public String getSms_gateway() {
        return sms_gateway;
    }

    public void setSms_gateway(String sms_gateway) {
        this.sms_gateway = sms_gateway;
    }

    public String getVerify_success() {
        return verify_success;
    }

    public void setVerify_success(String verify_success) {
        this.verify_success = verify_success;
    }

    public boolean isIs_number_verified() {
        return is_number_verified;
    }

    public void setIs_number_verified(boolean is_number_verified) {
        this.is_number_verified = is_number_verified;
    }

    public String getIs_number_verified_text() {
        return is_number_verified_text;
    }

    public void setIs_number_verified_text(String is_number_verified_text) {
        this.is_number_verified_text = is_number_verified_text;
    }

    public void setPhoneDialog(String textField, String cancelBtn, String confirmButton, String resendButton){
        phoneDialog = new PhoneDialog(textField,cancelBtn,confirmButton,resendButton);
    }


    public void setSMSDialog(String title, String text, String btn_send, String btn_cancel){
        smsDialog = new SendSmsDialog(title,text,btn_send,btn_cancel);
    }

    public SendSmsDialog getSMSDialogStrings(){
        return smsDialog;
    }


    public PhoneDialog getPhoneDialogStrings(){
        return phoneDialog;
    }



    public class PhoneDialog{
        public String text_field;
        public String btn_cancel;
        public String btn_confirm;
        public String btn_resend;

        public PhoneDialog(String text_field, String btn_cancel, String btn_confirm, String btn_resend) {
            this.text_field = text_field;
            this.btn_cancel = btn_cancel;
            this.btn_confirm = btn_confirm;
            this.btn_resend = btn_resend;
        }
    }

    public class SendSmsDialog{
        public String title;
        public String text;
        public String btn_send;
        public String btn_cancel;

        public SendSmsDialog(String title, String text, String btn_send, String btn_cancel) {
            this.title = title;
            this.text = text;
            this.btn_send = btn_send;
            this.btn_cancel = btn_cancel;
        }
    }
}
