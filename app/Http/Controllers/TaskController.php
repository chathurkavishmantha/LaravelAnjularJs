<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Environment\Console;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response() ->json([
            'error' => false,
            'tasks' => Task::all(),
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        // return $request->all();
        return Task::create([
            'name' => $request['name'],
            'description' => $request['description'],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
    //    $id = '5';
    // dd($id);
        $tasks = Task::find($id);
 
        if(is_null($tasks)){
            return response()->json([
                'error' => true,
                'message'  => "Record with id # $id not found",
            ], 404);
        }
 
        // return view('welcome', compact('item'));

        // return view('welcome')->with('item', $item);

        return response()->json(['tasks' => $tasks]);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        $validation = Validator::make($request->all(),[ 
            'name' => 'required',
            'description' => 'required',
        ]);
 
        if($validation->fails()){
            return response()->json([
                'error' => true,
                'messages'  => $validation->errors(),
            ], 200);
        }
        else
        {
            $tasks = Task::find($id);
            $tasks->name = $request->input('name');
            $tasks->description = $request->input('description');
            $tasks->save();
     
            return response()->json([
                'error' => false,
                'tasks'  => $tasks,
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        // DB::delete('delete from tasks where id = ?',[$id]);
    }
}
