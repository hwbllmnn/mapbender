<?php
namespace Mapbender\WmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Mapbender\WmsBundle\Entity\WmsInstance;
use Mapbender\WmsBundle\Entity\WmsLayerSource;

/**
 * WmsInstanceLayer class
 *
 * @author Paul Schmidt <paul.schmidt@wheregroup.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_wms_wmsinstancelayer")
*/
class WmsInstanceLayer {
    
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="WmsInstance", inversedBy="layers", cascade={"persist"})
     * @ORM\JoinColumn(name="wmsinstance", referencedColumnName="id")
     */
    protected $wmsinstance;
    
    /**
     * @ORM\ManyToOne(targetEntity="WmsLayerSource", inversedBy="id", cascade={"refresh", "persist"})
     * @ORM\JoinColumn(name="wmslayersource", referencedColumnName="id")
     */
    protected $wmslayersource;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $sublayer = true;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $selected = true;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $selected_default = true;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $gfinfo = true;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $gfinfo_default = true;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $minScale = 0;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $maxScale = 0;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $style = "";
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $priority = 0;
    

    public function __construct() {
        
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return WmsInstanceLayer
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sublayer
     *
     * @param boolean $sublayer
     * @return WmsInstanceLayer
     */
    public function setSublayer($sublayer)
    {
        $this->sublayer = $sublayer;
    
        return $this;
    }

    /**
     * Get sublayer
     *
     * @return boolean 
     */
    public function getSublayer()
    {
        return $this->sublayer;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return WmsInstanceLayer
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    
        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set selected_default
     *
     * @param boolean $selectedDefault
     * @return WmsInstanceLayer
     */
    public function setSelectedDefault($selectedDefault)
    {
        $this->selected_default = $selectedDefault;
    
        return $this;
    }

    /**
     * Get selected_default
     *
     * @return boolean 
     */
    public function getSelectedDefault()
    {
        return $this->selected_default;
    }

    /**
     * Set gfinfo
     *
     * @param boolean $gfinfo
     * @return WmsInstanceLayer
     */
    public function setGfinfo($gfinfo)
    {
        $this->gfinfo = $gfinfo;
    
        return $this;
    }

    /**
     * Get gfinfo
     *
     * @return boolean 
     */
    public function getGfinfo()
    {
        return $this->gfinfo;
    }

    /**
     * Set gfinfo_default
     *
     * @param boolean $gfinfoDefault
     * @return WmsInstanceLayer
     */
    public function setGfinfoDefault($gfinfoDefault)
    {
        $this->gfinfo_default = $gfinfoDefault;
    
        return $this;
    }

    /**
     * Get gfinfo_default
     *
     * @return boolean 
     */
    public function getGfinfoDefault()
    {
        return $this->gfinfo_default;
    }

    /**
     * Set minScale
     *
     * @param float $minScale
     * @return WmsInstanceLayer
     */
    public function setMinScale($minScale)
    {
        $this->minScale = $minScale;
    
        return $this;
    }

    /**
     * Get minScale
     *
     * @return float 
     */
    public function getMinScale()
    {
        return $this->minScale;
    }

    /**
     * Set maxScale
     *
     * @param float $maxScale
     * @return WmsInstanceLayer
     */
    public function setMaxScale($maxScale)
    {
        $this->maxScale = $maxScale;
    
        return $this;
    }

    /**
     * Get maxScale
     *
     * @return float 
     */
    public function getMaxScale()
    {
        return $this->maxScale;
    }

    /**
     * Set style
     *
     * @param string $style
     * @return WmsInstanceLayer
     */
    public function setStyle($style)
    {
        $this->style = $style;
    
        return $this;
    }

    /**
     * Get style
     *
     * @return string 
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return WmsInstanceLayer
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set wmsinstance
     *
     * @param WmsInstance $wmsinstance
     * @return WmsInstanceLayer
     */
    public function setWmsinstance(WmsInstance $wmsinstance = null)
    {
        $this->wmsinstance = $wmsinstance;
    
        return $this;
    }

    /**
     * Get wmsinstance
     *
     * @return WmsInstance 
     */
    public function getWmsinstance()
    {
        return $this->wmsinstance;
    }

    /**
     * Set wmslayersource
     *
     * @param WmsLayerSource $wmslayersource
     * @return WmsInstanceLayer
     */
    public function setWmslayersource(WmsLayerSource $wmslayersource = null)
    {
        $this->wmslayersource = $wmslayersource;
    
        return $this;
    }

    /**
     * Get wmslayersource
     *
     * @return WmsLayerSource 
     */
    public function getWmslayersource()
    {
        return $this->wmslayersource;
    }
}