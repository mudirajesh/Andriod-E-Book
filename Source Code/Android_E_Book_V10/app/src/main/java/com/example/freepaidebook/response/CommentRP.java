package com.example.freepaidebook.response;

import com.example.freepaidebook.item.CommentList;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.List;

public class CommentRP implements Serializable {

    @SerializedName("status")
    private String status;

    @SerializedName("message")
    private String message;

    @SerializedName("all_comments")
    private List<CommentList> commentLists;

    public String getStatus() {
        return status;
    }

    public String getMessage() {
        return message;
    }

    public List<CommentList> getCommentLists() {
        return commentLists;
    }
}
