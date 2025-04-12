<?php
namespace SmartInformationSystems\FileBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;

use SmartInformationSystems\FileBundle\Entity\File;
use SmartInformationSystems\FileBundle\Storage\AbstractStorage;
use SmartInformationSystems\FileBundle\Storage\ConfigurationContainer;
use SmartInformationSystems\FileBundle\Storage\StorageFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Расширение для файлов
 */
class FileExtension extends AbstractExtension
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
        return array(
            new TwigFilter('sis_get_url', [$this, 'fileGetUrlFilter']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sis_file_extension';
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
     * Возвращает ссылку на файл
     *
     * @param File $file Файл
     *
     * @return string
     */
    public function fileGetUrlFilter($file)
    {
        return $this->getStorage()->getUrl($file);
    }
}
