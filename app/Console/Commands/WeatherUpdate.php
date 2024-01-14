<?php

namespace App\Console\Commands;

use App\Models\Weather;
use App\Models\User;

use Auth;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Request;
use Response;


class WeatherUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weather-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch daily weather updates from OpenWeatherMap Api using user location';

    /**
     * Execute the console command.
     */
    public function handle()
    {	
        try {
			//Get city from Database
			$id = Auth::user()->id;	
				$weatherData = User::find($id)->weather; // get weather by user
				if(!$weatherData){
					$city = "johannesburg";
				}else{
					$city = $weatherData->city;
				}

			// Get your OpenWeather API key
				$apiKey = env('OPENWEATHERMAP_APIKEY');
			// Get the desired city passed as function augument  

			// API endpoint URL with your desired location and units (e.g., London, Metric units)
			 $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$apiKey}";		
			// Make a GET request to the OpenWeather API
			$promise = Http::get($apiUrl); 
		
			// Get the response body as an array
			$weather = $promise->object();

			// Handle the retrieved weather data as needed 
			$created_at = Carbon::now();
			$updated_at = Carbon::now();
			if($weather){
				try {
					$user = User::find($id);
					$user->weather()->updateOrCreate(['city'=>$weather->name,
												'condition'=>$weather->weather[0]->main,'description'=>$weather->weather[0]->description,
												'icon'=>$weather->weather[0]->icon,'temp'=>$weather->main->temp,'temp_min'=>$weather->main->temp_min,
												'temp_max'=>$weather->main->temp_max,'pressure'=>$weather->main->pressure,'humidity'=>$weather->main->humidity,
												'wind_speed'=>$weather->wind->speed,'wind_deg'=>$weather->wind->deg,'created_at'=>$created_at,'updated_at'=>$updated_at]);
					$response = Response::json(['success' => ['message' => 'Weather update successful.', 'weather'=> $weather] ], 200);
				} catch(\Exception $e){
					$response = Response::json(['error' => ['message' => 'Database Error: Cannot save weather update!.'] ], 404);
				}
			}
			return $response;
        } catch (\Exception $e) {
            // Handle any errors that occur during the API request
				$response = Response::json(['error' => ['message' => 'Daily Weather Update Error!'] ]);
			$return $response;
        }		
		
    }
}
