<?php

// Namespace
namespace App;

// Include Moddules
use Illuminate\Database\Eloquent\Model;

// Filesystem
protected $filesystem;
// Local upload storage path
protected $storagePath = \Config::get('config_data.upload_destination');
//s3 instance
protected $s3;

// classes
class AwsOperations extends Model
{
	/**
     * construct the models.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->s3 = \App::make('aws')->get('s3');
    }


	public function uploadFilesToS3($input)
    {
        $uploadPath = 
        $image = $formData['image'];
        foreach ($image as $file) {
            $file->move($uploadPath, $file->getClientOriginalName());
            // Upload directroy to S3 cloud and get their url(s)
            Self::saveImageInAws($this->storagePath . '/' . $file->getClientOriginalName(), $formData['folderPath']. '/' . $file->getClientOriginalName());
        }
        $this->filesystem->cleanDirectory($assetPath);
        // push the to S3
    }

    public function downloadFilesFromS3($input)
    {
        // get the uploaded files to temp dir
        
        // push the to S3
    }

    public function deleteFilesInS3($input)
    {
        // get the uploaded files to temp dir
        
        // push the to S3
    }
        /**
     * Bucket name
     */
    protected $bucket;
    protected $s3;

    /**
     * construct the models.
     */
    public function __construct()
    {
        $this->bucket = \Config::get('amazon.bucketname');
        $this->s3 = \App::make('aws')->get('s3');
    }

    /**
     * Generic function to Upload image to AWS
     *
     * @param $sourcePath full path of file to be uploaded
     * @param $folderName upload file path
     * @return bool
     */
    public function saveImageInAws($sourcePath, $folderName)
    {
        // ADD VERSIONING TO UPLOADING OBJECTS
        // IF OBJECT EXISTS RENAME IT
        $folderName = $this->checkObjectInAWS($folderName);
        $result = $this->s3->putObject(array(
            'Bucket' => $this->bucket,
            'Key'    => $folderName,
            'SourceFile' => $sourcePath
        ));
        return $result->toArray();
    }

    public function uploadDirectoryInAws($sourcePath, $folderName)
    {
        $result = $this->s3->uploadDirectory($sourcePath, $this->bucket, $folderName);
        return $result;
    }

    /**
     * Generic function to get list of objects
     *
     * @param $folderName upload file path
     * @return array
     */
    public function getImageInAWS($folderName)
    {
        $result = $this->s3->listObjects(array(
            'Bucket' => $this->bucket,
            'Prefix' => $folderName
        ));
        $urlList = $this->getFullImageUrl((array)$result['Contents']);
        return $urlList;
    }

    /**
     * Generic function to get list of object url 
     * based on key and pass them to browser as binary data for download
     * @param [String] $[downloadObjKey] [contains the s3 object key]
     * @return none
     */
    public function downloadImageFromAWS($downloadKey)
    {
        $fileUrl = $this->s3->getObjectUrl($this->bucket, $downloadKey, \Config::get('aws.singleFileTimeout'));
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Disposition: attachment; filename="'.basename($downloadKey).'"');
        readfile($fileUrl);
    }

    /**
     * Generic function to generate signed url for images
     *
     * @param $imageList array of relative image url
     * @return array
     */
    public function getFullImageUrl($imageList)
    {
        $urlList = array();
        foreach ($imageList as $image) {
            array_push($urlList, $this->s3->getObjectUrl($this->bucket, $image['Key'], \Config::get('aws.zipFileTimeout')));
        }
        return $urlList;
    }

    /**
     * Generic function to delete image from AWS
     *
     * @param $key relative url of the image to beleted
     * @return array
     */
    public function deleteImageInAWS($deletionKey)
    {
        $this->s3->deleteMatchingObjects($this->bucket, $deletionKey);
    }
 
    /**
     * [checkObjectInAWS ADDS VERSIONING TO UPLOADED S3 DATA]
     * @param  [type] $ObjectName  [description]
     * @param  [type] $pathToCheck [description]
     * @return [type]              [description]
     */
    public function checkObjectInAWS($objectPath, $version = 0)
    {
        $version = $version + 1;
        // $checkKey = $pathToCheck . $ObjectName;
        $objectExists = $this->s3->doesObjectExist($this->bucket, $objectPath);
        // IF OBJECT EXISTS RENAME OBJECT
        if($objectExists) {
            // GET BASENAME
            $baseName = basename($objectPath);
            // CHECK FILE OR DIRECTORY
            if (strpos($baseName, ".") != false) {
                // IF EXTENSION DETACH AND HOLD IT
                $explodedName = explode(".", $baseName);
                $name = $explodedName[0];
                $extension = $explodedName[1];
                // PREFIX VERSION NUMBER
                $name = $name . "(" . $version . ")";
                // RENAME OBJECT (ATTACH EXTENSION IF EXISTS)
                $name = $name . "." . $extension;
            } else {
                $name = $baseName . "(" . $version . ")";
            }
            // RETURN THE PATH NEW NAME
            $objectPath = str_replace($baseName, $name, $objectPath);
            // CHECK IF THE RENAMED OBJECT EXISTS
           return $this->checkObjectInAWS($objectPath, $version);
        } else {
            return $objectPath;
        }
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
