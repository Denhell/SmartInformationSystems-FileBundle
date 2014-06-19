<?php

namespace SmartSystems\FileBundle\Storage;

use SmartSystems\FileBundle\Entity\File;
use SmartSystems\FileBundle\Entity\Image;
use SmartSystems\FileBundle\Entity\ImagePreview;
use SmartSystems\FileBundle\Exception\UnknownStorageParameterException;

/**
 * Абстрактный класс хранилища.
 *
 */
abstract class AbstractStorage
{
    /**
     * Параметры.
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Класс для объекта в БД.
     *
     * @var string
     */
    private $entityClass;

    /**
     * Конструктор.
     *
     * @param array $params Параметры
     */
    public function __construct(array $params = array())
    {
        $this->parameters = $params;

        $this->init();
    }

    /**
     * Инициализация.
     *
     * @return void
     */
    protected function init()
    {
    }

    /**
     * Возвращает параметр.
     *
     * @param string $name Имя параметра
     *
     * @return mixed
     *
     * @throws UnknownStorageParameterException
     */
    protected function getParam($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        throw new UnknownStorageParameterException('Неизвестный параметр: ' . $name);
    }

    /**
     * Установка класса для объектов в БД.
     *
     * @param string $entityClass Имя класса
     *
     * @return AbstractStorage
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Возвращает класс для объектов в БД.
     *
     * @return string
     */
    protected function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Возвращает ссылку на файл.
     *
     * @param File|Image|ImagePreview $file Файл
     *
     * @return string
     */
    abstract public function getUrl($file);

    /**
     * Сохраняет файл в хранилище.
     *
     * @param File $file Файл
     *
     * @return string Токен во внешней системе
     */
    abstract public function store(File $file);
}