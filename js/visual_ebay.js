$(function () {
    var map;
    var items = [];
    var markers = [];
    var titles = [];     // for asynchronous call of Geocoder, titles have to be stored first.
	var URLs = [];
	var infowindow = null;

    var latlng = new google.maps.LatLng(40, -102);
    var myOptions = {
        zoom:4,
        center:latlng,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);


    $("#search_form").submit(function () {
        var value = $("#search-text").val();
        value = $.trim(value);

        $.get('api.php?item_info=' + value, function (data) {
		//alert(data);
            data = eval('(' + data + ')');
            items = data.item;
            $("#sidebar ul").html("");
            for (var i = 0; i < data.item.length; i++) {
				
                var item = items[i];
                var title = item['title'];
                var link = item['viewItemURL']
                var pic = item['galleryURL'];
                $("#sidebar ul").append("<li><img src='" + pic + "'/><div class='info'><span class='item_title'><a href='" + link + "'>" + title + "</a></span></div><div style='clear:both' ></div></li>");
            }
			
			if(markers.length !=0 ){         
				for(var i=0; i< markers.length; i++){
					markers[i].setMap(null);      //disable the markers of last search
				}
				markers.length=0;
				markersInfo=[];
			}

            var geocoder = new google.maps.Geocoder();
			var index=0;
			titles = [];
			URLs= [];
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                var address = item['location'];
                geocoder.geocode({ 'address':address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        lat = results[0].geometry.location.lat();
                        lng = results[0].geometry.location.lng();
						CreateMaker(lat,lng,index);
                        index++;
						
				/*		if(index == i){
							for (var j=0; j< markers.length; j++){
								google.maps.event.addListener( markers[j], 'click', function(){
								//alert("clicked");
									var infowindow = new google.maps.InfoWindow(
										{	content: ""+j,
											size: new google.maps.Size(20,50)
									});
									alert(infowindow.content);
									infowindow.open(map,markers[j]);
								});
							}
						}  */
                    }

                });
				
				titles.push(item['title']);
				URLs.push(item['viewItemURL']);
            }
			
        })
        return false;

    })

    function CreateMaker(lat,lng,index) {
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(lat,lng),
			map: map,
            title:titles[index]
		});
		
		google.maps.event.addListener( marker, 'click', function(){
			if(infowindow != null){
				infowindow.close();
				infowindow=null;
			}
			
			infowindow = new google.maps.InfoWindow(
			{	content: "<a href=\'" + URLs[index] + "\' target=\'_blank\'>" + titles[index] + "</a>",
				size: new google.maps.Size(50,50)
			});	
				
			infowindow.open(map,marker);
			
			
		});
		
		markers.push(marker);
    }



});