<?php

namespace App\Controllers;

use App\Models\UserAuthModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class UserLogin extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    use ResponseTrait;

    public function index()
    {

        helper(['form']);

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        // VALIDATE
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $authModel = new UserAuthModel();

        // Check User Existence
        $userExists = $authModel->where('email', $this->request->getVar('email'))->first();
        if (!$userExists) {
            return $this->fail('No record of user found.');
        }

        // Verify Password
        $validPassword = password_verify($this->request->getVar('password'), $userExists['password']);
        if (!$validPassword) {
            return $this->fail('Incorrect Password');
        }

        $key = getenv('JWT_SECRET_TOKEN');
        $payload = [
            'iat' => time(),
            'nbf' => time() + (21 * 24 * 60 * 60),
            'uid' => $userExists['user_id'],
            'email' => $userExists['email']
        ];

        $token = JWT::encode($payload, $key, 'HS256');
        $response = [    
            'status' => 'success',
            'user' => $token,
            'token' => $token
        ];

        return $this->respond($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        //
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }
}
