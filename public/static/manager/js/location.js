
App.location = {
    urls: {},
    init_list: ["urls"],
    regionID: 0,
    districtID: 0,

    changeRegionID: function (value, text, el) {
        if (value != this.regionID) {
            console.log("change Region")
            this.regionID = value;
            App.htmlSelect.deactive('district_id');
            App.htmlSelect.deactive('ward_id');
            App.htmlSelect.changeOptions('district_id', { "": "Chọn một" });
            App.htmlSelect.changeOptions('ward_id', { "": "Chọn một" });
            if (value) {
                App.api.get(this.urls.district_options, {
                    region_id: value
                }).then(function (res) {
                    if (res.status) {
                        App.htmlSelect.changeOptions('district_id', res.data);
                    }
                });
            }
        }
    },

    changeDistrictID: function (value, text, el) {
        if (value != this.districtID) {
            console.log("change District")
            this.districtID = value;
            App.htmlSelect.deactive('ward_id');
            App.htmlSelect.changeOptions('ward_id', { "": "Chọn một" });
            if (value) {
                App.api.get(this.urls.ward_options, {
                    district_id: value
                }).then(function (res) {
                    if (res.status) {
                        App.htmlSelect.changeOptions('ward_id', res.data);
                    }
                });
            }
        }
    }
};
