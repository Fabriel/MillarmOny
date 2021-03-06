<?php

namespace Millarmony\SiteBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Millarmony\SiteBundle\Entity\Miniatures;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Photos
 *
 * @ORM\Table(name="photos")
 * @ORM\Entity(repositoryClass="Millarmony\SiteBundle\Repository\PhotosRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Photos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Millarmony\SiteBundle\Entity\Miniatures", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $miniature;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="concert", type="boolean")
     */
    private $concert;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    private $file;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->updatedAt = new DateTime();
    }

    public function getUploadRootDir()
    {
        return __dir__.'/../../../../web/uploads/photos';
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getAssetPath()
    {
        return 'uploads/photos/'.$this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
        $this->oldFile = $this->getPath();
        $this->updatedAt = new DateTime();

        if (null !== $this->file)
        {
            $this->alt = $this->file->getClientOriginalName();
            $this->path = $this->alt;
            $dot_pos = strpos($this->alt, '.');
            $this->alt = substr($this->alt, 0, $dot_pos);
        }

    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null !== $this->file) {
            $this->file->move($this->getUploadRootDir(),$this->path);
            unset($this->file);

            if ($this->oldFile != null) unlink($this->tempFile);
        }

    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) unlink($this->tempFile);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Photos
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Photos
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     *
     * @return Photos
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Set miniature
     *
     * @param Miniatures $miniature
     *
     * @return Photos
     */
    public function setMiniature(Miniatures $miniature)
    {
        $this->miniature = $miniature;

        return $this;
    }

    /**
     * Get miniature
     *
     * @return Miniatures
     */
    public function getMiniature()
    {
        return $this->miniature;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Photos
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set concert
     *
     * @param boolean $concert
     *
     * @return Photos
     */
    public function setConcert($concert)
    {
        $this->concert = $concert;

        return $this;
    }

    /**
     * Get concert
     *
     * @return boolean
     */
    public function getConcert()
    {
        return $this->concert;
    }
}
