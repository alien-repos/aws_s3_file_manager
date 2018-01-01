<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Repositories\S3Repository;
use App\Http\Repositories\ValidationRepository;

class BucketController extends Controller
{
    protected $user;
    protected $ticketImageAWS;
    protected $s3Service;
    protected $validationRepo;

    public function __construct()
    {
        // Parent::__construct();
        // S3 Repository
        $this->s3Repo         = new S3Repository;
        $this->validationRepo = new ValidationRepository;
        $this->request = app('Illuminate\Http\Request');

        // $this->request        = new Request;
    }

    public function getAllBuckets()
    {
        return $this->s3Repo->getAllBuckets();
    }

    public function upload(Request $request)
    {
        // Validation
        
        // result
        return $this->s3Repo->uploadFilesToS3($request);
    }
}
