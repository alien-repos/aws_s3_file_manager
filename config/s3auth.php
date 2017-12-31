<?php

/**************************************************************
 ACCESS CONTROL CONFIGURATION FOR DIRECTORIES BASED ON ROLES
**************************************************************/

return array (

	// AWS S3 Permissions Allowed Refference
    // U = Upload, D = Download,
    // E = Erase, X = No Permission 
    // 'XXX' => 0,
    // 'XXE' => 1,
    // 'XDX' => 2,
    // 'XDE' => 3,
    // 'UXX' => 4,
    // 'UXE' => 5,
    // 'UDX' => 6,
    // 'UDE' => 7,
    

    // UPLOAD
    'upload' => array(4,5,6,7),
    // DOWNLOAD AND VIEW
    'download' => array(2,3,6,7),
    // DELETE
    'delete' => array(1,3,5,7),

	// Permissions for each directory on each role 
	'admin'             => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 7,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'localteamlead'     => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'photographer'      => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'servicesassociate' => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'editingteamlead'   => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'editor'            => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'catalogteamlead'   => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	'cataloger'         => array(
						'Edited_Images' => 7,
						'MIF_Flatfiles' => 6,
						'Raw_Images' => 2,
						'3D_Images' => 7,
						'Prione_shots' => 7,
						'Seller_Images' => 7
						),
	 // Static Folders
	'staticFolders' => array(
		'Edited_Images', 
		'MIF_Flatfiles',
		'Raw_Images',
		'3D_Images',
		'Prione_shots',
		'Seller_Images')
);