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

namespace Alexandre\EvcBundle\Tests;

use Alexandre\EvcBundle\AlexandreEvcBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Class BundleInitializationTest.
 *
 * @internal
 * @coversNothing
 */
class BundleInitializationTest extends BaseBundleTestCase
{
    /**
     * Setup public my services.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Make services public that have an idea that matches a regex
        $this->addCompilerPass(new PublicServicePass('|alexandre_evc|'));
    }

    /**
     * Test the bundle only with configuration file
     * It should failed because env variable is not defined.
     */
    public function testBundleWithConfiguration(): void
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/../Resources/doc/initial.yaml');

        // Boot the kernel as normal ...
        $this->bootKernel();

        self::assertTrue(true); //No exception has been thrown
    }

    /**
     * The bundle should crash properly if there is no configuration file.
     */
    public function testInitBundleWithoutConfiguration(): void
    {
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage('The child node "api_id" at path "alexandre_evc" must be configured.');

        // Boot the kernel.
        $this->bootKernel();
    }

    /**
     * Return the bundle class.
     *
     * @return string
     */
    protected function getBundleClass()
    {
        return AlexandreEvcBundle::class;
    }
}
