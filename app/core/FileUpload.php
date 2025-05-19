<?php
namespace App\Core;

/**
 * FileUpload Class
 * 
 * Handles file uploads with validation and processing
 */
class FileUpload {
    /**
     * @var array Allowed file extensions
     */
    private $allowedExtensions = [];
    
    /**
     * @var int Maximum file size in bytes
     */
    private $maxFileSize = 5242880; // 5MB
    
    /**
     * @var string Upload directory
     */
    private $uploadDir = '';
    
    /**
     * @var array Error messages
     */
    private $errors = [];
    
    /**
     * @var string Uploaded file path
     */
    private $uploadedFilePath = '';
    
    /**
     * Constructor
     * 
     * @param string $uploadDir Upload directory
     * @param array $allowedExtensions Allowed file extensions
     * @param int $maxFileSize Maximum file size in bytes
     */
    public function __construct($uploadDir = 'uploads', $allowedExtensions = [], $maxFileSize = 0) {
        // Set upload directory
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        
        // Create directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        
        // Set allowed extensions
        if (!empty($allowedExtensions)) {
            $this->allowedExtensions = $allowedExtensions;
        }
        
        // Set max file size
        if ($maxFileSize > 0) {
            $this->maxFileSize = $maxFileSize;
        }
    }
    
    /**
     * Set allowed file extensions
     * 
     * @param array $extensions Allowed file extensions
     * @return $this
     */
    public function setAllowedExtensions($extensions) {
        $this->allowedExtensions = $extensions;
        return $this;
    }
    
    /**
     * Set maximum file size
     * 
     * @param int $size Maximum file size in bytes
     * @return $this
     */
    public function setMaxFileSize($size) {
        $this->maxFileSize = $size;
        return $this;
    }
    
    /**
     * Set upload directory
     * 
     * @param string $dir Upload directory
     * @return $this
     */
    public function setUploadDir($dir) {
        $this->uploadDir = rtrim($dir, '/') . '/';
        
        // Create directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
        
        return $this;
    }
    
    /**
     * Upload a file
     * 
     * @param array $file File from $_FILES
     * @param string $newFilename New filename (optional)
     * @return bool True if upload was successful
     */
    public function upload($file, $newFilename = '') {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->errors[] = 'No file was uploaded';
            return false;
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $this->errors[] = 'File size exceeds the maximum limit of ' . Helpers::formatFileSize($this->maxFileSize);
            return false;
        }
        
        // Get file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check file extension
        if (!empty($this->allowedExtensions) && !in_array($fileExtension, $this->allowedExtensions)) {
            $this->errors[] = 'File extension not allowed. Allowed extensions: ' . implode(', ', $this->allowedExtensions);
            return false;
        }
        
        // Generate new filename if not provided
        if (empty($newFilename)) {
            $newFilename = uniqid() . '_' . Helpers::slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        }
        
        // Add extension to new filename
        $newFilename = $newFilename . '.' . $fileExtension;
        
        // Set upload path
        $uploadPath = $this->uploadDir . $newFilename;
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $this->uploadedFilePath = $uploadPath;
            return true;
        } else {
            $this->errors[] = 'Failed to move uploaded file';
            return false;
        }
    }
    
    /**
     * Upload an image with resizing
     * 
     * @param array $file File from $_FILES
     * @param string $newFilename New filename (optional)
     * @param int $maxWidth Maximum width (optional)
     * @param int $maxHeight Maximum height (optional)
     * @param int $quality Image quality (1-100, optional)
     * @return bool True if upload was successful
     */
    public function uploadImage($file, $newFilename = '', $maxWidth = 0, $maxHeight = 0, $quality = 90) {
        // Set allowed extensions to image types
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Upload file
        if (!$this->upload($file, $newFilename)) {
            return false;
        }
        
        // If no resizing needed, return
        if ($maxWidth <= 0 && $maxHeight <= 0) {
            return true;
        }
        
        // Get image info
        $imageInfo = getimagesize($this->uploadedFilePath);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        
        // If image is smaller than max dimensions, return
        if (($maxWidth <= 0 || $width <= $maxWidth) && ($maxHeight <= 0 || $height <= $maxHeight)) {
            return true;
        }
        
        // Calculate new dimensions
        $newWidth = $width;
        $newHeight = $height;
        
        if ($maxWidth > 0 && $width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($maxWidth / $width) * $height;
        }
        
        if ($maxHeight > 0 && $newHeight > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = ($maxHeight / $newHeight) * $newWidth;
        }
        
        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($this->uploadedFilePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($this->uploadedFilePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($this->uploadedFilePath);
                break;
            default:
                $this->errors[] = 'Unsupported image type';
                return false;
        }
        
        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $this->uploadedFilePath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $this->uploadedFilePath, round($quality / 10));
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $this->uploadedFilePath);
                break;
        }
        
        // Free memory
        imagedestroy($source);
        imagedestroy($destination);
        
        return true;
    }
    
    /**
     * Get uploaded file path
     * 
     * @return string Uploaded file path
     */
    public function getUploadedFilePath() {
        return $this->uploadedFilePath;
    }
    
    /**
     * Get errors
     * 
     * @return array Error messages
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get upload error message
     * 
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
}
