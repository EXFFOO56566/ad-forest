package com.scriptsbundle.adforest.modelsList;

public class homeCatListModel {
    private String title;
    private String thumbnail;
    private String id;
    private boolean has_children;
    private String adsCount;
    private String color;

    public String getColor() {
        return color;
    }

    public String setColor(String color) {
        this.color = color;
        return color;
    }

    public boolean isHas_children() {
        return has_children;
    }

    public void setHas_children(boolean has_children) {
        this.has_children = has_children;
    }

    public String getAdsCount() {
        return adsCount;
    }

    public void setAdsCount(String adsCount) {
        this.adsCount = adsCount;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getThumbnail() {
        return thumbnail;
    }

    public void setThumbnail(String thumbnail) {
        this.thumbnail = thumbnail;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

}
