<?php

namespace App\Controllers\Api\Department;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\DepartmentModel;
use PhpParser\Node\Expr\FuncCall;

class DepartmentController extends ResourceController
{

    // POST
    public function addDepartment()
    {

        // define rules
        $rules = [
            'department' => 'required|is_unique[departments.department]'
        ];

        //use guard clauses to validate and insert data
        if (!$this->validate($rules)) {

            return $this->respondCreated([
                'status' => false,
                'message' => $this->validator->getErrors(),
                'data' => []

            ]);
        }

        // Department Model
        $departmentModel = new DepartmentModel();

        if (!$departmentModel->insert([
            'department' => strtoupper($this->request->getVar('department'))
        ])) {

            return $this->respondCreated([
                'status' => false,
                'message' => 'Failed to insert department',
                'data' => []

            ]);
        }


        // return successfully data if all rigth
        return $this->respondCreated([
            'status' => true,
            'message' => 'Department insert successfully',
            'data' => [
                'department' => strtoupper($this->request->getVar('department'))
            ]
        ]);
    }


    // GET
    public function listDepartments()
    {
        $departmentModel = new DepartmentModel();
        // list all data
        $data = $departmentModel->findAll();

        if (empty($data)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "There's not available departments",
                'data' => []
            ]);
        }
        
        return $this->respondCreated([
            'status' => true,
            'message' => "Departments found",
            'data' => $data
        ]);
    }

    // PUT
    public function updateDepartment($id_dep)
    {
        $departmentModel = new DepartmentModel();
        // get department by id_dep
        $dep = $departmentModel->find($id_dep);    

        // error response if department not found
        if (empty($dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Department not found",
                'data' => []
            ]);
        }

        // params
        $dep_name = strtoupper($this->request->getVar('department'));

        // validate if params exist and if are empty.
        if (isset($dep_name) && !empty($dep_name)) {
            // replace data
            $dep['department'] = $dep_name;           
        }

        // update department
        if (!$departmentModel->update($id_dep,$dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => 'Failed to update department',
                'data' => []
            ]);
        }
        
        return $this->respondCreated([
            'status' => true,
            'message' => 'Department updated successfully',
            'data' => $departmentModel->find($id_dep)
        ]);


    }

    public function deleteDepartment($id_dep){
        $departmentModel = new DepartmentModel();
        // get department by id_dep
        $dep = $departmentModel->find($id_dep);    

        // error response if department not found
        if (empty($dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Department not found",
                'data' => []
            ]);
        }

        if (!$departmentModel->delete($id_dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Failed to delete department",
                'data' => []
            ]);
        }

        return $this->respondCreated([
            'status' => true,
            'message' => 'Department deleted successfully',
            'data' => []
        ]);
    }
}
