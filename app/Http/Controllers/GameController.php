<?php

namespace App\Http\Controllers;

use App\Models\Game;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
        return Game::all()->count();
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //  
        $request->validate([

            'gamename' => 'required',
            'category'=>'required',
            'user_id' => 'required',
            'start_time'=> 'required'
        ]);
        
        return Game::create($request->all());
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return Game::where('user_id',$id)->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        //find a prodcut by id
        return Game::where('gamename','like','%'.$name.'%')->get();
    }

    public function mostPlayedGame()
    {
       
     $value = DB::table('games')->select(DB::raw('count(*) as total, gamename'))->GROUPBY('gamename')->ORDERBY('total', 'DESC')->LIMIT(1)->get();
   
     return response ($value);
  
    }
 
    public function mostPlayedCategory()
    {
       
    $value = DB::table('games')->select(DB::raw('count(*) as total,category'))->GROUPBY('category')->ORDERBY('total', 'DESC')->LIMIT(1)->get();
   
     return response ($value);
  
    }

    public function countUniqueGames()
    {
       
     $value = DB::table('games')->select(DB::raw('SUM(user_id) as total, gamename'))->GROUPBY('gamename')->get();
   
     return response (count($value));
  
    }
 
    public function countUniqueGamesWithDate( Request $request)
    {
       
        $fields = $request->validate([
            'from'=>'required|date',
            'to'=>'date'
        ]);
       
     $value = DB::table('games')->select(DB::raw('SUM(user_id) as total, gamename'))->whereBetween('created_at', [$fields['from'], $fields['to']])->GROUPBY('gamename')->get();
   
     return response (count($value));
  
    }

    public function countGamesWithDate( Request $request)
    {
       
        $fields = $request->validate([
            'from'=>'required|date',
            'to'=>'date'
        ]);
       
     $value = DB::table('games')->select(DB::raw('gamename'))->whereBetween('created_at', [$fields['from'], $fields['to']])->get();
   
     return response (count($value));
  
    }
    
}
