<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Map</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('map/dist/css/s.map.min.css') }}">
        <link rel="stylesheet" href="{{ asset('map/dist/css/fa/style.css') }}">
        <style>
            #map {
                width: 100%;
                height: 600px;
            }
        </style>
    </head>
    <body>
        <div class="cotainer">
            <div class="row justify-content-center mt-4">
                <div class="col-sm-10">
                    <h2>here</h2>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </body>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('map/dist/js/jquery.env.js') }}"></script>
    <script src="{{ asset('map/dist/js/s.map.styles.js') }}"></script>
    <script src="{{ asset('map/dist/js/s.map.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        // Initial map instance
            const map = $.sMap({
                mode: 'development',
                element: '#map',
                presets: {
                    latlng: {
                        lat: {{ $lat }},
                        lng: {{ $long }},
                    },
                    zoom: {{ $zoom }},
                },
                after: afterMapInitialized
            });


            // Add base Layer to sMap plugin (required)
            // Todo: check documentation for alternative layers
            $.sMap.layers.static.build({
                layers: {
                    base: {
                        default: {
                            server: 'https://map.ir/shiveh',
                            layers: 'Shiveh:ShivehGSLD256',
                            format: 'image/png',
                        },
                    },
                },
            });

            //  Initialize the marker feature
            $.sMap.features();

            // adding fullscreen buuton (top right fo map)
            $.sMap.fullscreen.implement();

            // adding get location button (button right)
            // uses default browser location history if gps not available
            $.sMap.userLocation.implement({
                history: true,
            });


            // What is called after map instance is created
            function afterMapInitialized() {
                    // Change cursor to a marker icon (uneccessary)
                    // So The curser be a Marker to represent adding marker
                $('.leaflet-container').addClass("cursor-marker");
                map.on('click', (event) => {
                    $.sMap.features.marker.create({
                        name: 'مکان انتخابی شما',
                        popup: {
                            title: {
                                html: 'مکان انتخابی شما',
                                },
                            description: {
                                html: `
                                    <div>Lat: ${event.latlng.lat} </div>
                                    <div>Long: ${event.latlng.lng}</div>`,
                                },
                            custom: false
                        },
                        latlng: event.latlng,
                        popupOpen: true,
                        draggable: true,
                        toolbar: []
                    });
                    // logging latlng to console
                    console.log(event.latlng.lat);
                });
            }
        });
    </script>
</html>
