<?php


namespace Dave\Provider;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\ServiceProviderInterface;


/**
 * Class ServiceProvider
 * @package Dave\Provider
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app) {
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new TwigServiceProvider(), array('twig.path' => __DIR__ . '/../../src/View'));
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new DoctrineServiceProvider());

        /* third party service providers */
        $app->register(new DoctrineOrmServiceProvider(), array(
            'orm.proxies_dir' => __DIR__ . '/../../app/cache/doctrine/proxy',
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type'                         => 'annotation',
                        'namespace'                    => 'Dave\Entity',
                        'path'                         => __DIR__ . '/../../src/Entity',
                        'use_simple_annotation_reader' => false
                    )
                )
            ),
        ));
        AnnotationRegistry::registerLoader(array(require __DIR__ . '/../../vendor/autoload.php', 'loadClass'));
        AnnotationReader::addGlobalIgnoredName('type');

        $app['console'] = $app->share(function() use ($app) {
            return ConsoleRunner::createApplication(ConsoleRunner::createHelperSet($app['orm.em']));
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app) {}
}