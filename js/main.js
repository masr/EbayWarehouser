(function ($) {
    'use strict';
    var VMap = {
        houseMarkers:[],
        drawAllTrans:function (seller_id) {
            var imageSmall = new google.maps.MarkerImage('img/dot-small.png', new google.maps.Size(9, 8), new google.maps.Point(0, 0), new google.maps.Point(0, 8)),
                imageBig = new google.maps.MarkerImage('img/dot-medium.png', new google.maps.Size(31, 29), new google.maps.Point(0, 0), new google.maps.Point(0, 29)),
                self = this;
            self.points = [];

            $.getJSON("api.php", {seller_id:seller_id, type:'all_trans'}, function (data) {
                $.each(data, function (index, record) {
                    self.points.push(new google.maps.Marker({
                        map:self.map,
                        position:new google.maps.LatLng(record['latitude'], record['longtitude']),
                        icon:record['counter'] <= 5 ? imageSmall : imageBig,
                        title:record['counter']
                    }));

                });
            });

            return self;
        },

        createUserPosition:function (lat, lng) {
            var marker = new google.maps.Marker({
                map:this.map,
                position:new google.maps.LatLng(lat, lng)
            });
        },

        drawAllWarehouses:function () {
            self = this;
            self.points = [];

            $.getJSON("api.php?type=all_warehouses", function (data) {
                $.each(data, function (index, record) {
                    self.createWarehouse(record['latitude'], record['longtitude'], record);
                });
            });

            return self;
        },
        clearHouseMarkers:function () {
            for (var i in this.houseMarkers) {
                this.houseMarkers[i].setMap(null);
            }
        },
        createWarehouse:function (lat, lng, warehouseData) {
            var imageWarehouse = new google.maps.MarkerImage('img/warehouse.png', new google.maps.Size(30, 23), new google.maps.Point(0, 0), new google.maps.Point(0, 23));
            var marker = new google.maps.Marker({
                map:this.map,
                position:new google.maps.LatLng(lat, lng),
                icon:imageWarehouse
            });
            this.houseMarkers.push(marker);
            var self = this;

            google.maps.event.addListener(marker, 'click', function () {
                if (self.infowindow) {
                    self.infowindow.close();
                    self.infowindow = null;
                }

                var contentTemplate = ['<div id="content" style="color:#6440ff">',
                    '<img src="img/logo.png" height="10" width="50" />',
                    '<br/>{{city}}<br/> {{state}} <br />  {{zip_code}}<br/>{{warehouse_id}}',
                    '</div>'].join('\n');
                self.infowindow = new google.maps.InfoWindow({
                    content:$.mustache(contentTemplate, warehouseData),
                    size:new google.maps.Size(50, 60)
                });

                self.infowindow.open(self.map, marker);
            });
        }

    };

    $.fn.VMap = function (options) {
        var init = function (options) {
            // this -> $ element
            this.settings = $.extend({
                'initLat':40,
                'initLng':-102,
                'initZoom':4,
                'disable':false
            }, options);

            this.vMap = VMap;

            var url = "http://maps.google.com/maps/api/js?key=AIzaSyDourOJcQsTesK0XiSdxfYmyU3y4rFYX4U&sensor=true&callback=initVMap";
            window.initVMap = $.proxy(function () {
                var myOptions = {
                    center:new google.maps.LatLng(this.settings.initLat, this.settings.initLng),
                    zoom:this.settings.initZoom,
                    mapTypeId:google.maps.MapTypeId.ROADMAP
                };
                if (this.settings.disable) {
                    myOptions = $.extend(myOptions, {
                        disableDefaultUI:true,
                        disableDoubleClickZoom:true,
                        draggable:false
                    });
                }
                this.vMap.map = new google.maps.Map(this.get(0), myOptions);
                this.trigger('vmap.initComplete', this.vMap);
            }, this);
            $.getScript(url);
            return this;
        };

        return init.apply(this, arguments);
    };

}(jQuery));
