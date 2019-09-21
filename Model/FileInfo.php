<?php
declare(strict_types=1);

namespace Piuga\News\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class FileInfo
 * @package Piuga\News\Model
 */
class FileInfo
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Mime
     */
    protected $mime;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var ReadInterface
     */
    protected $baseDirectory;

    /**
     * FileInfo constructor.
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function getMediaDirectory() : WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    /**
     * Get Base Directory read instance
     *
     * @return ReadInterface
     */
    private function getBaseDirectory() : ReadInterface
    {
        if (!isset($this->baseDirectory)) {
            $this->baseDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        }

        return $this->baseDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     */
    public function getMimeType(string $fileName) : string
    {
        $filePath = $this->getFilePath($fileName);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);
        $result = $this->mime->getMimeType($absoluteFilePath);

        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     * @throws FileSystemException
     */
    public function getStat(string $fileName) : array
    {
        $filePath = $this->getFilePath($fileName);
        $result = $this->getMediaDirectory()->stat($filePath);

        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isExist(string $fileName) : bool
    {
        $filePath = $this->getFilePath($fileName);
        $result = $this->getMediaDirectory()->isExist($filePath);

        return $result;
    }

    /**
     * Construct and return file subpath based on filename relative to media directory
     *
     * @param string $fileName
     * @return bool|string
     * @throws FileSystemException
     */
    private function getFilePath(string $fileName)
    {
        $filePath = ltrim($fileName, '/');

        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();
        $isFileNameBeginsWithMediaDirectoryPath = $this->isBeginsWithMediaDirectoryPath($fileName);

        // If the file is not using a relative path, it resides in the catalog/category media directory
        $fileIsInMediaDir = !$isFileNameBeginsWithMediaDirectoryPath;

        if ($fileIsInMediaDir) {
            $filePath =  '/' . $filePath;
        } else {
            $filePath = substr($filePath, strlen($mediaDirectoryRelativeSubpath));
        }

        return $filePath;
    }

    /**
     * Checks for whether $fileName string begins with media directory path
     *
     * @param string $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function isBeginsWithMediaDirectoryPath(string $fileName) : bool
    {
        $filePath = ltrim($fileName, '/');
        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();
        $isFileNameBeginsWithMediaDirectoryPath = strpos($filePath, $mediaDirectoryRelativeSubpath) === 0;

        return $isFileNameBeginsWithMediaDirectoryPath;
    }

    /**
     * Get media directory subpath relative to base directory path
     *
     * @return bool|string
     * @throws FileSystemException
     */
    private function getMediaDirectoryPathRelativeToBaseDirectoryPath()
    {
        $baseDirectoryPath = $this->getBaseDirectory()->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();
        $mediaDirectoryRelativeSubpath = str_replace('pub/', '', substr($mediaDirectoryPath, strlen($baseDirectoryPath)));

        return $mediaDirectoryRelativeSubpath;
    }

    /**
     * Open file to be read
     *
     * @param string $file
     * @return Filesystem\File\ReadInterface
     * @throws FileSystemException
     */
    public function readFile(string $file)
    {
        $filePath = $this->getFilePath($file);
        $mediaDirectoryRead = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absoluteFilePath = $mediaDirectoryRead->getAbsolutePath($filePath);

        return $mediaDirectoryRead->openFile($absoluteFilePath);
    }
}
