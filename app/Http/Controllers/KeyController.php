<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Repositories\S3Repository;
use App\Http\Repositories\ValidationRepository;

class KeyController extends Controller
{
    protected $request;
    
    public function __construct(Request $request)
    {
        // Parent::__construct();
        // S3 Repository
        $this->s3Repo         = new S3Repository;
        $this->validationRepo = new ValidationRepository;
        $this->request = $request;

        // $this->request        = new Request;
    }
    
    public function getAllKeysInBucket($bucketName, $key = 'dev_box/local/2017/')
    {
        return $this->s3Repo->listView($bucketName, $key);
    }

    public function getAllKeysInPath()
    {
        $bucketName = $this->request->input('bucket');
        $key = $this->request->input('path');

        return $this->s3Repo->listView($bucketName, $key);
    }
}
