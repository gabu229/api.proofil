<?php

namespace App\Controllers;

use App\Models\UserAuthModel;
use App\Models\UserInfoModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class UserRegistration extends ResourceController
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
            'email' => 'required|valid_email|is_unique[users_auth.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        // Validate
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        // Generate a unique user_id
        $user_id = $this->generateUniqueUserId();

        // Save authentication information
        $authData = [
            'email' => $this->request->getVar('email'),
            'user_id' => $user_id,
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT)
        ];

        $authModel = new UserAuthModel();
        $storeAuth = $authModel->save($authData);

        if (!$storeAuth) {
            return $this->fail($this->validator->getErrors());
        }

        // Save user information
        $infoData = [
            'user_id' => $user_id,
            'username' => $user_id
        ];

        $userInfoModel = new UserInfoModel();
        $storeUserInfo = $userInfoModel->save($infoData);

        if (!$storeUserInfo) {
            $authModel->delete($authModel->insertID());
            return $this->fail('Failed to store user information.');
        }

        return $this->respondCreated('User registered successfully.');
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


    // Generate a unique user_id in the specified format
    private function generateUniqueUserId()
    {
        $user_id = 'USR' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);

        $authModel = new UserAuthModel();

        // Check if the generated user_id already exists, generate a new one if needed
        while ($authModel->where('user_id', $user_id)->first() !== null) {
            $user_id = 'USR' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
        }

        return $user_id;
    }
}
