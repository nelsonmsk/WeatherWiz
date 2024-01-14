<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Response;
use Auth;

use App\Models\User;
use App\Models\Profile;


class UserController extends Controller {

    public function __construct() {
        $this->middleware(['auth']); //Admin middleware lets only users with a //specific permission permission to access these resources
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request) {
    //Get all users and pass it to the view
		if($request->has('search_text')){
			try{
				$users = User::Search($request->search_text)->simplePaginate(15);//Get all users
				$status = '200';
				return	View('users.pages.index',compact('users','status'));
				
			}catch(Exception $e){
				return	View('users.pages.index');
			}
		}else{
			try{
				$users = User::latest()->simplePaginate(15);//Get all users
				$status = '200';
				return	View('users.pages.index',compact('users','status'));
				
			}catch(Exception $e){
				return	View('users.pages.index');
			}
		}
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create() {	
	
		return view('users.pages.create');
    }

    /**$newuser->id
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request) {
	
		try{
			$user = new User();
	
			//Validate name, email and password fields
			   $data = $this->validate($request, [
					'name'=>'required|max:120',
					'email'=>'required|email|unique:users',
					'type'=>'required',
					'password'=>'required|min:6|confirmed'
				]);
				
			$user->saveUser($data); //Retrieving only the email and password data
	
			$response = Response::json(['success' => ['message' => 'User has been successfully added.','data' => $user,] ], 201); 
			
			return $response;
			
		}catch(Exception $e){
			
			$response = Response::json(['error' => ['message' => 'User cannot be created, validation error!'] ], 422);
			
			return 	$response;		
		}					 
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id) {
		
 		try{
			$user = User::findOrFail($id); //Get user with specified id
			$status = '200';
			return	View('users.pages.show',compact('user','status'));
			
		}catch(Exception $e){

			$response = Response::json(['error' => ['message' => 'User cannot be found.'] ], 404);
			
			return 	$response;
	   }
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id) {
		try{
			$user = User::findOrFail($id); //Get user with specified id
			$status = '200';
			return	View('users.pages.edit',compact('user','status'));
			
		}catch(Exception $e){

			$response = Response::json(['error' => ['message' => 'User cannot be found.'] ], 404);
			
			return 	$response;
	   }			

    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id) {
		try{
			$user = User::findOrFail($id); //Get role specified by id

		//Validate name, email and password fields    
			$data = $this->validate($request, [
				'name'=>'required|max:120',
				'email'=>'required|email|unique:users,email,'.$id,
				'type'=>'required',				
				'password'=>'required|min:6|confirmed'
			]);
			$data['id'] = $id;

			$user-> updateUser($data);
			
			$response = Response::json(['success' => ['message' => 'User has been updated.','data' => $user,] ], 200); 
				
			return  $response;;
				 
		}catch(Exception $e){
			
			$response = Response::json(['error' => ['message' => 'User cannot be updated, validation error!'] ], 422);
			
			return 	$response;		
		}				 
			 
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id) {
    //Find a user with a given id and delete
		try{	
			$user = User::findOrFail($id); 
			$user->delete();

			$response = Response::json(['success' => ['message' => 'User has been deleted.'] ], 200); 
				
			return  $response;
				 
		}catch(Exception $e){
			
			$response = Response::json(['error' => ['message' => 'User cannot be found.'] ], 404);
			
			return 	$response;		
		}				 
    }
}