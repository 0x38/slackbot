<?php

define('BOT_USERNAME', 'slackbot'); // This is the default name of the bot if you don't specify one in a personality

define('FORECASTIO_API', ''); // Get an API key at https://developer.forecast.io/
define('FORECASTIO_LATLON', '45.416972,-75.705242'); //The Latitude,Longitude of the location you want the weather for
define('FORECASTIO_UNITS', 'si'); //Whether you want F or C. See https://developer.forecast.io/docs/v2 for valid options

define('GEOCODER_API', ''); // Google geocoder API key for advanced weather support. Visit https://developers.google.com/maps/documentation/geocoding/ for more info.