<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;
use \Config;
use \App;
use ZipArchive;
use Illuminate\Filesystem\Filesystem;
<<<<<<< HEAD
// use Aws\S3\S3Client;
=======
>>>>>>> b1dc9dc... added choosing and loading buckets and items, corrected one way file navigation in list view, removed junk code

class S3Repository extends Model
{
    public function __construct()
    {
        $this->s3 = App::make('aws')->createClient('s3');
        // $this->bucket = Config::get('s3.bucketname');
        $this->filesystem = new Filesystem();
        $this->storage = public_path('uploads');
        // Instantiate the client.
        // $this->s3 = S3Client::factory(array(
        //     'key'    => 'AKIAIUZ3GGUEAXOUAV2A',
        //     'secret' => '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',
        //     'region' => 'us-east-2',
        //     'version' => 'latest'
        // ));
    }

    public function uploadFilesToS3($request)
    {
        $file = $request->file('image');
        $file->move(Config::get('s3.upload_store'), $file->getClientOriginalName());

        $result = $this->s3->putObject(array(
                                'Bucket'    => $this->bucket,
                                'Key'       => '/prionecataloguing/',
                                'SourceFile'=> $this->storage,
                                'ACL'       => 'public-read'
                            ));

        return $result->toArray();

        // $this->ticketImageAWS->saveTicketImageOrFolder($userRole, $user, Input::all());
    }

    public function downloadFilesInS3($encodedUrl)
    {
        $decodeUrl = str_replace(';', '/', $encodedUrl);
        $fileName = basename($decodeUrl);
        $destinationPath = public_path('uploads');
        $filePath = $destinationPath . '/' . $fileName;
        if (file_exists($filePath)) {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            readfile($filePath);
            exit;
        }
    }

    public function deleteFilesInS3($request)
    {
        $files = $request->get('urlArray');
        $files = json_decode($files);

        $destinationPath = public_path('uploads');
        foreach ($files as $key => $file) {
            $filePath = $destinationPath . '/' . basename($file);
            $deleteFile = unlink($filePath);
        }
        if ($deleteFile) {
            return ['state' => 'true', 'message' => 'Files Deleted Successfully' ];
        } else {
            return ['state' => 'false', 'message' => 'Cannot delete Files' ];
        }
    }

    public function thumbNailView($path = '/prionecataloguing/fosBhopal/12-2015/8282_srimaa_energy/')
    {
    	// Config data
    	$baseUrl = Config::get('app.url');
    	$storagePath = Config::get('s3.upload_store');
        $s3Key = 'fosBhopal/12-2015/8282_srimaa_energy/';

        // $dirList = scandir($storagePath);
        // // echo $dirList[5];
        // $urlsArray = [];
        // foreach ($dirList as $key => $filename) {
        //     // If periods are present in dir list ignore them
        //     if ($filename == '.' || $filename == '..') {
        //         continue;
        //     }
        //     array_push($urlsArray, $baseUrl . 'uploads' . '/' . $filename);
        // }
        // return $urlsArray;
        // Use the high-level iterators (returns ALL of your objects).
        $objects = $this->s3->getIterator('ListObjects', array(
            'Bucket' => $this->bucket,
            'Prefix' => $s3Key));
        $urls = [];
        
        foreach ($objects as $object) {
                $expUrl = $this->s3->getObjectUrl(
                $this->bucket,
                $object['Key'],
                '15 minutes'
                );
            array_push($urls, $expUrl);
        }

        return $urls;
    }

    public function listView($path)
    {
<<<<<<< HEAD
        // Config data
        $baseUrl = Config::get('app.url');
        $storagePath = Config::get('s3.upload_store');
        // $s3Key = 'fosBhopal/12-2015/8282_srimaa_energy/';
=======
>>>>>>> b1dc9dc... added choosing and loading buckets and items, corrected one way file navigation in list view, removed junk code
        $s3Key = $path;

        // Use the high-level iterators (returns ALL of your objects).
        $objects = $this->s3->getIterator('ListObjects', array(
            'Bucket' => $this->bucket,
            'Prefix' => $s3Key));
<<<<<<< HEAD
        
        $urls = [];
=======
        $files = [];
        // foreach ($objects as $k => $v) {
        //     print_r($v);
        // }
        // exit;
>>>>>>> b1dc9dc... added choosing and loading buckets and items, corrected one way file navigation in list view, removed junk code
        
        foreach ($objects as $object) {
            $s3Key = rtrim($s3Key, '/') . '/';
            $s3KeyArray = explode($s3Key, $object['Key']);
            $lastArrayItem = $s3KeyArray[count($s3KeyArray)-1];
            $dirName = explode('/', $lastArrayItem);
            $dirName = $dirName[0];
            if ($dirName == null) {
                continue;
            }
<<<<<<< HEAD
            $dirName = $s3Key . $dirName;
            array_push($urls, $dirName);
        }
        $urls = array_unique($urls);
=======

            // $currentPaths[] = $s3Key . $dirName;
            $files[] = $dirName;
        }

        // print_r($urls);
        // exit;
        $urlArray['urls'] = ['curernt_path' => $path, 'files' => array_unique($files)];
>>>>>>> b1dc9dc... added choosing and loading buckets and items, corrected one way file navigation in list view, removed junk code
        
        return $urls;
    }

    public function updateConfiguration($type, $value) {
        if ($type === 'bucket') {
            config(['s3.bucketname' => $value]);
            // Config::get('s3.bucketname') = $value;
        }
    }
}
