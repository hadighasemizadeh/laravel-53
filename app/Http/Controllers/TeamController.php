<?php

namespace App\Http\Controllers;

use App\User;
use App\Teams;
use Validator;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:api', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $team = Teams::get();
        return $this->_result($team);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
//        print_r($data);

        $validator = Validator::make($data, [
            'name' => 'required|max:10',
            'owner_id' => 'required',
        ],
        [
             'name' => 'Required name field',
            'owner_id' => 'Required owner id field',

        ]);

        if($validator->fails())
        {
            $errors = $validator->errors()->all();
            return $this->_result($errors, 1, 'fail');
        }

        $team = Teams::create([
            'name' => $data['name'],
            'owner_id' => $data['owner_id'],
        ]);

        return $this->_result($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Teams::whereId($id)->first();

        if (empty($team)){
            return $this->_result('Team not exist', 1, "fail");
        } else {
            return $this->_result($team);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|max:20',
        ],
        [
            'name' => 'Required name field\'',
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors()->all();
            return $this->_result($errors, 1, 'fail');
        }
        $team = Teams::whereId($id)->first();
        $team->name = $data['name'];
        $team->save();

        return $this->_result($team);
    }

    public function destroy($id)
    {
        $team = Teams::whereId($id)->first();

        if (empty($team)){
            return $this->_result('Team not exists');
        } else {
            $team->delete();

            return $this->_result('Team'.$id.' removed with sucessfully');
        }
    }

    public function getusers($id)
    {

        $team = Teams::whereId($id)->first();

        if (empty($team)){

            return $this->_result('Team not exist');

        } else {

            $users = Teams::find($id)->users()->get();

            return $this->_result($users);
        }
    }

    public function getOwners($id)
    {
        $owners = Teams::find($id)->owners()->orderBy('name')->get();

        if ($owners->isEmpty()){
            return $this->_result('Team has not owners', 404, "fail");
        } else {
            return $this->_result($owners);
        }
    }

    public function addOwners(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'users' => 'required',
        ],
        [
            'users' => 'The users field is required',
        ]);

        if($validator->fails())
        {
            $errors = $validator->errors()->all();
            return $this->_result($errors, 400, 'fail');
        }

        $team = Teams::find($id);

        // Check if team exists
        if (empty($team)){
            return $this->_result('Team not exist', 404, "NOK");
        }

        $userID = $data['users'];
        $user = User::find($userID);

        // Check if user exists
        if (empty($user)){
            return $this->_result('User not exist', 404, "fail");
        }

        // Verify if the user is already a owner
        $hasUser = $team->owners()->where('id', $userID)->exists();

        if($hasUser != 1){
            // Attach new owner to the team
            $team->owners()->attach($userID);

            // Notify the user
            //$user = User::whereId($userID)->first();
            //$user->notify(new AddedToList($listID));

            return $this->_result('User successfuly added');
        } else {
            return $this->_result('User is already a team owner', 400, "fail");
        }
    }

    public function removeOwners(Request $request, $id)
    {
        $data = $request->all();

        $team = Teams::whereId($id)->first();

        // Check if team exists
        if (empty($team)){
            return $this->_result('team not exists', 404, "fail");
        }

        $userID = $data['users'];

        $user = User::find($userID);

        // Check if user exists
        if (empty($user)){
            return $this->_result('User not exist', 404, "fail");
        }

        // Verify if the user is already a owner
        $hasUser = $team->owners()->where('id', $userID)->exists();

        if($hasUser == 1){
            // Detach user of the list
            $team->owners()->detach($user);

            // Notify the user
            //$user = User::whereId($userID)->first();
            //$user->notify(new AddedToList($listID));

            return $this->_result('User successfuly removed');
        } else {
            return $this->_result('User was not a team owner', 400, "fail");
        }
    }
}
