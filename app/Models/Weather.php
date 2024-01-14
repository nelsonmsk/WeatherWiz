<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;
	
	protected $fillable = ['city','condition','description','icon','temp','temp_min','temp_max','pressure','humidity','wind_speed','wind_deg','user_id'];

	public function saveWeather($data)
	{
		$this->city = $data['city'];
		$this->condition = $data['condition'];
		$this->description = $data['description'];
		$this->icon = $data['icon'];	
		$this->temp = $data['temp'];		
		$this->temp_min = $data['temp_min'];
		$this->temp_max = $data['temp_max'];	
		$this->pressure = $data['pressure'];		
		$this->humidity = $data['humidity'];
		$this->wind_speed = $data['wind_speed'];
		$this->wind_deg = $data['wind_deg'];		
		$this->user_id = auth()->user()->id;	
		$this->save();
			return 1;
	}

	public function updateWeather($data)
	{
		$wt = $this::find($data['id']);
		$wt->city = $data['city'];
		$wt->condition = $data['condition'];
		$wt->description = $data['description'];
		$wt->icon = $data['icon'];
		$wt->temp = $data['temp'];		
		$wt->temp_min = $data['temp_min'];
		$wt->temp_max = $data['temp_max'];	
		$wt->pressure = $data['pressure'];		
		$wt->humidity = $data['humidity'];
		$wt->wind_speed = $data['wind_speed'];
		$wt->wind_deg = $data['wind_deg'];				
		$wt->user_id = $data['user_id'];	
		$wt->save();
			return 1;
	}
	
    /**
     * Get the user that owns the weather update.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }		
}
