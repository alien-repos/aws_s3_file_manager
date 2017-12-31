<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;

class TextEditorController extends Controller
{
    public function readTextFile()
    {
        $txt = (base_path() . '/app/Http/Controllers/CRUDController.php');
        $fileContents = file_get_contents($txt);
        echo '<html><body><textarea rows="30" cols="60">' . $fileContents . '</textarea></body></html>';
    }
}
