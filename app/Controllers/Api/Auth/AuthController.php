<?php

// Controller for managment user and authtentication request
// Use: To create, login, profile, logout users

namespace App\Controllers\Api\Auth;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Models\UserModel; //User Model for shield authentication
use CodeIgniter\Shield\Entities\User; //User Entities for user login

class AuthController extends ResourceController
{
    
    // POST
    public function register(){
        // Use: To create and save users

        // Data: username, email, password
        // Validation
        // Model and Entity
        // Save data to database table

        // rules to each table user cols 
        $rules = [
            'username' => 'required|is_unique[users.username]', //required and unique data
            'email' => 'required|valid_email|is_unique[auth_identities.secret]', //required, email validate and unique data 
            'password' => 'required' //required
        ];

        if (!$this->validate($rules)) {

            $response = [
                "status" => false,
                "message" => $this->validator->getErrors(),
                "data" => []
            ];

        } else {

            // User Model
            $userObject = new UserModel();

            // User Entity
            $userEntityObject = new User([
                "username" => $this->request->getVar("username"),
                "email" => $this->request->getVar("email"),
                "password" => $this->request->getVar("password")
            ]);

            $userObject->save($userEntityObject);

            $response = [
                "status" => true,
                "message" => "User saved succesfully",
                "data" => []
            ];
        }
        
        return $this->respondCreated($response);

    }


    // POST
    public function login()
    {
        // Use: Logic specific user to aplication
        // It generates a token value

        // fisrt validate if user is logged
        // If is true, logout session
        if (auth()->loggedIn()) {
            auth()->logout();
        }

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            $response = [
                "status" => false,
                "message" => $this->validator->getErrors(),
                "data" => []
            ];
        } else {
            $credentials = [
                "email" => $this->request->getVar("email"),
                "password" => $this->request->getVar("password")
            ];

            $loginAttemp = auth()->attempt($credentials);

            if (!$loginAttemp->isOK()) {
                $response = [
                    "status" => false,
                    "message" => 'Invalid login details',
                    "data" => []
                ];
            } else {
                
                $userObject = new UserModel();

                $userData = $userObject->findById(auth()->id());

                // $token = $userData->generateAccessToken("TestToken");

                // $auth_token = $token->raw_token;

                $response = [
                    "status" => true,
                    "message" => 'User logged successfully',
                    "data" => [
                        "token" => $userData->generateAccessToken("TestToken")->raw_token
                    ]
                ];
            }
            
        }

        return $this->respondCreated($response);
    }

    // GET
    public function profile()
    {
        $userObject = new UserModel();        
        return $this->respondCreated([
            "status" => true,
            "message" => "Profile api called",
            "data" => [
                'user' => $userObject->findById(auth()->id())
            ],  

        ]);
    }

    // GET
    public function logout()
    {
        auth()->logout();
        auth()->user()->revokeAllAccessTokens();

        return $this->respondCreated([
            "status" => true,
            "message" => "User logout successfully",
            "data" => []        
        ]);
    }

    
    public function accessDenied()
    {
        return $this->respondCreated([
            'status' => false,
            'message' => "Invalid access",
            "data" => []
        ]);
    }
    
}
