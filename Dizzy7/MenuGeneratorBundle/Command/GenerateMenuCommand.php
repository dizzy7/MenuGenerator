<?php
/**
 * Created by PhpStorm.
 * User: dizzy
 * Date: 05.10.13
 * Time: 21:49
 */

namespace Dizzy7\MenuGeneratorBundle\Command;

use Dizzy7\MenuGeneratorBundle\Entity\Menu;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class GenerateMenuCommand extends Command implements ContainerAwareInterface {


    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function configure(){
        $this
            ->setName('menu:generate')
            ->setDescription('Generate menu from controllers annotation')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        /** @var $router Router */
        $router = $this->container->get('router');
        $this->routeCollection = $router->getRouteCollection();

        $output->writeln('Truncate menu table');
        $className = "dizzy7\\AutoMenuBundle\\Entity\\Menu";
        /** @var $em EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        $cmd = $em->getClassMetadata($className);
        /** @var $connection Connection */
        $connection = $this->container->get('doctrine')->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollback();
            $output->writeln('Error while truncating!');
            return;
        }

        $output->writeln('Updating menu');

        $bundles = $this->container->getParameter('kernel.bundles');

        /** @var $reader Reader */
        $reader = $this->container->get('annotation_reader');
        /** @var $em EntityManager */
        $em = $this->container->get('doctrine')->getManager();

        foreach ($bundles as $bundleName) {
            if(!preg_match('#^Snews#',$bundleName)){
                continue;
            }
            /** @var $bundle Bundle */
            $bundle  = new $bundleName;
            $bundleNamespace = preg_replace('/^(.*\\\\).*/','$1',$bundleName);
            $controllersPath = $bundle->getPath().'/Controller';
            $finder = new Finder();
            $controllersFile = $finder->files()->in($controllersPath)->getIterator();
            /** @var $controllersFile SplFileInfo[] */
            foreach ($controllersFile as $controllerFile) {
                $path = pathinfo($controllerFile->getRealPath());

                $reflectionClass = new \ReflectionClass($bundleNamespace."Controller\\".$path['filename']);

                $annotation = $reader->getClassAnnotation($reflectionClass,'dizzy7\\AutoMenuBundle\\Menu\\Mapping\\Controller');

                if($annotation === null){
                    continue;
                }
                $output->writeln($annotation->getName());

                $topMenu = new Menu();

                $topMenu->setName($annotation->getName());
                $em->persist($topMenu);

                $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($reflectionMethods as $method) {
                    if(!preg_match('/Action$/',$method->getName())){
                        continue;
                    }
                    $annotation = $reader->getMethodAnnotation($method,'dizzy7\\AutoMenuBundle\\Menu\\Mapping\\Action');
                    if($annotation!==null){
                        $menuItem = new Menu();
                        $menuItem->setName($annotation->getName());
                        $menuItem->setParent($topMenu);
                        $menuItem->setPath($this->findPathByAction($reflectionClass->getName(),$method->getName()));
                        $output->writeln($annotation->getName());
                        $em->persist($menuItem);
                    }

                }

            }

        }
        $em->flush();
        $output->writeln('Menu updated!');
    }

    private function findPathByAction($controllerNamespace,$action){
        $action = $controllerNamespace.'::'.$action;

        /** @var $route Route */
        foreach ($this->routeCollection->getIterator() as $routeName=>$route) {
            if($route->getDefault('_controller')==$action){
                return $routeName;
            }
        }

        return null;
    }

} 