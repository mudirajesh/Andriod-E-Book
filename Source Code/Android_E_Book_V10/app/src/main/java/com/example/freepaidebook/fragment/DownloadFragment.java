package com.example.freepaidebook.fragment;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AnimationUtils;
import android.view.animation.LayoutAnimationController;
import android.widget.ProgressBar;

import androidx.annotation.Nullable;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.freepaidebook.R;
import com.example.freepaidebook.activity.MainActivity;
import com.example.freepaidebook.activity.PDFShow;
import com.example.freepaidebook.adapter.DownloadAdapter;
import com.example.freepaidebook.database.DatabaseHandler;
import com.example.freepaidebook.interfaces.OnClick;
import com.example.freepaidebook.item.DownloadList;
import com.example.freepaidebook.util.Constant;
import com.example.freepaidebook.util.Method;
import com.folioreader.FolioReader;
import com.folioreader.model.locators.ReadLocator;
import com.karumi.dexter.Dexter;
import com.karumi.dexter.PermissionToken;
import com.karumi.dexter.listener.PermissionDeniedResponse;
import com.karumi.dexter.listener.PermissionGrantedResponse;
import com.karumi.dexter.listener.PermissionRequest;
import com.karumi.dexter.listener.single.PermissionListener;

import org.jetbrains.annotations.NotNull;

import java.io.File;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.LinkedList;
import java.util.List;
import java.util.Queue;

public class DownloadFragment extends Fragment {

    private Method method;
    private OnClick onClick;
    private DatabaseHandler db;
    private ConstraintLayout conNoData;
    private ProgressBar progressBar;
    private List<File> inFiles;
    private List<DownloadList> downloadLists;
    private List<DownloadList> downloadListsCompair;
    private RecyclerView recyclerView;
    private DownloadAdapter downloadAdapter;
    private LayoutAnimationController animation;

    @Nullable
    @Override
    public View onCreateView(@NotNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        View view = LayoutInflater.from(getActivity()).inflate(R.layout.download_fragment, container, false);

        if (MainActivity.toolbar != null) {
            MainActivity.toolbar.setTitle(getResources().getString(R.string.download));
        }

        int resId = R.anim.layout_animation_fall_down;
        animation = AnimationUtils.loadLayoutAnimation(getActivity(), resId);

        db = new DatabaseHandler(getActivity());
        downloadLists = new ArrayList<>();

        onClick = (position, type, id,subId, title, fileType, fileUrl,otherData) -> {
            if (fileUrl.contains(".epub")) {
                FolioReader folioReader = FolioReader.get();
                folioReader.setOnHighlightListener((highlight, type1) -> {

                });
                if (!db.checkIdEpub(id)) {

                    String string = db.getEpub(id);
                    ReadLocator readPosition = ReadLocator.fromJson(string);
                    folioReader.setReadLocator(readPosition);

                }
                folioReader.openBook(fileUrl);
                folioReader.setReadLocatorListener(readLocator -> {
                    if (db.checkIdEpub(id)) {
                        db.addEpub(id, readLocator.toJson());
                    } else {
                        db.updateEpub(id, readLocator.toJson());
                    }
                });
            } else {

                String[] strings = fileUrl.split("filename-");
                String[] idPdf = strings[1].split(".pdf");

                startActivity(new Intent(getActivity(), PDFShow.class)
                        .putExtra("id", idPdf[0])
                        .putExtra("link", fileUrl)
                        .putExtra("title", title)
                        .putExtra("type", "file"));
            }
        };
        method = new Method(getActivity(), onClick);

        inFiles = new ArrayList<>();
        downloadListsCompair = new ArrayList<>();

        progressBar = view.findViewById(R.id.progressbar_download_fragment);
        conNoData = view.findViewById(R.id.con_noDataFound);
        recyclerView = view.findViewById(R.id.recyclerView_download_fragment);

        conNoData.setVisibility(View.GONE);
        progressBar.setVisibility(View.GONE);

        recyclerView.setHasFixedSize(true);
        RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(getActivity());
        recyclerView.setLayoutManager(layoutManager);
        recyclerView.setFocusable(false);

        Dexter.withContext(getActivity())
                .withPermission(Manifest.permission.WRITE_EXTERNAL_STORAGE)
                .withListener(new PermissionListener() {
                    @Override
                    public void onPermissionGranted(PermissionGrantedResponse response) {
                        // permission is granted, open the camera
                        new Execute().execute();
                    }

                    @Override
                    public void onPermissionDenied(PermissionDeniedResponse response) {
                        // check for permanent denial of permission
                        if (response.isPermanentlyDenied()) {
                            // navigate user to app settings
                        }
                        method.alertBox(getResources().getString(R.string.cannot_use_save_permission));
                    }

                    @Override
                    public void onPermissionRationaleShouldBeShown(PermissionRequest permission, PermissionToken token) {
                        token.continuePermissionRequest();
                    }
                }).check();
        return view;
    }

    @SuppressLint("StaticFieldLeak")
    class Execute extends AsyncTask<String, String, String> {

        @Override
        protected void onPreExecute() {

            progressBar.setVisibility(View.VISIBLE);

            downloadLists.clear();
            inFiles.clear();

            downloadListsCompair.clear();

            db = new DatabaseHandler(getContext());
            downloadLists = db.getDownload();

            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... strings) {
            File file = new File(Constant.bookPath);
            getListFiles(file);
            getDownloadLists(inFiles);
            return null;
        }

        @Override
        protected void onPostExecute(String s) {

            if (downloadListsCompair.size() == 0) {
                conNoData.setVisibility(View.VISIBLE);
            } else {
                downloadAdapter = new DownloadAdapter(getActivity(), downloadListsCompair, "download", onClick);
                recyclerView.setAdapter(downloadAdapter);
                recyclerView.setLayoutAnimation(animation);
            }

            progressBar.setVisibility(View.GONE);
            super.onPostExecute(s);
        }
    }

    private List<File> getListFiles(File parentDir) {
        try {
            Queue<File> files = new LinkedList<>(Arrays.asList(parentDir.listFiles()));
            while (!files.isEmpty()) {
                File file = files.remove();
                if (file.isDirectory()) {
                    files.addAll(Arrays.asList(file.listFiles()));
                } else if (file.getName().endsWith(".epub") || file.getName().endsWith(".pdf")) {
                    inFiles.add(file);
                }
            }
        } catch (Exception e) {
            Log.d("error", e.toString());
        }
        return inFiles;
    }

    private List<DownloadList> getDownloadLists(List<File> list) {
        for (int i = 0; i < downloadLists.size(); i++) {
            for (int j = 0; j < list.size(); j++) {
                if (list.get(j).toString().contains(downloadLists.get(i).getUrl())) {
                    downloadListsCompair.add(downloadLists.get(i));
                    break;
                } else {
                    if (j == list.size() - 1) {
                        db.deleteDownloadBook(downloadLists.get(i).getId());
                    }
                }
            }
        }
        return downloadListsCompair;
    }

}
