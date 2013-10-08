<?php
namespace Dizzy7\MenuGeneratorBundle\Menu\Mapping;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 */
class Menu {

    private $name;

    public function __construct($metadata = array())
    {
        $this->name = (isset($metadata['name']) && $metadata['name'] != '') ? $metadata['name'] : false;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if($this->name === false){
            throw new AnnotationException('Name not defined! Use @Menu\\Action(name="Menu item")');
        }
        return $this->name;
    }


} 