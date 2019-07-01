<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Map</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('mapIr/dist/css/s.map.min.css') }}">
        <link rel="stylesheet" href="{{ asset('mapIr/dist/css/fa/style.css') }}">
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
                     <form method="post" action="{{ route('map.store') }}" class="mt-4 mb-4">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user_id }}">
                        <input type="hidden" name="latitude" id="user_latitude" value="{{ $lat }}">
                        <input type="hidden" name="longitude" id="user_longitude" value="{{ $long }}">
                        <input type="hidden" name="province"  value="{{ $province }}">
                        <input type="hidden" name="state"  value="{{ $state }}">
                        <input type="hidden" name="city"  value="{{ $city }}">
                        <input type="submit" class="btn btn-primary btn-block" name="submit" value="ثبت موقعیت">
                    </form>
                </div>
            </div<
        </div>
    </body>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('mapIr/dist/js/jquery.env.js') }}"></script>
    <script src="{{ asset('mapIr/dist/js/s.map.styles.js') }}"></script>
    <script src="{{ asset('mapIr/dist/js/s.map.min.js') }}"></script>
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




            // if previous location of user was found
            // initialize a marker from previous location
            @if($previous_lat)
                var previous_lat = "{{ $previous_lat }}";
                var previous_long = "{{ $previous_long }}";
                $.sMap.features.marker.create({
                        name: 'previous_location',
                        popup: {
                            title: {
                                html: 'مکان قبلی ثبت شده ی شما در سیستم',
                                i18n: ''
                                },
                            description: {
                                html: `
                                    <div>Lat: ${previous_lat} </div>
                                    <div>Long: ${previous_long}</div>`,
                                i18n: ''
                                },
                            custom: false
                        },
                        latlng: {
                            lat: previous_lat,
                            lng: previous_long,
                        },
                        popupOpen: true,
                        draggable: false,
                        toolbar: []
                    });
            @endif


            // What is called after map instance is created
            function afterMapInitialized() {
                    // Change cursor to a marker icon (uneccessary)
                    // So The curser be a Marker to represent adding marker
                $('.leaflet-container').addClass("cursor-marker");
                map.on('click', (event) => {
                    $.sMap.features.marker.create({
                        name: 'user_location',
                        popup: {
                            title: {
                                html: 'مکان انتخابی شما',
                                i18n: ''
                                },
                            description: {
                                html: `
                                    <div>Lat: ${event.latlng.lat} </div>
                                    <div>Long: ${event.latlng.lng}</div>`,
                                i18n: ''
                                },
                            custom: false
                        },
                        latlng: event.latlng,
                        popupOpen: true,
                        draggable: true,
                        toolbar: []
                    });
                    // logging latlng to console
                    console.log(event.latlng);

                    // updating hidden inputs of forms for submit
                    $("#user_longitude").val(event.latlng.lng);
                    $("#user_latitude").val(event.latlng.lat);
                });
            }
        });
    </script>
</html>
