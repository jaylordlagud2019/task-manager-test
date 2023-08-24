# Task Management API

## Description:
You are tasked with building a RESTful API for a task management application using the Laravel framework. The application will be used to manage tasks for a user. Users should be able to create, retrieve, update, and delete tasks through the API.

## Pre-Development
Before you begin developing, create a branch from master and name it `firstName_lastNameInitial`, e.g. `git checkout -b james_g` and publish the branch to the remote repo.  
There are also some things to configure before developing:
- Run `composer install`
- Configure your db credentials inside the .env file. Note: `mysql` is our preferred db, if you do not have mysql, sqlite will suffice. Just make sure to update the `.env.example` so we know which database type to use.
- Run `php artisan init`

> Note: A default user will be initialised for you to be able to login and retrieve a JWT token. The credentials can be found at `config/default-user.php`. This is mainly for efficiency so you don't have to waste time registering a user yourself (you may wish to do this if you please).

## Requirements:
1. **Authentication and Authorization:**  
    Implement JWT (JSON Web Tokens) based authentication for the API. Users should be able to register, log in, and receive a JWT token upon successful authentication (this is set up for you already). 
    
    Using this token, **only authenticated users** should be able to perform CRUD operations on tasks, and they should only be able to access their own tasks.

2. **Task CRUD Operations:**  
Implement API endpoints for the following CRUD operations on tasks:
    - Create a new task.
    - Retrieve a list of tasks owned by the authenticated user.
    - Retrieve details of a specific task.
    - Update the details of a task.
    - Delete a task.

3. **Task Properties:**  
Properties must be created using Laravel migrations.  
Each task should have the following properties:
    - title (string)
    - description (text)
    - due_date (datetime)
    - status (enum: "pending", "in_progress", "completed")

4. **Validation and Error Handling:**  
Implement validation rules for creating and updating tasks. Return appropriate error responses with meaningful error messages for invalid requests.

5. **Pagination:**  
When retrieving the tasks for a user, the results should be paginated. The API should allow users to specify the page number and the number of tasks per page (a default of 10 tasks per page).

6. **Sorting and Filtering:**  
Implement the ability to sort tasks by title, due date, or status. Also, allow users to filter tasks by status.

### Bonus:
- Implement user roles (e.g., admin, regular user) and limit certain operations to specific roles.
- Implement user-friendly error responses with appropriate HTTP status codes and JSON payloads.
- Write feature tests to ensure the functionality of your API endpoints. Use PHPUnit for writing tests.

## Need Clarification?
If you're having trouble interpreting the requirements please don't hesitate to email me at `james@medmate.com.au`

## Submission:
Once you have finalised the applicaiton, push the finished product to your branch (you may make as many commits as necessary). Create a pull request from your branch to base repository `Medmate-Australia-Pty/task-manager-test` and base branch `master`. Please format the pull request name to be "Submission for <firstName_lastNameInitial>", e.g. "Submission for james_g".