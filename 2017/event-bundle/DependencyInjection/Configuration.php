<?php

namespace Photocreate\EventBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('photocreate_event');

        $rootNode
            ->children()
                ->integerNode('store_id')->isRequired()->cannotBeEmpty()->end()
                ->booleanNode('login_required')->isRequired()->end()
                ->scalarNode('login_form_template')->defaultValue('@PhotocreateMember/sign_in/_form.login.html.twig')->end()
                ->scalarNode('sign_up_link_template')->defaultValue('@PhotocreateMember/sign_up/_link.sign_up.html.twig')->end()
                ->arrayNode('api')->isRequired()->cannotBeEmpty()
                    ->children()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('version')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
