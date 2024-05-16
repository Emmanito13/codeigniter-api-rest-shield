<?php

namespace App\Controllers\Api\Employee;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use App\Models\WorkModel;

class EmployeeController extends ResourceController
{

    // POST
    public function addEmployee()
    {

        $workModel = new WorkModel();
        $rules = [
            'name' => 'required',
            'lastname' => 'required',
            'birthdate' => 'required|valid_date',
            'birthdate' => 'required|valid_date',
            'gender' => 'required|integer',
            'address' => 'required',
            'date_admission' => 'permit_empty|valid_date',
            'salary' => 'required|decimal',
            'department' => 'required|integer',
            'job' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->respondCreated([
                'status' => false,
                'message' => $this->validator->getErrors(),
                'data' => []

            ]);
        }

        $imageFile = $this->request->getFile("profile_image_src");
        $phone = $this->request->getVar('phone');
        $curp = $this->request->getVar('curp');
        $date_admission = $this->request->getVar('date_admission');

        $dataEmployee = array(
            "name" => strtoupper($this->request->getVar('name')),
            "lastname" => strtoupper($this->request->getVar('lastname')),
            "birthdate" => $this->request->getVar('birthdate'),
            "gender" => $this->request->getVar('gender'),
            "address" => strtoupper($this->request->getVar('address')),
            "phone" => (isset($phone) && !empty($phone)) ? $phone : null,
            "curp" => (isset($curp) && !empty($curp)) ? $curp : null
        );

        

        $dataWork = array(            
            "salary" => $this->request->getVar('salary'),
            "department" => $this->request->getVar('department'),
            "job" => $this->request->getVar('job'),
        );

        if ((isset($date_admission) && !empty($date_admission))) {
            $dataWork['date_admission'] = $date_admission;
        }

        if (!isset($imageFile) && empty($imageFile)) {

            $respoInsEmployee = $this->insertEmployee($dataEmployee);

            if (!$respoInsEmployee['status']) {
                return $this->respondCreated(array(
                    "status" => false,
                    "message" => $respoInsEmployee['message'],
                    "data" => []
                ));
            }

            $dataWork['idE'] = $respoInsEmployee['lastId'];

            if (!$workModel->insert($dataWork)) {

                $this->rollbackEmployee($respoInsEmployee['lastId'], $imageFile);

                return $this->respondCreated(array(
                    "status" => false,
                    "message" => "Failed to create employee",
                    "data" => []
                ));
            }

            return $this->respondCreated(array(
                "status" => true,
                "message" => $respoInsEmployee['message'],
                "data" => []
            ));
        }

        $respoUpImage = $this->uploadImage($imageFile);

        if (!$respoUpImage['status']) {
            return $this->respondCreated(array(
                "status" => false,
                "message" => $respoUpImage['message'],
                "data" => []
            ));
        }

        $dataEmployee["profile_image_src"] =  $respoUpImage["img_src"];

        $respoInsEmployee = $this->insertEmployee($dataEmployee);

        if (!$respoUpImage['status']) {
            return $this->respondCreated(array(
                "status" => false,
                "message" => $respoUpImage['message'],
                "data" => []
            ));
        }

        $dataWork['idE'] = $respoInsEmployee['lastId'];

        if (!$workModel->insert($dataWork)) {

            $this->rollbackEmployee($respoInsEmployee['lastId'], $imageFile);

            return $this->respondCreated(array(
                "status" => false,
                "message" => "Failed to create employee",
                "data" => []
            ));
        }

        return $this->respondCreated(array(
            "status" => true,
            "message" => "Employee inserted successfully",
            "data" => []
        ));
    }

    public function insertEmployee($data)
    {
        $employeeModel = new EmployeeModel();

        if (!$employeeModel->insert($data)) {
            return array(
                "status" => false,
                "message" => "Failed to create employee"
            );
        }

        return array(
            "status" => true,
            "message" => "Employee inserted successfully",
            "lastId" => $employeeModel->getInsertID()
        );
    }

    public function uploadImage($imageFile)
    {

        $imageName = $imageFile->getName();

        // abc.png, xyz.jpge

        $tempArray = explode(".", $imageName);
        $newImageName = round(microtime(true)) . "." . end($tempArray);

        if (!$imageFile->move("images/", $newImageName)) {
            return array(
                "status" => false,
                "message" => "Failed to upload image"
            );
        }

        return array(
            "status" => true,
            "message" => "Image uploaded successfully",
            "img_src" => "images/" . $newImageName
        );
    }

    public function rollbackEmployee($idE, $imageFile)
    {
        $employeeModel = new EmployeeModel();
        try {
            $employeeModel->delete($idE);
            if (isset($imageFile) && !empty($imageFile)) {
                $imageFile->delete();
            }
        } catch (\Exception $e) {
            return array(
                "status" => false,
                "message" => $e->getMessage()
            );
        }
    }

    // GET
    public function listEmployee()
    {
    }

    // GET
    public function singleDataEmployee($idE)
    {
    }

    // PUT
    public function updateEmployee($idE)
    {
    }

    // DELETE
    public function deleteEmployee($idE)
    {
    }
}
