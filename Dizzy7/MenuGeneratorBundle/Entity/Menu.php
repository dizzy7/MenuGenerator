<?php

namespace Dizzy7\MenuGeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Entity
 * @ORM\Table(name="Menu")
 */
class Menu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255,nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="sort", type="integer",nullable=true)
     */
    private $sort;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id",nullable=true)
     **/
    private $parent;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Menu
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    
        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Add children
     *
     * @param \Dizzy7\MenuGeneratorBundle\Entity\Menu $children
     * @return Menu
     */
    public function addChildren(\Dizzy7\MenuGeneratorBundle\Entity\Menu $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \Dizzy7\MenuGeneratorBundle\Entity\Menu $children
     */
    public function removeChildren(\Dizzy7\MenuGeneratorBundle\Entity\Menu $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Dizzy7\MenuGeneratorBundle\Entity\Menu $parent
     * @return Menu
     */
    public function setParent(\Dizzy7\MenuGeneratorBundle\Entity\Menu $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Dizzy7\MenuGeneratorBundle\Entity\Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Menu
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
}