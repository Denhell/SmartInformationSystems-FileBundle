<?php
namespace SmartInformationSystems\FileBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * Картинка.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Image
{
    /**
     * Ширина.
     *
     * @var int
     */
    public $width;

    /**
     * Высота.
     *
     * @var int
     */
    public $height;

    /**
     * Обрезать при ресайзе.
     *
     * @var bool
     */
    public $crop = false;

    /**
     * @var array<SmartInformationSystems\FileBundle\Annotations\Image\Preview>
     */
    public $previews = [];
}
