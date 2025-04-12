<?php
namespace SmartInformationSystems\FileBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use SmartInformationSystems\FileBundle\Entity\Image;
use SmartInformationSystems\FileBundle\Entity\ImagePreview;
use SmartInformationSystems\FileBundle\Repository\ImagePreviewRepository;
use SmartInformationSystems\FileBundle\Storage\AbstractStorage;
use SmartInformationSystems\FileBundle\Storage\ConfigurationContainer;
use SmartInformationSystems\FileBundle\Storage\StorageFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Расширение для изображений.
 */
class ImageExtension extends AbstractExtension
{
    /**
     * Подключение к БД
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Хранилище файлов
     *
     * @var AbstractStorage
     */
    private $storage;

    /**
     * Хранилище файлов
     *
     * @var $storageConfiguration
     */
    private $storageConfiguration;

    /**
     * Конструктор.
     *
     * @param EntityManagerInterface $entityManager Подключение к БД
     * @param ConfigurationContainer $storageConfiguration Настройки
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigurationContainer $storageConfiguration)
    {
        $this->em = $entityManager;
        $this->storageConfiguration = $storageConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('sis_image_preview', [$this, 'previewFilter']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sis_image_extension';
    }

    /**
     * Возвращает хранилище файлов
     *
     * @return AbstractStorage
     */
    private function getStorage()
    {
        if ($this->storage === null) {
            $this->storage = StorageFactory::create($this->storageConfiguration);
        }

        return $this->storage;
    }

    /**
     * Возвращает ссылку на превью
     *
     * @param Image $image Изображение
     * @param string $name Имя превью
     *
     * @return string
     */
    public function previewFilter(Image $image, $name)
    {
        /** @var ImagePreviewRepository $rep */
        $rep = $this->em->getRepository(ImagePreview::class);

        if ($preview = $rep->getByName($image, $name)) {
            return $this->getStorage()->getUrl($preview);
        } else {
            return $this->getStorage()->getUrl($image);
        }
    }
}
