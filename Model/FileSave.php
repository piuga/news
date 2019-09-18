<?php
declare(strict_types=1);

namespace Piuga\News\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Piuga\News\Api\Data\NewsInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FileSave
 * @package Piuga\News\Model
 */
class FileSave
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FileUploader
     */
    protected $imageUploader;

    /**
     * @var FileUploader
     */
    protected $fileUploader;

    /**
     * @var string
     */
    private $additionalData = '_additional_data_';

    /**
     * FileSave constructor.
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param FileUploader $imageUploader
     * @param FileUploader $fileUploader
     */
    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        FileUploader $imageUploader,
        FileUploader $fileUploader
    ) {
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->logger = $logger;
        $this->imageUploader = $imageUploader;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Get media attributes and corresponding uploader object
     *
     * @return array
     */
    protected function getMediaAttributes() : array
    {
        return [
            NewsInterface::IMAGE => $this->imageUploader,
            NewsInterface::FILE => $this->fileUploader
        ];
    }

    /**
     * Prepare files before object is saved in DB
     *
     * @param AbstractModel $object
     */
    public function beforeSave(AbstractModel $object)
    {
        $attributes = $this->getMediaAttributes();

        foreach ($attributes as $attributeCode => $uploader) {
            $value = $object->getData($attributeCode);
            $object->setData($this->additionalData . $attributeCode, $value);

            if ($this->fileResidesOutsideMediaDir($value)) {
                // Use relative path for file attribute so we know it's outside of media dir when we fetch it
                $value[0]['name'] = $value[0]['url'];
            } elseif ($value) {
                $value[0]['name'] = $uploader->getBasePath() . '/'. $value[0]['name'];
            }

            if ($fileName = $this->getUploadedFileName($value)) {
                $object->setData($attributeCode, $fileName);
            } elseif (!is_string($value)) {
                $object->setData($attributeCode, null);
            }
        }
    }

    /**
     * Save uploaded file or move temporary file to permanent location
     *
     * @param AbstractModel $object
     * @return AbstractModel
     */
    public function afterSave(AbstractModel $object) : AbstractModel
    {
        $attributes = $this->getMediaAttributes();

        foreach ($attributes as $attributeCode => $uploader) {
            $value = $object->getData($this->additionalData . $attributeCode);

            if ($value && $this->isTmpFileAvailable($value) && ($fileName = $this->getUploadedFileName($value))) {
                try {
                    $uploader->moveFileFromTmp($fileName);
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }

        return $object;
    }

    /**
     * Check for file path resides outside of media dir. The URL will be a path including pub/media if true
     *
     * @param array|null $value
     * @return bool
     */
    private function fileResidesOutsideMediaDir(?array $value) : bool
    {
        if (!is_array($value) || !isset($value[0]['url'])) {
            return false;
        }

        $fileUrl = ltrim($value[0]['url'], '/');
        $baseMediaDir = $this->filesystem->getUri(DirectoryList::MEDIA);

        $usingPathRelativeToBase = strpos($fileUrl, $baseMediaDir) === 0;

        return $usingPathRelativeToBase;
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is null
     *
     * @param array|null $value
     * @return string
     */
    private function getUploadedFileName(?array $value) : string
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array|null $value
     * @return bool
     */
    private function isTmpFileAvailable(?array $value) : bool
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }
}
