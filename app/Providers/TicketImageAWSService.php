<?php

namespace app\services;

use Illuminate\Filesystem\Filesystem;
use \Exception;

class TicketImageAWSService
{
    /**
     * User Instance Holder.
     *
     * @var user model
     */
    protected $user;

    /**
     * Ticket Instance Holder
     * @var ticket model
     */
    protected $ticket;

    protected $input;

    /**
     * AWS Custom laravel wrapper
     * @var AwsS3Laravel instance holder
     */
    protected $awsS3Laravel;

    protected $s3;

    protected $filesystem;

    /**
     * construct the models.
     */
    public function __construct()
    {
        $this->input = \App::make('request');
        $this->user = \App::make('User');
        $this->ticket = \App::make('Ticket');
        $this->awsS3Laravel = \App::make('AwsS3Laravel');
        $this->filesystem = new Filesystem();
        $this->S3Services = \App::make('s3service');
        $this->s3 = \App::make('S3TransactionRepository');

    }

    /**
     * Upload image to AWS using ticket id by LTL
     * @return bool
     */

    public function saveTicketImageOrFolder($userRole, $user, $formData)
    {
        // uploads to public/uploads/ in the local server
        $assetPath = 'uploads/images';
        $uploadPath = public_path($assetPath);
        $image = $formData['image'];
        foreach ($image as $file) {
            $file->move($uploadPath, $file->getClientOriginalName());
            // Upload directroy to S3 cloud and get their url(s)
            $this->awsS3Laravel->saveImageInAws($uploadPath. '/' . $file->getClientOriginalName(), $formData['folderPath']. '/' . $file->getClientOriginalName());
        }
        $this->filesystem->cleanDirectory($assetPath);
    }

    public function getTicketImageForRole()
    {
        if (!$this->input->has('ticket_id')) {
            throw new Exception('Ticket id is required');
        }
        $ticketId = $this->input->get('ticket_id');
        $ticket = $this->ticket->find($ticketId);
        if (empty($ticket)) {
            throw new Exception("Ticket not found.");
        }
        $folderName = $ticket->s3_folder;

        return $this->awsS3Laravel->getImageInAWS($folderName);
    }

/**
 * [validateS3Input Validate user input and user role in dashboard]
 * @param  [int] $active [description]
 * @return [array]       [description]
 */
    public function validateS3Input($active)
    {
        if (!$this->input->has('ticket_id')) {
            throw new Exception('Ticket id is required');
        }
        $image = $this->input->file('image');

        $this->checkTicketInDashBoard($active);
        if (is_null($image[0])) {
            throw new Exception("No image selected");
        }

        $uploadSize = \Config::get('aws.fileSize');
        $extension = $data['extension'] = $image[0]->getClientOriginalExtension();

        // Validate size of zip file bieng uploaded
        $imageFileSize = 0;
        foreach ($image as $key => $imageFile) {
            $imageFileSize = $imageFileSize + $imageFile->getSize();
        }
        if ($imageFileSize > $uploadSize) {
            $uploadSize = ($uploadSize/1024)/1024;
            throw new Exception('Total file(s) size should not exceed ' . $uploadSize . 'MB');
        }

        $ticketId = $this->input->get('ticket_id');
        $ticket = $this->ticket->find($ticketId);
        if (empty($ticket)) {
            throw new Exception("Ticket not found.");
        }

        return ['status' => true];
    }

/**
 * [deleteObjectInAwsS3 Delete the object present in the S3 bucket]
 * @param  [array] $deletingObjKey [objectKeys]
 */
    public function deleteObjectInAwsS3($deletingObjKey)
    {
        $user = $this->user->currentUser();
        $userRole = $this->user->findUserRoleById($user->id)->rolename;
        foreach ($deletingObjKey['objKey'] as $deletionKeyValue) {
            // Exploding the key into array
            $folderStructure = explode("/", $deletionKeyValue);
            $folderStructureLength = count($folderStructure);
            // Default folder of Ticket created
            $defaultFolders = array_merge(\Config::get('aws.rootfolder'), \Config::get('aws.Raw_Images'));
            // Validated default folder cannot be removed
            if (in_array($folderStructure[$folderStructureLength-1], $defaultFolders)) {
                throw new Exception("Default Folders cannot be deleted");
            }

            $fileInfo['ticket_id'] = $deletingObjKey['ticket_id'];
            $fileInfo['file_name'] = $folderStructure[$folderStructureLength-1];
            $fileInfo['file_type'] = 'Directroy';
            // Check whether it is  a file or a variable
            if (!$this->filesystem->isDirectory($folderStructure[$folderStructureLength-1])) {
                $fileInfo['file_type'] = $this->filesystem->extension($folderStructure[$folderStructureLength-1]);
            }

            $fileInfo['file_s3_path'] = $deletionKeyValue;
            $fileInfo['user_id'] = $user->id;
            $fileInfo['group'] = $userRole;
            $fileInfo['s3_operation'] = \Config::get('aws.deleteFiles');

            $this->s3->s3DbTrannsaction($fileInfo);
            $this->awsS3Laravel->deleteImageInAWS($deletionKeyValue);
        }
    }

    /**
     * Download Objects From AWS Using Object Key
     * @param  [String] $[downloadS3objKey] [contains the AWS s3 object key]
     * @return none
     */
    public function downloadObjectsFromAWS($downloadS3objKey)
    {
        $this->awsS3Laravel->downloadImageFromAWS($downloadS3objKey);
    }

    public function checkTicketInDashBoard($active)
    {
        // Check ticket is present or not
        if (empty($this->ticket->find(\Input::get('ticket_id')))) {
            throw new Exception("Ticket not found");
        }
        // Ticket is present in thee respective dashboard
        if ($active == 0) {
            throw new Exception("Ticket is not present in dashboard");
        }
    }

    /**
     * [Check User-Role Permission with AWS S3 Directory]
     * @param  [string] $userRole [description]
     * @param  [array]  $acl      [description]
     * @return [true]   if validation passed
     */
    public function userS3AccessControl($userRole, $acl)
    {
        // GET INPUT DATA
        $inputS3Data = $this->input->all();
        // IF ACCESSING ROOT DIRECTORY ALLOW
        if (!isset($inputS3Data['folderPath']) && !isset($inputS3Data['objKey'])) {
            return true;
        }
        // IF OPENING A DIRECTORY
        if (isset($inputS3Data['folderPath'])) {
            $folderKey = (array) $inputS3Data['folderPath'];
            $inputS3Data['objKey'] = $folderKey;
        }
        // FOR DELETING OR UPLOADING
        $S3key = $inputS3Data['objKey'];
        foreach ($S3key as $key => $value) {
             // IF FILE GET PARENT DIR NAME
            $dirName = explode('/', $value);
            if (strpos(basename($value), '.')) {
                // GET DIRECTORY NAME
                $parentDirKey = count($dirName) - 2;
                $dirName = $dirName[$parentDirKey];
            } else {
                // ELSE GET DIRECTORY NAME
                $dirName = basename($value);
            }
                // GET PERMISSION ID
                $authId = \Config::get('s3auth.' . $userRole .'.'. $dirName);
                // ALLOW ACCESS FOR UPLOADED FOLDERS(UNZIPPED FILES)
                $uploadedZipDir = !in_array($dirName, \Config::get('s3auth.staticFolders'));
            if ($uploadedZipDir) {
                return true;
            }
                // VALIDATE REQUESTED PERMISSION WITH PRESET PERMISSION
            if (!in_array($authId, $acl)) {
                throw new Exception("Permission Denied for this Operation!");
            }
        }
    }
}
