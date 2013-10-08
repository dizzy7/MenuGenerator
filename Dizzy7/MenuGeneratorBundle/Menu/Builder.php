<?php

namespace Dizzy7\MenuGeneratorBundle\Menu;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {

    public function generateMenu(FactoryInterface $factory){

        /** @var $em EntityManager */
        $em = $this->container->get('doctrine')->getManager();

        $items = $em
            ->createQueryBuilder()
            ->select('m,c')
            ->from('Dizzy7\MenuGeneratorBundle\Entity\Menu','m')
            ->leftJoin('m.children','c')
            ->where('m.parent IS NULL')
            ->orderBy('m.sort','asc')
            ->orderBy('c.sort','asc')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;

        $menu = $factory->createItem('root');

        foreach ($items as $topMenuItem) {
            $item = $menu->addChild($topMenuItem['name']);
            foreach ($topMenuItem['children'] as $menuItem) {
                $item->addChild($menuItem['name'],array('route'=>$menuItem['path']));
            }
        }

        return $menu;
    }

}