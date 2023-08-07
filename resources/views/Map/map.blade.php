<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>


    <title>Document</title>
</head>
<body>

    <div id="map" style="width:100%;height:90vh">

    </div>
    
    <script type="text/javascript">
        var mymap = L.map('map').setView([{{ $lat }},{{ $long }}],10);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?{foo}', 
        {foo: 'bar', attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(mymap);


        var polyline_latlong = [
            [{{ $lat }},{{ $long }}]
        ]

        L.marker([{{ $lat }},{{ $long }}]).addTo(mymap)

        L.Routing.control({
            waypoints: [
                L.latLng({{ $lat }},{{ $long }}),
            ],
            routeWhileDragging: true,
            lineOptions:{
                styles:[
                    {
                        color:"black",
                        opacity:1,
                        weight:3
                    }
                ]
            },
            
        }).addTo(mymap);

        var tooltip = L.tooltip(polyline_latlong, {content: 'Hello world!<br />This is a nice tooltip.'})
    .addTo(mymap);


    </script>


</body>
</html>