<?php

namespace App\Services;

use \Config;
use \App;
use ZipArchive;
use Illuminate\Filesystem\Filesystem;
use Aws\S3\S3Client;

class S3Services
{

    /**
     * [$bucket Store s3 bucket name]
     * @var [Bucket name holder]
     */
    protected $bucket;

    /**
     * [$s3 s3 Instance]
     * @var [S3 Instance ]
     */
    protected $s3;

    /**
     * [$util Util Instance]
     * @var [Util Instance ]
     */
    protected $util;

    /**
     * [$util path]
     * @var [s3RootPath  ]
     */
    protected $s3RootPath;

    /**
     * [__construct]
     */
    public function __construct()
    {
        $this->s3 = App::make('aws')->get('s3');
        $this->util = App::make('util');
        $this->filesystem = new Filesystem();
    }

    /**
     * [createFolderInAWS Creation of s3 folder in amazon for perticular request]
     * @param  [int]    $ticketId
     * @param  [string] $merchant_name
     * @param  [string] $cityName
     * @return [string] [Return s3 folder url]
     */
    public function createFolderInAWS($ticketId, $merchant_name, $cityName, $created_at = null)
    {
        // Bucket Will be the city name
        $this->chooseBucket($cityName);
        // Remove White spaces and replace with underscore _
        $merchant_name = $this->util->modifyMerchantName($merchant_name);
        // Folder Formate:  /<month-year>/<ticketId>_<merchantName>
        $date = date('m-Y');
        if (!is_null($created_at)) {
            $date = $created_at;
        }
        $rootFolderName = $this->s3RootPath.'/' . $date . '/' . $ticketId . '_' . $merchant_name . '/';

        // \Log::info('S3 folder Ticket Root Initiated');
        $this->createFolderInAmazonSSS($rootFolderName);
        // S3 Root Folders
        $folderArray = \Config::get('aws.rootfolder');

        foreach ($folderArray as $key => $MasterFolderName) {
             // Creation of s3folder which does not contain any nested folder
            $ticketMasterFolder = $rootFolderName.$MasterFolderName.'/';
            $this->createFolderInAmazonSSS($ticketMasterFolder);
            // Get the Folder
            $nestedFolder = Config::get('aws.'.$MasterFolderName);
            // Checking if it contain nested folder or not
            if (is_array($nestedFolder)) {
                // Creation of s3 folder for nested folder
                foreach ($nestedFolder as $subkey => $subFolder) {
                    $folder =  $ticketMasterFolder.$subFolder.'/';
                    $this->createFolderInAmazonSSS($folder);
                }
            }
        }
         // \Log::info('S3 folder creation completed');
        $s3URL = $this->s3->getObjectUrl($this->bucket, $rootFolderName);

        return $s3URL;
    }

    /**
     * [createFolderInAmazonS3 ]
     * @param  [string]  $folderPath
     * @return [boolean] true;
     */
    public function createFolderInAmazonSSS($folderPath)
    {
        $result = $this->s3->putObject(array(
                    'Bucket' => $this->bucket,
                    'Key' => $folderPath,
                    'Body' => '',
                ));

        \Log::info('S3 folder '. $folderPath . 'Created');
        $this->createFolderInAmazonSSSAccount2($folderPath);

        return true;
    }

    public function createFolderInAmazonSSSAccount2($folderPath)
    {
        // Creating same folder structure in Account2 account.
        $s3Client = S3Client::factory(array(
            'credentials' => array(
            'key'    => Config::get('aws.s3Account2.key'),
            'secret' => Config::get('aws.s3Account2.secret'),
            )
        ));
        $result = $s3Client->putObject(array(
            'Bucket' => Config::get('aws.s3Account2.bucketname'),
            'Key'    => $folderPath,
            'Body'   => ''
        ));
    }

    public function chooseBucket($cityName)
    {
        // Bucket Will be the city name on Prodeuction else config value
        // if (App::environment('production')) {
        //     $this->bucket = strtolower('fos'.$cityName);
        //     $this->s3RootPath = '';
        // }
        // Local And Other Environments
        if (App::environment('local', 'staging', 'uat', 'integration', 'production')) {
            $this->bucket = \Config::get('aws.bucketname');
            $this->s3RootPath = 'fos'.$cityName.'/';
        }
    }

    /**
     * [exploderFolderStructure Explode folder string into array]
     * @param  [string] $folderString
     * @return [array]
     */
    public function exploderFolderStructure($folderString)
    {
        $rootFolderArray = explode(",", $folderString);

        return $rootFolderArray;
    }

    /**
     * [unzip ]
     * @param  [string]         $fileName
     * @return [boolean/string]
     */
    public function unzip($fileName, $filePath)
    {
        $zip = new ZipArchive;
        $file = $zip->open($fileName);
        if ($file === true) {
            $zip->extractTo($filePath);
            $zip->close();
        }

        return false;
    }

