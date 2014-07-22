<?php 
/*
Plugin Name: Wunderground Historical Data
Plugin URI: http://blackreit.com
Description: Search historical records data from Wunderground API
Author: Matthew M. Emma
Version: 1.0
Author URI: http://www.blackreit.com
*/
add_shortcode('hw','wunderground_history');
function wunderground_history( $atts ) { // ($city, $state, $year, $month, $day) {
  wp_register_style( 'weatherformat', plugins_url('css/weatherformat.css', __FILE__) );
  wp_enqueue_style( 'weatherformat' );
  wp_register_style( 'weatherfont', plugins_url('css/weather-icons.css', __FILE__) );
  wp_enqueue_style( 'weatherfont' );
  extract( shortcode_atts( array(
    'city' => 'New_York',
    'state' => 'NY',
    'y' => '1986',
    'm' => '11',
    'd' => '27'
  ), $atts, 'hw' ) );
  $json_string = file_get_contents('http://api.wunderground.com/api/b8e924a8f008b81e/history_' . $y . $m . $d . '/q/' . $state . '/' . $city . '.json');
  $parsed_json = json_decode($json_string);
  $dailysummary = $parsed_json->{'history'}->{'dailysummary'}[0];
  $observations = $parsed_json->{'history'}->{'observations'};
  $obsarray = array();
  for ($i = 1; $i <= 3; $i++) {
    array_push($obsarray, $observations[count($observations) * $i / 4]);
  }

  echo '<div class="weatherformat">';

  foreach ($obsarray as $obs) {
    echo '<div class="col-1-3">'.$obs->{'date'}->{'pretty'}.'<br><br>'.wunderground_to_icon($obs->{'conds'}, 72).'<br><br>'.$obs->{'conds'}.'</div>';
  }

  echo '<div class="wf-content">';
  echo '<div class="col-1-3">Temperature Range: [' . $dailysummary->{'mintempi'} . ',' . $dailysummary->{'maxtempi'} . ']</div>';
  echo '<div class="col-1-3">Avg. Wind Speed: <br>' . $dailysummary->{'meanwindspdi'} . ' mph</div>';
  echo '<div class="col-1-3">Avg. Visibility: <br>' . $dailysummary->{'meanvisi'} . ' miles</div>';
  if(strcmp($dailysummary->{'rain'}, "1") == 0) {
    if(strcmp($dailysummary->{'snow'}, "1") == 0) {
      echo '<div class="col-1-2">Rainfall: ' . $dailysummary->{'precipi'} . '</div>';
      echo '<div class="col-1-2">Snowfall: ' . $dailysummary->{'snowfalli'} . '</div>';
    } else {
      echo '<div class="col-1-1">Rainfall: ' . $dailysummary->{'precipi'} . '</div>';
    }
  } else {
    if(strcmp($dailysummary->{'snow'}, "1") == 0) {
      echo '<div class="col-1-1">Snowfall: ' . $dailysummary->{'snowfalli'} . '</div>';
    }
  }
  
  echo '</div></div>';
}

function wunderground_to_icon( $status, $size ) {
  if (strncmp($status, 'Light', 5) == 0 || strncmp($status, 'Heavy', 5) == 0) {
      $status = substr($status, 6);
  }
  $icons = array(
    'Drizzle' => 'wi-day-sprinkle',
    'Rain' => 'wi-day-rain',
    'Snow' => 'wi-day-snow',
    'Snow Grains' => 'wi-day-snow',
    'Ice Crystals' => 'wi-day-snow',
    'Ice Pellets' => 'wi-day-snow',
    'Hail' => 'wi-day-hail',
    'Mist' => 'wi-day-fog',
    'Fog' => 'wi-day-fog',
    'Fog Patches' => 'wi-day-fog',
    'Smoke' => 'wi-smoke',
    'Volcanic Ash' => 'wi-smog',
    'Widespread Dust' => 'wi-dust',
    'Sand' => 'wi-dust',
    'Haze' => 'wi-smog',
    'Spray' => 'wi-day-sprinkle',
    'Dust Whirls' => 'wi-dust',
    'Sandstorm' => 'wi-tornado',
    'Low Drifting Snow' => 'wi-day-snow',
    'Low Drifting Widespread Dust' => 'wi-dust',
    'Low Drifting Sand' => 'wi-dust',
    'Blowing Snow' => 'wi-day-snow-wind',
    'Blowing Widespread Dust' => 'wi-dust',
    'Blowing Sand' => 'wi-dust',
    'Rain Mist' => 'wi-day-sprinkle',
    'Rain Showers' => 'wi-day-showers',
    'Snow Showers' => 'wi-day-snow',
    'Snow Blowing Snow Mist' => 'wi-day-snow-wind',
    'Ice Pellet Showers' => 'wi-day-hail',
    'Hail Showers' => 'wi-day-hail',
    'Small Hail Showers' => 'wi-day-hail',
    'Thunderstorm' => 'wi-day-thunderstorm',
    'Thunderstorms and Rain' => 'wi-day-storm-showers',
    'Thunderstorms and Snow' => 'wi-day-snow-thunderstorm',
    'Thunderstorms and Ice Pellets' => 'wi-day-snow-thunderstorm',
    'Thunderstorms with Hail' => 'wi-day-snow-thunderstorm',
    'Thunderstorms with Small Hail' => 'wi-day-snow-thunderstorm',
    'Freezing Drizzle' => 'wi-day-rain-mix',
    'Freezing Rain' => 'wi-day-rain-mix',
    'Freezing Fog' => 'wi-day-fog',
    'Patches of Fog' => 'wi-day-fog',
    'Shallow Fog' => 'wi-day-fog',
    'Partial Fog' => 'wi-day-fog',
    'Overcast' => 'wi-day-sunny-overcast',
    'Clear' => 'wi-day-sunny',
    'Partly Cloudy' => 'wi-day-cloudy',
    'Mostly Cloudy' => 'wi-day-cloudy',
    'Scattered Clouds' => 'wi-day-cloudy',
    'Small Hail' => 'wi-day-snow',
    'Squalls' => 'wi-day-cloudy-gusts',
    'Funnel Cloud' => 'wi-tornado',
    'Unknown Precipitation' => 'wi-day-rain',
    'Unknown' => 'wi-day-sunny'
  );
  return '<i style="font-size: '.$size.'px;" class="wi '.$icons[$status].'"></i>';
}

function months( $month_id ) {
  $months = array(
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'Septemeber',
    10 => 'October',
    11 => 'November',
    12 => 'December'
  );
  return $months[$month_id];
}

function Quartile($Array, $Quartile) {
  $pos = (count($Array) - 1) * $Quartile;
 
  $base = floor($pos);
  $rest = $pos - $base;
 
  return $Array[$base];
}