package com.scriptsbundle.adforest.utills;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.ImageView;


/**
 * Created by alan on 08.12.16.
 */

public class RectangleImageView extends androidx.appcompat.widget.AppCompatImageView {
    private CollageView.ImageForm imageForm = CollageView.ImageForm.IMAGE_FORM_SQUARE;

    public RectangleImageView(Context context, CollageView.ImageForm imageForm) {
        super(context);
        this.imageForm = imageForm;
    }

    public RectangleImageView(Context context, AttributeSet attrs, CollageView.ImageForm imageForm) {
        super(context, attrs);
        this.imageForm = imageForm;
    }

    public RectangleImageView(Context context, AttributeSet attrs, CollageView.ImageForm imageForm, int defStyle) {
        super(context, attrs, defStyle);
        this.imageForm = imageForm;
    }

    @Override
    protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
        super.onMeasure(widthMeasureSpec, heightMeasureSpec);
        if (getParent() != null) {
            getLayoutParams().height = widthMeasureSpec / imageForm.getDivider();
            setMeasuredDimension(widthMeasureSpec, widthMeasureSpec / imageForm.getDivider());
        }
    }
}