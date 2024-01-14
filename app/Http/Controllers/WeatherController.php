<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Weather;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class WeatherController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
			function getLocation($ipAddress)
			{
				// Get your ipGeolocation API key
				$apiKey = env('IPGEOLOCATION_APIKEY');
				// Get the desired ip passed as function augument
				$ip = $ipAddress;   
				// API endpoint URL with your desired ip and units (e.g., Ip address, Metric units)
				$apiUrl = "https://api.ipgeolocation.io/ipgeo?apiKey={$apiKey}&ip={$ip}";
				try {
					// Make a GET request to the ipGeolocation API
					$promise = Http::get($apiUrl);
				// Get the response body as an object
					$data = $promise->object();
					$location = $data->city;
					// Handle the retrieved location data as needed
					return $location;
				} catch (\Exception $e) {
					// Handle any errors that occur during the API request
						//$error = $e->getMessage();
						$error = '';
					 return $error; 
				}
			}	
			
			 /**
			 * Look up weather status by city or coordinates.
			 */
			function lookUp($searchField)
			{
				// Get your OpenWeather API key
				$apiKey = env('OPENWEATHERMAP_APIKEY');
				// Get the desired city passed as function augument
				$field = $searchField;  
				// API endpoint URL with your desired location and units (e.g., London, Metric units)
				$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$field}&units=metric&appid={$apiKey}";
				try {
					// Make a GET request to the OpenWeather API
					$promise = Http::get($apiUrl);
					// Get the response body as an array
					$response = $promise->object();
					return $response;
				} catch (\Exception $e) {
					// Handle any errors that occur during the API request
						$response = '';				
					 return $response; 
				}	
			} 
		if($request->has('search_text')){	
		    try{		
				$weather = Weather::Search($request->search_text)->SimplePaginate(14) ; //Get all weather
				$response = Response::json(['success' => ['weather'=> $weather] ], 200);	
				return 	$response;				
			}catch(Exception $e){
				$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);
				return $response;				
		    }
		}else{
				$user = Auth::user();		
				//Get the ip address of the user
				$ipAdress = request->ip();
				$weather = $user->weather()->latest()->get() ; //Get all weather data
				if(!$weather){
					$location = getLocation($ipAddress));
					if(!$location){
						$status = "404";
						$response = Response::json(['error' => ['message' => 'Weather cannot be found. Error fetching location city'] ], 404);	
						return 	$response;
					}else {
						//Get weather status from OpenWeather Api
						$data= lookUp($location);
						if(!$data){
							$status = "404";
							$response = Response::json(['error' => ['message' => 'Weather cannot be found. Error fretching weather updates!'] ], 404);	
						return 	$response;
						}else{
							$weather = $data;
						}
					}
				}
				$response = Response::json(['success' => ['weather'=> $weather] ], 200);	
				return 	$response;
		}
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
		try{
			$data = $this->validate($request, [
				'city'=>'required|max:60',
				'condition'=>'required|max:30',
				'description'=>'required|max:30',
				'icon'=>'required|max:30',
				'temp'=>'required|max:30',	
				'temp_min'=>'required|max:30',
				'temp_max'=>'required|max:30',	
				'pressure'=>'required|max:30',		
				'humidity'=>'required|max:30',
				'wind_speed'=>'required|max:30',
				'wind_deg'=>'required|max:30',	
			]);
		   
			$user = Auth::user();
			$weather = new Weather($data);
			$user->weather()->save($weather);
			$response = Response::json(['success' => ['message' => 'Weather has been created successfully.'] ], 201); 
			return  $response;	
			
		}catch(Exception $e){
			$response = Response::json(['error' => ['message' => 'Weather cannot be created, validation error!'] ], 422);
			return 	$response;		
		}
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
		try{
			$user = Auth::user();
			$weather = $user->weather()->findOrFail($id); //Find Service of id = $id
			$response = Response::json(['success' => ['message' => 'Weather cannot be found.', 'weather'=> $weather] ], 200);	
			return 	$response;	
			
		}catch(Exception $e){
			$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);
			return 	$response;
	   }	
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
		try{
			$weather = new Weather();
			$data = $this->validate($request, [
				'city'=>'required|max:60',
				'condition'=>'required|max:30',
				'description'=>'required|max:30',
				'icon'=>'required|max:30',
				'temp'=>'required|max:30',	
				'temp_min'=>'required|max:30',
				'temp_max'=>'required|max:30',	
				'pressure'=>'required|max:30',		
				'humidity'=>'required|max:30',
				'wind_speed'=>'required|max:30',
				'wind_deg'=>'required|max:30',					
			]);
			
		    $data['id'] = $id;
			$user = Auth::user();
			$user->weather()->fill($data)->save;
			$response = Response::json(['success' => ['message' => 'Weather has been updated.'] ], 200); 	
			return  $response;	
			
		}catch(Exception $e){
			$response = Response::json(['error' => ['message' => 'Weather cannot be updated, validation error!'] ], 422);
			return 	$response;		
		}
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
		try{
			$user = Auth::user();	
			$weather = $user->weather()->findOrFail($id); //Find Weather of id = $id			
			$weather->delete();	
			$response = Response::json(['success' => ['message' => 'Weather has been deleted.'] ], 201); 	
			return  $response;	
			
		}catch(Exception $e){	
			$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);
			return 	$response;		
		}	
    }
	
     /**
     * Find the specified resource by Date.
     */
    public function getByDate($date)
    {
		try{
			$user = Auth::user();
			$weather = $user->weather()->where('updated_at',$date)->get(); //Find Service of id = $id
			$response = Response::json(['success' => ['message' => 'Weather cannot be found.', 'weather'=> $weather] ], 200);	
			return 	$response;	
			
		}catch(Exception $e){
			$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);
			return 	$response;
	   }	
    }   
	
	
	 /**
     * Find the specified resource by Date.
     */
    public function getByRandomCity($city)
    {
			 /**
			 * Look up weather status by city or coordinates.
			 */
			function lookUp($searchField)
			{
				// Get your OpenWeather API key
				$apiKey = env('OPENWEATHERMAP_APIKEY');
				// Get the desired city passed as function augument
				$field = $searchField;  
				// API endpoint URL with your desired location and units (e.g., London, Metric units)
				$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$field}&units=metric&appid={$apiKey}";
				try {
					// Make a GET request to the OpenWeather API
					$promise = Http::get($apiUrl);
					// Get the response body as an array
					$response = $promise->object();
					return $response;
				} catch (\Exception $e) {
					// Handle any errors that occur during the API request
						$error = $e->getMessage();
					$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);				
					 return $response; 
				}	
			} 		
		try{
			//Get weather status from OpenWeather Api
			$weather = lookUp($city);
			$response = Response::json(['success' => ['weather'=> $weather] ], 200);	
			return 	$response;
		}catch(Exception $e){
			$response = Response::json(['error' => ['message' => 'Weather cannot be found.'] ], 404);
			return 	$response;				
		}
    }  	
}
