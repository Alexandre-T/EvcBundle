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

use Alexandre\EvcBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @coversDefaultClass \Alexandre\EvcBundle\DependencyInjection\Configuration
 *
 * @internal
 */
class ConfigurationTest extends TestCase
{
    /**
     * Variable to test.
     *
     * @var Configuration
     */
    private $configuration;

    /**
     * Setup the configuration before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration();
    }

    /**
     * Tear down to avoid memory leaks.
     */
    protected function tearDown(): void
    {
        $this->configuration = null;

        parent::tearDown();
    }

    /**
     * Test that an empty api_id is throwing exception.
     */
    public function testEmptyApiId(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('The path "alexandre_evc.api_id" cannot contain an empty value, but got "".');
        $node = $this->configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize([
            'api_id' => '', //empty
        ]);
        $node->finalize($normalizedConfig);
    }

    /**
     * Test that an empty password is throwing exception.
     */
    public function testEmptyPassword(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('The path "alexandre_evc.password" cannot contain an empty value, but got "".');
        $node = $this->configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize([
            'api_id' => 'foo',
            'username' => 'bar',
            'password' => '', //empty
        ]);
        $node->finalize($normalizedConfig);
    }

    /**
     * Test that an empty username is throwing exception.
     */
    public function testEmptyUsername(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('The path "alexandre_evc.username" cannot contain an empty value, but got "".');
        $node = $this->configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize([
            'api_id' => 'foo',
            'username' => '', //empty
        ]);
        $node->finalize($normalizedConfig);
    }

    /**
     * Test configuration.
     */
    public function testGetConfigTreeBuilder(): void
    {
        $actual = $expected = [
            'api_id' => 'foo',
            'password' => 'team',
            'username' => 'bar',
        ];
        $node = $this->configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize($actual);
        $finalizedConfig = $node->finalize($normalizedConfig);
        $this->assertEquals($expected, $finalizedConfig);
    }

    /**
     * Test configuration with wrong keys.
     */
    public function testGetConfigWithWrongKey(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('Unrecognized option "foo" under "alexandre_evc"');
        $node = $this->configuration->getConfigTreeBuilder()->buildTree();
        $normalizedConfig = $node->normalize([
            'foo' => 'bar',
        ]);
        $node->finalize($normalizedConfig);
    }

    /**
     * Test that the sample configuration file exists and is up to date.
     */
    public function testSampleConfigurationFile(): void
    {
        $expected = file_get_contents(__DIR__.'/../../Resources/doc/configuration_sample.yaml');
        $dumper = new YamlReferenceDumper();
        $actual = $dumper->dump($this->configuration);
        self::assertEquals($expected, $actual);
    }
}
