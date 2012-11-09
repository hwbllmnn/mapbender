<?php

namespace Mapbender\WmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Mapbender\WmsBundle\Entity\WmsInstanceLayer;
use Mapbender\WmsBundle\Entity\WmsSource;

/**
 * WmsInstance class
 *
 * @author Paul Schmidt <paul.schmidt@wheregroup.com>
 * 
 * @ORM\Entity
 * @ORM\Table(name="mb_wms_wmsinstanse")
 */
class WmsInstance {

    /**
     *  @ORM\Id
     *  @ORM\Column(type="integer")
     *  @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="WmsSource", inversedBy="id", cascade={"refresh", "persist"})
     * @ORM\JoinColumn(name="wmssource", referencedColumnName="id")
     */
    protected $wmssource;

    /**
     * @ORM\OneToMany(targetEntity="WmsInstanceLayer", mappedBy="id", cascade={"refresh", "persist"})
     * @ORM\JoinColumn(name="layers", referencedColumnName="id")
     */
    protected $layers; //{ name: 1,   title: Webatlas,   visible: true }

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $srs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $format;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $infoformat;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $exceptionformat = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $visibile = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $opacity = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $proxy = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $tiled = false;

    public function __construct() {
        $this->layers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set layers
     *
     * @param array $layers
     * @return WmsInstance
     */
    public function setLayers($layers) {
        $this->layers = $layers;

        return $this;
    }

    /**
     * Get layers
     *
     * @return array 
     */
    public function getLayers() {
        return $this->layers;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return WmsInstance
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set srs
     *
     * @param array $srs
     * @return WmsInstance
     */
    public function setSrs($srs) {
        $this->srs = $srs;

        return $this;
    }

    /**
     * Get srs
     *
     * @return array 
     */
    public function getSrs() {
        return $this->srs;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return WmsInstance
     */
    public function setFormat($format) {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string 
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * Set infoformat
     *
     * @param string $infoformat
     * @return WmsInstance
     */
    public function setInfoformat($infoformat) {
        $this->infoformat = $infoformat;

        return $this;
    }

    /**
     * Get infoformat
     *
     * @return string 
     */
    public function getInfoformat() {
        return $this->infoformat;
    }

    /**
     * Set exceptionformat
     *
     * @param string $exceptionformat
     * @return WmsInstance
     */
    public function setExceptionformat($exceptionformat) {
        $this->exceptionformat = $exceptionformat;

        return $this;
    }

    /**
     * Get exceptionformat
     *
     * @return string 
     */
    public function getExceptionformat() {
        return $this->exceptionformat;
    }

    /**
     * Set visibile
     *
     * @param boolean $visibile
     * @return WmsInstance
     */
    public function setVisibile($visibile) {
        $this->visibile = $visibile;

        return $this;
    }

    /**
     * Get visibile
     *
     * @return boolean 
     */
    public function getVisibile() {
        return $this->visibile;
    }

    /**
     * Set opacity
     *
     * @param boolean $opacity
     * @return WmsInstance
     */
    public function setOpacity($opacity) {
        $this->opacity = $opacity;

        return $this;
    }

    /**
     * Get opacity
     *
     * @return boolean 
     */
    public function getOpacity() {
        return $this->opacity;
    }

    /**
     * Set proxy
     *
     * @param boolean $proxy
     * @return WmsInstance
     */
    public function setProxy($proxy) {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Get proxy
     *
     * @return boolean 
     */
    public function getProxy() {
        return $this->proxy;
    }

    /**
     * Set tiled
     *
     * @param boolean $tiled
     * @return WmsInstance
     */
    public function setTiled($tiled) {
        $this->tiled = $tiled;

        return $this;
    }

    /**
     * Get tiled
     *
     * @return boolean 
     */
    public function getTiled() {
        return $this->tiled;
    }

    /**
     * Set wmssource
     *
     * @param WmsSource $wmssource
     * @return WmsInstance
     */
    public function setWmssource(WmsSource $wmssource = null) {
        $this->wmssource = $wmssource;

        return $this;
    }

    /**
     * Get wmssource
     *
     * @return WmsSource 
     */
    public function getWmssource() {
        return $this->wmssource;
    }


    /**
     * Add layers
     *
     * @param WmsInstanceLayer $layers
     * @return WmsInstance
     */
    public function addLayer(WmsInstanceLayer $layers)
    {
        $this->layers[] = $layers;
    
        return $this;
    }

    /**
     * Remove layers
     *
     * @param WmsInstanceLayer $layers
     */
    public function removeLayer(WmsInstanceLayer $layers)
    {
        $this->layers->removeElement($layers);
    }
}