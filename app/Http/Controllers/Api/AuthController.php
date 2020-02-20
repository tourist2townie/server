<?php

namespace App\Http\Controllers\Api;

use App\guides;
use App\tourists;
use App\tours;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\inquiries;
use App\places;
use App\timelines;
use App\packages;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed',
            'userType' => 'required',
            'status'=>'required',
        ]);



        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        if ($request->userType == 'guide') {

            DB::table('guides')->insert(array('id' => $request->id, 'name' => $request->name, 'email' => $request->email, 'password' => $request->password,));
        } elseif ($request->userType == 'tourist') {

            DB::table('tourists')->insert(array('id' => $request->id, 'name' => $request->name, 'email' => $request->email, 'password' => $request->password));
        }

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'accesstoken' => $accessToken]);
    }

    public function login(Request $request)
    {

        $loginData = $request->validate([

            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'accesstoken' => $accessToken]);
    }

    public function profileRetrieve($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function profileTripsRetrieve($id)
    {
        $trips = tours::where([

            ['status', '=', 'Completed'],
            ['tourist_id', '=', $id],
        ])->get();
        return response()->json($trips);
    }

    public function profileRetrieveGuide($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function profileUpdate(Request $request, $id)
    {
        $user = User::find($id);
        // $user->password = $request->input('na');
        $user->email = $request->input('email');
        $user->profile_image = $request->input('profile_image');
        $user->save();
        return response()->json($user);
    }

    public function RetrieveGuides()
    {
        $user = User::where(
            [
                ['userType', '=', 'guide'],
                ['status','=','Accepted'],
            ])->get();
        return response()->json($user);
    }

    public function addTimeline(Request $request)
    {
        $timeline = new timelines();

        $timeline->place = $request->input('place');
        $timeline->date = $request->input('date');
        $timeline->image = $request->input('image');
        $timeline->save();
        return response()->json($timeline);
    }

    public function retrieveTimeline()
    {
        $timeline = timelines::all();
        return response()->json($timeline);
    }

    public function makeTour(Request $request)
    {
        $tours = new tours();
        $tours->tourist_id = $request->input('tourist_id');
        $tours->guide_id = $request->input('guide_id');
        $tours->tour_type = $request->input('tour_type');
        $tours->place = $request->input('place');
        $tours->date = $request->input('date');
        $tours->No_of_days = $request->input('No_of_days');
        $tours->status = $request->input('status');

        $tours->save();
        return response()->json($tours);
    }

    public function retrieveRequestedTours()
    {
        $tours = tours::where('status', '=', 'Requested')->get();
        return response()->json($tours);
    }

    public function completeTour(Request $request)
    {
        $tours = new tours();
        $tours->tourist_id = $request->input('tourist_id');
        $tours->guide_id = $request->input('guide_id');
        $tours->tour_type = $request->input('tour_type');
        $tours->place = $request->input('place');
        $tours->date = $request->input('date');
        $tours->No_of_days = $request->input('Nore_of_days');
        $tours->status = $request->input('status');

        $tours->save();
        return response()->json($tours);
    }

    public function tripStatusUpdate(Request $request, $id)
    {
        $tripStatus = tours::find($id);
        $tripStatus->status = $request->input('status');
        $tripStatus->save();
        return response()->json($tripStatus);
    }

    public function retrieveOngoingTrip()
    {
        $tours = tours::where('status', '=', 'Ongoing')->first();
        return response()->json($tours);
    }

    public function guideRating(Request $request, $id)
    {
        $guideRating = tours::find($id);
        $guideRating->guide_rating = $request->input('guide_rating');
        $guideRating->save();
        return response()->json($guideRating);
    }


    //Guides functions

    public function tripRequestsPending($id)
    {
        $tours = tours::where([
            ['guide_id', '=', $id],
            ['status', '=', 'Pending'],
        ])->get();
        return response()->json($tours);
    }
    //guide requested trips
    public function tripRequests($id)
    {
        $tours = tours::where([
            ['guide_id', '=', $id],
            ['status', '=', 'Requested'],
        ])->get();
        return response()->json($tours);
    }

    public function currentTrip($id)
    {
        $tours = tours::where(
            [
                ['guide_id', '=', $id],
                ['status', '=', 'Ongoing'],
            ]
        )->first();
        return response()->json($tours);
    }

    public function addGuidingPlace(Request $request)
    {
        $guidingPlace = new places();
        $guidingPlace->place = $request->input('place');
        $guidingPlace->guide_id = $request->input('guide_id');
        $guidingPlace->guide_name = $request->input('guide_name');
        $guidingPlace->save();
        return response()->json($guidingPlace);
    }

    public function retrievePlaces($place)
    {
        $guidingPlaces = places::where('place', '=', $place)->get();
        return response()->json($guidingPlaces);
    }

    public function getGuidingPlaces($id)
    {
        $getGuidingPlaces = places::where('guide_id', '=', $id)->get();
        return response()->json($getGuidingPlaces);
    }

    public function getPackages($id,$place)
    {
        $getPackages = packages::where([
            ['tourist_id', '=', $id],
            ['category','=',$place],
        ])->get();
        return response()->json($getPackages);
    }

    public function pendingTripDelete($id){
        $tripDelete = tours::find($id);
        if($tripDelete)
            $tripDelete->delete();
        else
            return response()->json($tripDelete);
        return response()->json($tripDelete);
    }

    public function getAllTourists()
    {
        $user = User::where('userType', '=', 'tourist')->get();
        return response()->json($user);
    }

    public function getAllPackages(){
        $packages = packages::all();
        return response()->json($packages);
    }

    public function postPackages(Request $request){
        $postPackages = new packages();
        $postPackages->category = $request->input('category');
        $postPackages->guide_id = $request->input('guide_id');
        $postPackages->tourist_id = $request->input('tourist_id');
        $postPackages->date = $request->input('date');
        $postPackages->days = $request->input('days');
        $postPackages->province = $request->input('province');
        $postPackages->price = $request->input('price');
        $postPackages->status = $request->input('status');
        $postPackages->save();
        return response()->json($postPackages);
    }

    public function getPackagesForGuide($id){
        $packages = packages::where('guide_id', '=', $id)->get();
        return response()->json($packages);
    }

    public function PaymetAuthToken(){
        $client = new \GuzzleHttp\Client();
        $headers = ['Authorization' => 'Basic NE9WeDM4QVI5YkU0RTB2VmQyWk9oNzNENzo4TEtZNFBBWGVzdzRhRkpGeXgzak0yNGFDU3I4YnEwNXk0dVhHWm0wSDduMyA='];
        $url = "https://sandbox.payhere.lk/merchant/v1/oauth/token";
        $body['grant_type'] = "client_credentials";
        $request = $client->post($url,['headers'=>$headers,'body'=>json_encode($body)]);
    }

    public function reportGuide(Request $request){
        $inquiries = new inquiries();
        $inquiries->guide_id = $request->input('guide_id');
        $inquiries->tourist_id = $request->input('tourist_id');
        $inquiries->reason = $request->input('reason');
        $inquiries->description = $request->input('description');
        $inquiries->save();
        return response()->json($inquiries);
    }

    public function getInquiries(Request $request){
        $inq = inquiries::get();
        return response()->json($inq);
    }

    public function getPendingPackages(){
        $packages = packages::where('status', '=', "pending")->get();
        return response()->json($packages);
    }

    public function assignPackages(Request $request, $id)
    {
        $update = packages::find($id);
        $update->guide_id = $request->input('guide_id',false);
        $update->status = $request->input('status',false);
        $update->save();
        return response()->json($update);
    }

    public function removeuser($id){
        $userDelete = User::find($id);
        if($userDelete)
            $userDelete->delete();
        else
            return response()->json($userDelete);
        return response()->json($userDelete);
    }

    public function getalltripPrices(){
        $allTrips = packages::all();
        return response()->json($allTrips);
    }

    public function retrieveTrips($id){
        $trips = tours::where('tourist_id', '=', $id)->get();
        return response()->json($trips);
    }

    public function removeInquiry($id){
        $inqDelete = inquiries::find($id);
        if($inqDelete)
            $inqDelete->delete();
        else
            return response()->json($inqDelete);
        return response()->json($inqDelete);
    }

    public function searchTouristById($id){
        $tourist = User::find($id);
        return response()->json($tourist);

    }


    public function requestForPackage(Request $request,$id){
        $getPackages = packages::find($id);
        $getPackages->tourist_id = $request->input('tourist_id');
        $getPackages->save();
        return response()->json($getPackages);
    }

    public function getNewGuides(){
        $getGuides = User::where([
            ['userType', '=', 'guide'],
            ['status','=','pending'],
        ])->get();

        return response()->json($getGuides);
    }

    public function guideStatusUpdate(Request $request,$id){
        $updateGuide = User::find($id);
        $updateGuide->status = $request->input('status');
        $updateGuide->save();
        return response()->json($updateGuide);
    }
}
