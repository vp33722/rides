<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Ride;
use JWTAuth;
use DB;
use App\Http\Resources\Ride as RideCollection;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Controllers\Controller;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
class RideController extends Controller
{

	protected $user;
	public function __construct()
		{
    		$this->user = JWTAuth::parseToken()->authenticate();
		}
    public function store(Request $request)
		{
			$rides = new Ride();
		    $rides->date = $request->date;
		    $rides->time = $request->time;
		    $rides->from_place = $request->from_place;
		    $rides->to_place=$request->to_place;
		    $rides->routes=$request->routes;
		    $rides->seats=$request->seats;

		    if ($this->user->rides()->save($rides))
		    {
		    	$rides=Ride::with('users')->where('time',$rides->time)->get();
		        return response()->json([
		            'success' => true,
		            'Message' =>'Rides Add Successfully',
		            'rides' =>RideCollection::collection($rides),
		        ]);
		    }
		}
	public function update(Request $request,$id)
	{
		$rides = $this->user->rides()->find($id);
		$updated = $rides->fill($request->all())->save();
		if(count($updated)>0){
			$rides=Ride::with('users')->where('id',$id)->get();
			 return response()->json([
		            'success' => true,
		            'Message' =>'Rides Updated Successfully',
		            'rides' =>RideCollection::collection($rides),
		        ]);
		}
	}
	public function destroy($id)
	{
		$rides = $this->user->rides()->find($id);
		if ($rides->delete()) {
		        return response()->json([
		            'success' => true,
		            'Message' => 'Rides Deleted SuccessFully'
		        ]);
		    }
	}
}
