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

namespace Alexandre\EvcBundle\Tests\DependencyInjection;

use Alexandre\EvcBundle\DependencyInjection\AlexandreEvcExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @coversDefaultClass
 *
 * @internal
 */
class AlexandreEvcExtensionTest extends TestCase
{
    /**
     * @var AlexandreEvcExtension
     */
    private $evcExtension;

    /**
     * Setup test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->evcExtension = $this->getExtension();
    }

    /**
     * Tear down and avoid memory leaks.
     */
    protected function tearDown(): void
    {
        $this->evcExtension = null;

        parent::tearDown();
    }

    /**
     * We test a valid configuration.
     */
    public function testEmptyConfiguration(): void
    {
        $container = $this->getContainer();

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('The child node "api_id" at path "alexandre_evc" must be configured.');

        $this->evcExtension->load([
            'alexandre_evc' => [],
        ],
            $container
        );
    }

    /**
     * We test a valid configuration.
     */
    public function testNonExistentConfiguration(): void
    {
        $container = $this->getContainer();
        self::expectException(ServiceNotFoundException::class);
        self::expectExceptionMessage('You have requested a non-existent service "alexandre_evc"');
        $container->getDefinition('alexandre_evc');
    }

    /**
     * We test a valid configuration.
     */
    public function testNonValidConfiguration(): void
    {
        $container = $this->getContainer();

        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('Unrecognized option "url" under "alexandre_evc"');

        $this->evcExtension->load([
            'alexandre_evc' => [
                'url' => 'https://example.org', // this shall throw an error
                'api_id' => 'foo_api',
                'password' => 'evc',
                'username' => 'alexandre',
            ],
        ],
            $container
        );
    }

    /**
     * We test a valid configuration.
     */
    public function testValidConfiguration(): void
    {
        $container = $this->getContainer();
        $this->evcExtension->load([
            'alexandre_evc' => [
                'api_id' => 'foo_api',
                'password' => 'evc',
                'username' => 'alexandre',
            ],
        ],
            $container
        );

        $expected = [
            '$url' => 'https://evc.de/services/api_resellercredits.asp',
            '$api' => 'foo_api',
            '$username' => 'alexandre',
            '$password' => 'evc',
        ];

        self::assertEquals($expected, $container->getDefinition('alexandre_evc')->getArguments());
    }

    /**
     * Return the extension.
     *
     * @return AlexandreEvcExtension
     */
    protected function getExtension()
    {
        return new AlexandreEvcExtension();
    }

    /**
     * Return the container builder.
     *
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return new ContainerBuilder();
    }
}
