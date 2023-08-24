<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Task;
use Validator;
use Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Task::orderBy('created_at', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'due_date' => 'required | d-F-y',
            'status' => 'required | in:pending,in_progress,completed'
        ]);
        
        if ($validator->fails()) {
            $responseArr['message'] = $validator->errors();;
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }else{
            try {
                $task = new Task;
                $task->title = $request->input('title');
                $task->description = $request->input('description');
                $task->due_date = $request->input('due_date');
                $task->status = $request->input('status');
                $task->user_id = Auth::user()->id;

                $task->save();
                return response()->json(['task' => $task], 200);

            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'due_date' => 'd-F-y',
            'status' => 'in:pending,in_progress,completed',
            'id' => 'required|exists:tasks,id',
        ]);

        if ($validator->fails()) {
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }else{
            try {
                $user_id = Auth::user()->id;
                $task = Task::where('id',$id)->where('user_id',$user_id)->first();
                if($task)
                {
                    $task->title = $request->input('title');
                    $task->description = $request->input('description');
                    $task->due_date = $request->input('due_date');
                    $task->status = $request->input('status');
                    $task->user_id = Auth::user()->id;
    
                    $task->save();
                    return response()->json(['task' => $task], 200);    
                }else{
                    $responseArr['message'] = "Unauthorized action.";
                    return response()->json($responseArr, Response::HTTP_BAD_REQUEST);                    
                }

            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_id = Auth::user()->id;        
        $task = Task::where('id',$id)->where('user_id',$user_id)->first();
        if($task)
        {
            try {
                if($task->delete())
                return response()->json(['message' =>"Delete successful"], 200);  
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }            
        }else{
            $responseArr['message'] = "Unauthorized action.";
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);                    
        }        

    }
}