    /**
     * [getFolders ]
     * @param  [string] $ticketId
     * @return [Object] $s3Folders
     */
    public function getFolders($ticketId, $folderPath = null)
    {
        $s3Folders = array();
        //Get Seller Infor by Ticket
        $sellerRequest = \SellerRequest::findRequetDetailByTicketId($ticketId);
        //replace white space
        $merchant_name = $this->util->modifyMerchantName($sellerRequest->merchant_name);
        $city = \City::find($sellerRequest->merchant_city_id);
        // Bucket Will be the city name on Production
        $this->chooseBucket($city->city_name);
        // Ticket Root Path
        $s3TicketRootPath = $this->s3RootPath.date('m-Y', strtotime($sellerRequest->created_at))."/".$ticketId."_".$merchant_name."/";
        // Sub Folders
        if ($folderPath) {
            $s3TicketRootPath = $folderPath.'/';
        }

        // GET LIST OF OBJECTS
        $iterator = $this->s3->getIterator('ListObjects', array(
            'Bucket' => $this->bucket,
            'Prefix' => $s3TicketRootPath
        ));

        // RESOLVE NAME AND PATHS FROM KEYS
        foreach ($iterator as $folderObj) {
            $folderPath = str_replace($s3TicketRootPath, '', $folderObj['Key']);
            $folderName = $this->splitFolders($folderPath);
            if ($folderName && !in_array($folderName, $s3Folders)) {
                $itemPath = $s3TicketRootPath.$folderName;
                $key = $itemPath;
                if (strrpos($folderName, ".") != false) {
                    $itemPath = $this->s3->getObjectUrl($this->bucket, $itemPath, \Config::get('aws.thumbnailsTimeout'));
                    // $itemPath = $this->s3->getObjectUrl($this->bucket, $itemPath);
                }
                $s3Folders[$folderName] = [
                    'foldername'=> $folderName,
                    'folderpath'=> $itemPath,
                    'key' => $key ];
            }
        }

        return $s3Folders;
    }

    /**
     * [splitFolders description]
     * @param  [String] $folderPath [S3 Key]
     * @return [Array   value]             [Folder Name]
     */
    private function splitFolders($folderPath)
    {
        $folders = explode('/', $folderPath);

        return $folders[0];
    }

    /**
     * [toggleS3Permissions description]
     * @param  [type] $permission [description]
     * @return [type] [description]
     */
    public function changeS3Permissions($permission, $s3Key)
    {
        // Directory path that should be made public
        $s3Key = $s3Key. '/' . \Config::get('aws.etl_storage_path');
        $bucketName = \Config::get('aws.bucketname');
        // Change ACL to Public
        if ($permission == 'public') {
            $accessControl = \Config::get('aws.public-read');
        }
        // Change ACL to Private
        if ($permission == 'private') {
            $accessControl = \Config::get('aws.private');
        }

        // GET LIST OF OBJECTS
        $iterator = $this->s3->getIterator('ListObjects', array(
            'Bucket' => \Config::get('aws.bucketname'),
            'Prefix' => $s3Key
        ));

        /*Iterate through the list and change
        permissions individually for each key*/
        foreach ($iterator as $filesKey) {
            $acp = $this->s3->putObjectAcl(array(
                'Bucket' => \Config::get('aws.bucketname'),
                'Key'    => $filesKey['Key'],
                'ACL'    => $accessControl
                ));
        }
    }

    /**
     * [createZipFile - Creates a zip file on the server with s3 data]
     * @return [type] [description]
     */
    public function createZipFile($awsS3Keys)
    {
        // Delete any older files in the directory
        $this->deleteOlderFiles();

        $expiringUrls = [];

        // PASS THEM TO GENERATE EXPIRING URLS
        foreach ($awsS3Keys as $awsS3Key) {
            // GET LIST OF OBJECTS EXPIRING URLS
            $expUrl = $this->s3->getObjectUrl(\Config::get('aws.bucketname'), $awsS3Key, \Config::get('aws.thumbnailsTimeout'));
            // $expUrl = $this->s3->getObjectUrl(\Config::get('aws.bucketname'), $awsS3Key);
            array_push($expiringUrls, $expUrl);
        }

        // ADD (DOWNLOAD) THE URLS TO LOCAL ZIP
        $zipname = 'S3_files_' . date("YmdHis") . '.zip';
        $zip = new ZipArchive;

        if ($zip->open($zipname, ZipArchive::CREATE)!= true) {
              exit("cannot open <$filename>\n");
        }
        foreach ($expiringUrls as $file) {
            // download file
            $download_file = file_get_contents($file);
            $fileName = explode('?', basename($file));
            // add it to the zip
            $zip->addFromString($fileName[0], $download_file);
        }
        $zip->close();
        // PASS THE PATH OF DOWNLOADABLE ZIP TO BROWSER
        $zipFilePath = $zipname;

        return $zipFilePath;
    }

    /**
     * [downloadS3toZip - Download created zip file]
     * @param  [type] $fileUrl [description]
     * @return [type] [description]
     */
    public function downloadS3toZip($fileUrl)
    {
        $baseName = basename($fileUrl);
        // Pass to browser for download
        header('Content-Type: application/zip');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Disposition: attachment; filename="'. $baseName .'"');
        header("Content-length: " . filesize($baseName));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($fileUrl);
        // Remove the File on the server
        unlink(public_path().'/'.$baseName);
        $this->filesystem->delete($baseName);
    }

    /**
     * [getS3Url description]
     * @param  [String] $awsS3Key [Contains AWS key]
     * @return [String] [Expiring url of the requested s3 object]
     */
    public function getS3Url($awsS3Key)
    {
        return $this->s3->getObjectUrl(
            \Config::get('aws.bucketname'),
            $awsS3Key,
            '15 minutes'
        );
    }

    /**
     * [deleteOlderFiles Deletes older junk zipfiles]
     * @return [type] [description]
     */
    public function deleteOlderFiles()
    {
        // Loop through all zip files
        foreach (glob("*.zip") as $file) {
            // Delete files if older than 2 hours
            if (filemtime($file) < time() - 7200) {
                unlink($file);
            }
        }
    }
}
