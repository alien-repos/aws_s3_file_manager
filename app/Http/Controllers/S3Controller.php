<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\S3Repository;
use App\Http\Repositories\ValidationRepository;

class S3Controller extends Controller
{
    /**
     * User Instance Holder
     * @var user
     */
    protected $user;

    /**
     * Ticket Image Service Instance Holder
     * @var ticketImageServiceAWS
     */
    protected $ticketImageAWS;

    /**
     * S3 Service Instance Holder
     * @var s3Service
     */
    protected $s3Service;

    protected $validationRepo;

    /**
     * Default constructor for DE
     */
    public function __construct()
    {
        // Parent::__construct();
        // S3 Repository
        $this->s3Repo         = new S3Repository;
        $this->validationRepo = new ValidationRepository;
        $this->request = app('Illuminate\Http\Request');

        // $this->request        = new Request;
    }

    public function upload(Request $request)
    {
        // Validation
        
        // result
        return $this->s3Repo->uploadFilesToS3($request);
    }

    public function download(Request $request)
    {
        // Validation
        
        // result
        return $this->request->get('urlArray');
    }

    public function downloadStream($encodedUrl)
    {
        // Validation
        
        // result
        return $this->s3Repo->downloadFilesInS3($encodedUrl);
    }

    public function delete(Request $request)
    {
        // Validation
        
        // result
        return $this->s3Repo->deleteFilesInS3($request);
    }

    public function genThumbs(Request $request)
    {
        // Validation
        $path = $this->request->input('path');
        return $this->s3Repo->thumbNailView($path);
    }
    
    public function genLists(Request $request)
    {
        // Validation
        $path = $this->request->input('path');
        return $this->s3Repo->listView($path);
    }

    public function updateConfig($config)
    {
        // Validation
        if ($config === 'updateBucket') {
            $this->s3Repo->updateConfiguration('bucket', $this->request->input('bucketname'));
        }
    }
}
