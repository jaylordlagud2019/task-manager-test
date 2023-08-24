<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Api\TaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Task;
use Auth;
use Carbon\Carbon;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $sort_by = $request->input('sort_by');
            $sort_by = $sort_by ? $sort_by : 'title'; 
    
            $sort = $request->input('sort');
            $sort = $sort ? $sort : 'asc'; 

            $user_id = $request->user()->id;
            $task = Task::where('user_id',$user_id)->orderBy($sort_by, $sort);

            if($request->input('status'))
            $task->where('status','=',$request->input('status'));

            $per_page = $request->input('per_page');
            $per_page = $per_page ? $per_page : 10;             
            $result = $task->paginate($per_page);
    
            return response()->json(['success'=> true,'data'=> $result], 200);  
    
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $data = $request->validated();
        try {
            $data['user_id'] = Auth::user()->id;
            $data['due_date'] = Carbon::parse($data['due_date']);
            
            $task = TaskResource::make(Task::create($data));
            return response()->json(['success'=> true,'data'=> $task], 200);  

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        try {
            $user_id = Auth::user()->id;
            $task = Task::where('id',$id)->where('user_id',$user_id)->first();
            if($task)
            {
                return response()->json(['success'=> true,'data'=> TaskResource::make($task)], 200);            
            }else{
                $responseArr['message'] = "Unauthorized action.";
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);                    
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }            
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
    public function update(UpdateTaskRequest $request, $id)
    {
        $data = $request->validated();
        try {
            $user_id = $request->user()->id;
            $task = Task::where('id',$id)->where('user_id',$user_id)->first();
            if($task)
            {   
                if(array_key_exists('due_date',$data))
                $data['due_date'] = Carbon::parse($data['due_date']); 

                $task->fill($data);
                $task->update();
                return response()->json(['success'=> true,'data'=> TaskResource::make($task)], 200);                  
            }else{
                $responseArr['message'] = "Unauthorized action.";
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);                    
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
                return response()->json(['success'=> true, "data"=>['message'=>"Delete successful"]], 200);                  
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }            
        }else{
            $responseArr['message'] = "Unauthorized action.";
            return response()->json([
                'success'   => false,
                'data'      => $responseArr
            ], Response::HTTP_BAD_REQUEST);                    
        }        

    }
}
