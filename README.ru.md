MenuGenerator
=============

О пакете
--------

Бандл для Symfony2, который позволяет генерировать меню на основе аннотаций к контроллерам и действиям.

Установка
---------

Добавить в composer.json:
<pre>
    {
       "require": {
            "dizzy7/menu-generator": "dev-master"
        }
    }
</pre>    
    
Или установить из косноли:
<pre>
php composer.phar require menu-generator dev-master
</pre>

Добавить строчку к app/AppKernel.php
<pre>
class AppKernel extends Kernel
    {
        ...
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Dizzy7\MenuGeneratorBundle\Dizzy7MenuGeneratorBundle(),
            );
            ...

            return $bundles;
        }
        ...
    }
</pre>    
    
Обновить структуру базы:
<pre>
app/console doctrine:schema:update --force
</pre>
    
Использование
-------------

В классе контроллера, для действий которого нужно сгенерировать меню:

<pre>
use ...
use Dizzy7\MenuGeneratorBundle\Menu\Mapping\Menu;

/**
 * @Menu(name="Раздел меню",sort="10")
 */
class SampleController extends Controller
{

    /**
     * @Menu(name="Строка меню 2",sort="20")
     */
    public function indexAction()
    {
        ...
    }
    
    /**
     * @Menu(name="Строка меню 1",sort="10")
     */
    public function otherAction()
    {
        ...
    }
}    
</pre>

Запустить генерацию меню:
<pre>
app/console menu:generate
</pre>

В шаблоне для вывода меню спользовать:
<pre>
{{ knp_menu_render('Dizzy7MenuGeneratorBundle:Builder:generateMenu') }}
</pre>

Более подробно о шаблонах меню можно в документации [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)




  








