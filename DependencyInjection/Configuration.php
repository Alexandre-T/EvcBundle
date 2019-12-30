<?php
/**
 * This file is part of the Evc Bundle.
 *
 * PHP version 7.1|7.2|7.3|7.4
 * Symfony version 4.4|5.0|5.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2020 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace Alexandre\EvcBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        if (1 === version_compare('4.0.0', Kernel::VERSION)) {
            //Version 3.4
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('alexandre_evc');
        } else {
            $treeBuilder = new TreeBuilder('alexandre_evc');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
            ->scalarNode('api_id')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('username')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('password')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
