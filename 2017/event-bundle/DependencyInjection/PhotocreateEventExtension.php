<?php

namespace Photocreate\EventBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PhotocreateEventExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('photocreate_event.store_id', $config['store_id']);
        $container->setParameter('photocreate_event.login_required', $config['login_required']);
        $container->setParameter('photocreate_event.login_form_template', $config['login_form_template']);
        $container->setParameter('photocreate_event.sign_up_link_template', $config['sign_up_link_template']);
        $container->setParameter('photocreate_event.api.version', $config['api']['version']);
        $container->setParameter('photocreate_event.api.host', $config['api']['host']);
        $container->setParameter('photocreate_event.api.version', $config['api']['version']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
