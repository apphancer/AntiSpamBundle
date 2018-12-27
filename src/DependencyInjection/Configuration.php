<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\AntiSpamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('core23_antispam');

        // Keep compatibility with symfony/config < 4.2
        if (!\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root('core23_antispam');
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }
        \assert($rootNode instanceof ArrayNodeDefinition);

        $this->addTwigSection($rootNode);
        $this->addTimeSection($rootNode);
        $this->addHoneypotSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTwigSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('mail')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('css_class')->defaultNull()->end()
                                ->arrayNode('dot_text')
                                     ->useAttributeAsKey('id')
                                     ->requiresAtLeastOneElement()
                                     ->defaultValue(['[DOT]', '(DOT)', '[.]'])
                                     ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('at_text')
                                     ->useAttributeAsKey('id')
                                     ->requiresAtLeastOneElement()
                                     ->defaultValue(['[AT]', '(AT)', '[ÄT]'])
                                     ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTimeSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('time')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('min')->defaultValue(5)->end()
                        ->integerNode('max')->defaultValue(3600)->end()
                        ->booleanNode('global')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addHoneypotSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('honeypot')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('field')->defaultValue('email_address')->end()
                        ->scalarNode('class')
                            ->defaultValue('hidden')
                            ->info('CSS class to hide the honeypot. If not set a "style:hidden" attribute ist set.')
                        ->end()
                        ->booleanNode('global')->defaultFalse()->end()
                        ->scalarNode('provider')->defaultValue('core23_antispam.provider.session')->end()
                    ->end()
                ->end()
            ->end();
    }
}
