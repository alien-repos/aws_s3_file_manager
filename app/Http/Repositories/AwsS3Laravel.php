<?php

namespace Compassites\AwsS3Laravel;

class AwsS3Laravel
{
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
}
