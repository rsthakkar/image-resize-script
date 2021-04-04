<?php
//Maximize script execution time
// ini_set('max_execution_time', 0);

//Initial settings, Just specify Source and Destination Image folder.
$ImagesDirectory = 'images/directory/path'; //Source Image Directory End with Slash
// $DestImagesDirectory    = 'desitnation/directory'; //Destination Image Directory End with Slash
$NewImageWidth = 100; //New Width of Image
$NewImageHeight = 100; // New Height of Image             = ''; //Imag

//Open Source Image directory, loop through each Image and resize it.
if ($dir = opendir($ImagesDirectory)) {
    while (($file = readdir($dir)) !== false) {

        $imagePath = $ImagesDirectory . $file;

        $destPath = $ImagesDirectory . "100x100_" . $file;
        $checkValidImage = @getimagesize($imagePath);
        if (file_exists($imagePath) && $checkValidImage) //Continue only if 2 given parameters are true
        {
            echo $imagePath . "\n";
            if (resizeImage($imagePath, $destPath, $NewImageWidth, $NewImageHeight)) {
                echo $file . "resize Success!\n";
            } else {
                echo $file . ' resize Failed!<br />';
            }
        }
        closedir($dir);
    }
}

//Function that resizes image.
function resizeImage($SrcImage, $DestImage, $MaxWidth, $MaxHeight)
{
    list($iWidth, $iHeight, $type) = getimagesize($SrcImage);
    $ImageScale = min($MaxWidth / $iWidth, $MaxHeight / $iHeight);
    $NewWidth = ceil($ImageScale * $iWidth);
    $NewHeight = ceil($ImageScale * $iHeight);
    $NewCanves = imagecreatetruecolor($NewWidth, $NewHeight);

    switch (strtolower(image_type_to_mime_type($type))) {
        case 'image/jpeg':
            $NewImage = imagecreatefromjpeg($SrcImage);
            break;
        case 'image/png':
            $NewImage = imagecreatefrompng($SrcImage);
            imageAlphaBlending($NewCanves, false);
            imageSaveAlpha($NewCanves, true);
            $transparency = imagecolorallocatealpha($NewCanves, 255, 255, 255, 127);
            imagefilledrectangle($NewCanves, 0, 0, $NewHeight, $NewHeight, $transparency);
            imagecopyresampled($NewCanves, $NewImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $iWidth, $iHeight);
            imagepng($NewCanves, $DestImage);
            return true;
            break;
        case 'image/gif':
            $NewImage = imagecreatefromgif($SrcImage);
            break;
        default:
            return false;
    }

    // Resize Image
    if (imagecopyresampled($NewCanves, $NewImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $iWidth, $iHeight)) {
        // copy file
        if (imagejpeg($NewCanves, $DestImage)) {
            imagedestroy($NewCanves);
            return true;
        }
    }
}
