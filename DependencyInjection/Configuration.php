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
 * @license   MIT https://spdx.org/licenses/MIT.html
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
     * @SuppressWarnings(PHPMD.ElseExpression)
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
            ->example('api_version_1')
            ->info('This is the API version, a string code')
            ->end()
            ->scalarNode('username')
            ->example('33333')
            ->info('This is your account number')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('password')
            ->example("'%env(EVC_PASSWORD)%'")
            ->info('This is the password of your api account, this is NOT the password to login on evc.de website.'
                        ."\nYou shall use an ENV variable to avoid store your password on cloud.")
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
