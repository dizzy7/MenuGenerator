<?php
namespace Dizzy7\MenuGeneratorBundle\Menu\Mapping;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 */
class Menu {

    private $name;
    private $sort;

    public function __construct($metadata = array())
    {
        $this->name = (isset($metadata['name']) && $metadata['name'] != '') ? $metadata['name'] : null;
        $this->sort = (isset($metadata['sort']) && $metadata['sort'] != '') ? $metadata['sort'] : null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if($this->name === false){
            throw new AnnotationException('Name not defined! Use @Menu(name="Menu item")');
        }
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

} 