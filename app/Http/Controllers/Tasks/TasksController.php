<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Validator;
use App\User;
use App\Task;
use JWTAuth;
use Auth;
use Tymon\JWTAuthExceptions\JWTException;
use DB;
use Illuminate\Support\Collection as Collection;

class TasksController extends ApiController
{

    public function __construct(){
      $this->middleware('jwt.auth');
   }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = DB::table('users')
                        ->join('tasks', 'users.id', '=', 'tasks.user_id')
                        ->select('tasks.id', 'name', 'title', 'description', 'tasks.created_at')
                        ->orderBy('tasks.id', 'desc')
                        ->get();

        return $this->showAll(Collection::make($tasks));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user_id = Auth::user()->id;

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'status' => 'required'
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['user_id'] = $user_id;
        $task = Task::create($data);


        $task = Task::create($data);

        return $this->showOne($task, "Tarea $task->title creada exitosamente!", 201);

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task){
      return $this->showOne($task); 
    }

    public function detail(Task $task){
      return $this->showOne($task); 
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        
     if($request->has('title')){
        $task->title = $request->title;
      }

      if($request->has('description')){
        $task->description = $request->description;
      }

      if($request->has('status')){
        $task->status = $request->status;
      }

      //el metodo isDirty valida si algunos e los valores originales ah cambiado su valor
       if(!$task->isDirty()){
         return $this->errorResponse('Se debe de especificar un valor diferente para actualizar', 422);
       }

       $task->save();

       return $this->showOne($task, "Tarea $task->title actualizada exitosamente!", 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
