<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\User;
use App\Task;

class TaskTest extends TestCase
{

    /**
     *  Failed authentication .
     *  @method GET
     *  
     *  Note: Must have Header Accept: application/json 
     * @return status code 400
     */

     public function testTask__FailedAuthentication() {
        $token = "No bearer"; 
    
        $this->json('GET','/api/tasks',[],['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(401)
            ->assertJson([
                    "message"=> "Unauthenticated."
            ]);        
     }

    /**
     * Retrieve a list of tasks owned by the authenticated user.
     *  @method GET
     *  @param
     *  status [pending, in_progress, completed]
     *  per_page default 10
     *  sort_by [title, description, due_date, status]
     *  sort - [asc, desc]
     * 
     * @return status code 200
     */

     public function testTask__ListbyOwner() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        $this->json('GET','/api/tasks?per_page=1',[],['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonStructure(["success",
                "data"=>[
                    "current_page",
                    "data",
                    "first_page_url",
                    "from",
                    "last_page",
                    "last_page_url",
                    "next_page_url",
                    "path",
                    "per_page",
                    "prev_page_url",
                    "to",
                    "total"
                ]
            ]);        
     }


    /**
     *  Task created successfully by the authencated user.
     *  @method POST
     *  @param
     *  title
     *  description
     *  status
     *  due_date
     *  user_id
     * 
     * @return status code 200
     */
     public function testTask__CreateSuccessful() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        //POST BODY
        $body = [
            "title"=>"test generate",
            "description"=>"test description",
            "due_date"=>"10-10-2023",
            "status"=>"pending"
        ];

        $this->json('POST','/api/tasks',$body,['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data"=>[
                    "id",
                    "title",
                    "description",
                    "due_date",
                    "status"
                    ]
            ]);        
     }    
     

    /**
     *  Display the validations upon failed insert attempt.
     *  @method POST
     * 
     * @return status code 400
     */     
     public function testTask__CreateValidations() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        //Empty POST body
        $body = [
        ];

        $this->json('POST','/api/tasks',$body,['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(400)
            ->assertJson([
                    "success"=>false,
                    "data"=>[
                        "title"=>[
                            "The title field is required."
                        ],
                        "description"=>[
                            "The description field is required."
                        ],
                        "due_date"=>[
                            "The due date field is required."
                        ],
                        "status"=>[
                            "The status field is required."
                        ]
                    ]
            ]);        
     }     

    /**
     *  Task updated successfully by the authencated user.
     *  @method POST
     * 
     *  @param
     *  title
     *  description
     *  status
     *  due_date
     *  user_id
     * 
     * @return status code 200
     */         
     public function testTask__UpdateSuccessful() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;

        //create task
        $Task = Task::create([
            'user_id'=>$user->id,
            "title"=>"original",
            "description"=>"description",
            "due_date"=> Carbon::parse("10-10-2025"),
            "status"=>"pending"            
        ]);

        //POST BODY -  Updated information
        $new_data = [
            "title"=>"Updated title",
            "description"=>"Updated description",
            "due_date"=>"10-10-2023",
            "status"=>"completed"
        ];

        $this->json('POST','/api/tasks/'.$Task->id,$new_data,['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson([
                    "success"=>true,
                    "data"=>[
                        "id"=>$Task->id,
                        "title"=>"Updated title",
                        "description"=>"Updated description",
                        "due_date"=>"2023-10-10T00:00:00.000000Z",
                        "status"=>"completed"
                    ]
            ]);        
     } 
     

    /**
     *  Display the validation for status and due.
     * 
     * 
     * @return status code 400
     */           
     public function testTask__UpdateFailed() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
        
        //Create new task
        $Task = Task::create([
            'user_id'=>$user->id,
            "title"=>"original",
            "description"=>"description",
            "due_date"=> Carbon::parse("10-10-2025"),
            "status"=>"pending"            
        ]);

        //POST BODY
        $new_data = [
            "title"=>"Updated title",
            "description"=>"Updated description",
            "due_date"=>"650-10-2023", // Invalid date format 
            "status"=>"Not completed"  // Not included in the enum
        ];

        $this->json('POST','/api/tasks/'.$Task->id,$new_data,['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(400)
            ->assertJson([
                    "success"=>false,
                    "data"=>[
                        "due_date"=>[
                            "The due date is not a valid date."
                        ],
                        "status"=>[
                            "The selected status is invalid."
                        ]
                    ]
            ]);        
     } 


    /**
     *  Successful deletion of existing task owned by the user.
     *  @method DELETE
     * 
     *  @param
     *  <Task ID>
     * 
     * @return status code 200
     */                
     public function testTask__DeleteSuccessful() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
        
        //Create new task
        $Task = Task::create([
            'user_id'=>$user->id,
            "title"=>"original",
            "description"=>"description",
            "due_date"=> Carbon::parse("10-10-2025"),
            "status"=>"pending"            
        ]);

        $this->json('DELETE','/api/tasks/'.$Task->id,[],['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson([
                "success"=>true,
                "data"=>[
                    "message"=>"Delete successful"
                    ]
            ]);        
     } 

    /**
     *  Failed attempt of deleting the task.
     *  @method DELETE
     * 
     *  @param
     *  <Task ID>
     * 
     * @return status code 400
     */                
    public function testTask__DeleteFailed() {
        //Generate user
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
        
        //Not owned task
        $Task_id = 1; 

        $this->json('DELETE','/api/tasks/'.$Task_id,[],['Accept'=>'application/json','Authorization' => 'Bearer ' . $token])
            ->assertStatus(400)
            ->assertJson([
                "success"=>false,
                "data"=>[
                    "message"=>"Unauthorized action."
                    ]                     
            ]);        
     } 

}     