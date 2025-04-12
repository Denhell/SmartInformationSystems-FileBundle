<?php
namespace SmartInformationSystems\FileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SmartInformationSystems\FileBundle\Common\OriginalFile;
use SmartInformationSystems\FileBundle\Common\AbstractEntity;

/**
 * Файл
 */
#[ORM\Table(name: 'sis_file')]
#[ORM\Entity(repositoryClass: \SmartInformationSystems\FileBundle\Repository\FileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class File extends AbstractEntity
{
    /**
     * Имя файла
     *
     * @var string
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: false)]
    protected string $name;

    /**
     * Уникальный ключ
     *
     * @var string
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 32, nullable: false, unique: true)]
    protected string $token;

    /**
     * Внешний уникальный ключ, зависит от системы хранения
     *
     * @var string
     */
    #[ORM\Column(name: 'external_token', type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: false, unique: true)]
    protected string $externalToken;

    /**
     * Тип файла
     *
     * @var string
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: false)]
    protected string $mime;

    /**
     * Размер файла
     *
     * @var integer
     */
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER, options: ['unsigned' => true], nullable: false)]
    protected int $size;

    /**
     * {@inheritdoc}
     */
    public function __construct(OriginalFile $originalFile = null)
    {
        parent::__construct($originalFile);

        if ($file = $this->getOriginalFile()) {
            $this->name = $file->getOriginalName();
            $this->size = $file->getSize();
            $this->mime = $file->getMimeType();
        }
    }

    /**
     * Возвращает имя.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Возвращает токен
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Устанавливает токен во внешней системе
     *
     * @param string $externalToken Токен во внешней системе
     *
     * @return File
     */
    public function setExternalToken($externalToken)
    {
        $this->externalToken = $externalToken;

        return $this;
    }

    /**
     * Возвращает токен во внешней системе
     *
     * @return string
     */
    public function getExternalToken()
    {
        return $this->externalToken;
    }

    /**
     * Возвращает тип
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Возвращает размер
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Выполняется перед сохранением в БД
     */
    #[ORM\PrePersist]
    public function prePersist()
    {
        parent::prePersist();

        if (!$this->token) {
            $this->token = md5(microtime() . $this->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this;
    }

    /**
     * Является ли файл картинкой
     *
     * @return bool
     */
    public function isImage()
    {
        return preg_match('/^image\//', $this->mime);
    }
}
