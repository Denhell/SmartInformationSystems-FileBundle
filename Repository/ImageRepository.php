<?php
namespace SmartInformationSystems\FileBundle\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use SmartInformationSystems\FileBundle\Common\OriginalFile;
use SmartInformationSystems\FileBundle\Common\AbstractRepository;
use SmartInformationSystems\FileBundle\Entity\Image;
use SmartInformationSystems\FileBundle\Entity\ImagePreview;

class ImageRepository extends AbstractRepository
{
    /**
     * Создание экземпляра объекта
     *
     * @param OriginalFile $originalFile Оригиналный файл
     * @param array $options Настройки
     *
     * @return Image
     */
    public function createEntity(OriginalFile $originalFile, array $options = [])
    {
        $image = new Image($originalFile);

        if (isset($options['previews'])) {
            if (!is_array($options['previews'])) {
                $options['previews'] = array($options['previews']);
            }

            /** @var ImagePreview $preview */
            foreach ($options['previews'] as $preview) {
                $preview->setImage($image);
                $image->addPreview($preview);
            }
        }

        return $image;
    }

    /**
     * Ресайз изображения, хранящегося в файле, и сохранение в этот же файл
     *
     * @param OriginalFile $file Файл
     * @param integer $width Ширина
     * @param integer $height Высота
     * @param boolean $crop Обрезать ли
     *
     * @return void
     */
    public static function resizeFile(OriginalFile $file, $width, $height, $crop)
    {
        $path = $file->getRealPath();

        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: $path");
        }

        if (filesize($path) === 0) {
            throw new \RuntimeException("Empty file: $path");
        }

        $info = getimagesize($path);
        if ($info === false) {
            throw new \RuntimeException("File is not image: $path");
        }

        $data = @file_get_contents($path);
        $resource = imagecreatefromstring($data);
var_dump($resource);
var_dump(is_resource($resource));

        $imagine = new Imagine();
        $imagine
            ->open($file->getRealPath())
            ->thumbnail(
                new Box($width, $height),
                $crop ? ImageInterface::THUMBNAIL_OUTBOUND : ImageInterface::THUMBNAIL_INSET
            )
            ->save($file->getRealPath(), [
                'jpeg_quality' => 100,
                'png_compression_level' => 9,
            ]);

        clearstatcache(true, $file->getRealPath());
    }

    /**
     * Возвращает настройки создания изображения по аннотации поля
     *
     * @param OriginalFile $originalFile Исходный файл
     * @param string $class Имя класса
     * @param string $property Имя поля
     *
     * @return array
     */
    public static function getCreateOptions(OriginalFile $originalFile, $class, $property)
    {
        $options = [];

        // Обработаем изображение
        $reader = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty($property);

        if ($annotation = $reader->getPropertyAnnotation(
            $reflectionProperty,
            'SmartInformationSystems\FileBundle\Annotations\Image'
        )) {
            $options['previews'] = [];

            if (count($annotation->previews) > 0) {

                foreach ($annotation->previews as $previewSettings) {
                    $options['previews'][] = ImagePreviewRepository::createPreviewFromFile(
                        $originalFile,
                        $previewSettings->name,
                        $previewSettings->width,
                        $previewSettings->height,
                        $previewSettings->crop
                    );
                }
            }

            if ($annotation->width && $annotation->height) {
                self::resizeFile(
                    $originalFile,
                    $annotation->width,
                    $annotation->height,
                    $annotation->crop
                );
            }
        }

        return $options;
    }
}
