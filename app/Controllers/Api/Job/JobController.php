<?php

namespace App\Controllers\Api\Job;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\JobsModel;

class JobController extends ResourceController
{
    // POST
    public function addJob()
    {

        // define rules
        $rules = [
            'job' => 'required|is_unique[jobs.job]'
        ];

        //use guard clauses to validate and insert data
        if (!$this->validate($rules)) {

            return $this->respondCreated([
                'status' => false,
                'message' => $this->validator->getErrors(),
                'data' => []

            ]);
        }

        // Job Model
        $jobModel = new JobsModel();

        if (!$jobModel->insert([
            'job' => strtoupper($this->request->getVar('job'))
        ])) {

            return $this->respondCreated([
                'status' => false,
                'message' => 'Failed to insert job',
                'data' => []

            ]);
        }


        // return successfully data if all rigth
        return $this->respondCreated([
            'status' => true,
            'message' => 'Job insert successfully',
            'data' => [
                'job' => strtoupper($this->request->getVar('job'))
            ]
        ]);
    }


    // GET
    public function listJobs()
    {
        $jobModel = new JobsModel();
        // list all data
        $data = $jobModel->findAll();

        if (empty($data)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "There's not available jobs",
                'data' => []
            ]);
        }

        return $this->respondCreated([
            'status' => true,
            'message' => "Jobs found",
            'data' => $data
        ]);
    }

    // PUT
    public function updateJob($id_job)
    {
        $jobModel = new JobsModel();
        // get job by id_dep
        $dep = $jobModel->find($id_job);

        // error response if job not found
        if (empty($dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Job not found",
                'data' => []
            ]);
        }

        // params
        $dep_name = strtoupper($this->request->getVar('job'));

        // validate if params exist and if are empty.
        if (isset($dep_name) && !empty($dep_name)) {
            // replace data
            $dep['job'] = $dep_name;
        }

        // update job
        if (!$jobModel->update($id_job, $dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => 'Failed to update job',
                'data' => []
            ]);
        }

        return $this->respondCreated([
            'status' => true,
            'message' => 'Job updated successfully',
            'data' => $jobModel->find($id_job)
        ]);
    }

    public function deleteJob($id_job)
    {
        $jobModel = new JobsModel();
        // get job by id_dep
        $dep = $jobModel->find($id_job);

        // error response if job not found
        if (empty($dep)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Job not found",
                'data' => []
            ]);
        }

        // delete job
        if (!$jobModel->delete($id_job)) {
            return $this->respondCreated([
                'status' => false,
                'message' => "Failed to delete job",
                'data' => []
            ]);
        }

        return $this->respondCreated([
            'status' => true,
            'message' => 'Job deleted successfully',
            'data' => []
        ]);
    }
}
