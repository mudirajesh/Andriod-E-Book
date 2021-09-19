package com.example.freepaidebook.fragment;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.constraintlayout.widget.ConstraintLayout;

import com.example.freepaidebook.R;
import com.example.freepaidebook.util.Events;
import com.example.freepaidebook.util.GlobalBus;
import com.example.freepaidebook.util.Method;
import com.google.android.material.bottomsheet.BottomSheetDialogFragment;
import com.nguyenhoanglam.imagepicker.model.Config;
import com.nguyenhoanglam.imagepicker.model.Image;
import com.nguyenhoanglam.imagepicker.ui.imagepicker.ImagePicker;

import java.util.ArrayList;

public class ProImage extends BottomSheetDialogFragment {

    private Method method;
    private String imageProfile;
    private ArrayList<Image> galleryImages;
    private int REQUESTGALLERYPICKER = 100;
    private ConstraintLayout conRemove, conImage;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.pro_image, container, false);

        method = new Method(getActivity());
        if (method.isRtl()) {
            view.setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }

        conRemove = view.findViewById(R.id.con_remove_proImage);
        conImage = view.findViewById(R.id.con_image_proImage);

        conRemove.setOnClickListener(v -> {
            Events.ProImage proImage = new Events.ProImage("", "", false, true);
            GlobalBus.getBus().post(proImage);
            dismiss();
        });

        conImage.setOnClickListener(v -> chooseGalleryImage());

        return view;
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (data != null && resultCode == Activity.RESULT_OK && requestCode == REQUESTGALLERYPICKER) {
            galleryImages = data.getParcelableArrayListExtra(Config.EXTRA_IMAGES);
            assert galleryImages != null;
            imageProfile = galleryImages.get(0).getPath();
            dismiss();
            Events.ProImage proImage = new Events.ProImage("", imageProfile, true, false);
            GlobalBus.getBus().post(proImage);
        }
    }

    private void chooseGalleryImage() {
        try {
            ImagePicker.with(this)
                    .setFolderMode(true)
                    .setFolderTitle("Album")
                    .setImageTitle(getResources().getString(R.string.app_name))
                    .setStatusBarColor(method.imageGalleryToolBar())
                    .setToolbarColor(method.imageGalleryToolBar())
                    .setProgressBarColor(method.imageGalleryProgressBar())
                    .setMultipleMode(true)
                    .setMaxSize(1)
                    .setShowCamera(false)
                    .start();
        } catch (Exception e) {
            Log.e("error", e.toString());
        }
    }

}
