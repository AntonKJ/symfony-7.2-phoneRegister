<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectUserDeprecationMessageTrait;
use Symfony\Bundle\SecurityBundle\DependencyInjection\MainConfiguration;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Security\Http\Authentication\ExposeSecurityLevel;

class MainConfigurationTest extends TestCase
{
    use ExpectUserDeprecationMessageTrait;

    /**
     * The minimal, required config needed to not have any required validation
     * issues.
     */
    protected static array $minimalConfig = [
        'providers' => [
            'stub' => [
                'id' => 'foo',
            ],
        ],
        'firewalls' => [
            'stub' => [],
        ],
    ];

    public function testNoConfigForProvider()
    {
        $config = [
            'providers' => [
                'stub' => [],
            ],
        ];

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);

        $this->expectException(InvalidConfigurationException::class);

        $processor->processConfiguration($configuration, [$config]);
    }

    public function testManyConfigForProvider()
    {
        $config = [
            'providers' => [
                'stub' => [
                    'id' => 'foo',
                    'chain' => [],
                ],
            ],
        ];

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);

        $this->expectException(InvalidConfigurationException::class);

        $processor->processConfiguration($configuration, [$config]);
    }

    public function testCsrfAliases()
    {
        $config = [
            'firewalls' => [
                'stub' => [
                    'logout' => [
                        'csrf_token_manager' => 'a_token_manager',
                        'csrf_token_id' => 'a_token_id',
                    ],
                ],
            ],
        ];
        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);
        $this->assertArrayHasKey('csrf_token_manager', $processedConfig['firewalls']['stub']['logout']);
        $this->assertEquals('a_token_manager', $processedConfig['firewalls']['stub']['logout']['csrf_token_manager']);
        $this->assertArrayHasKey('csrf_token_id', $processedConfig['firewalls']['stub']['logout']);
        $this->assertEquals('a_token_id', $processedConfig['firewalls']['stub']['logout']['csrf_token_id']);
    }

    public function testLogoutCsrf()
    {
        $config = [
            'firewalls' => [
                'custom_token_manager' => [
                    'logout' => [
                        'csrf_token_manager' => 'a_token_manager',
                        'csrf_token_id' => 'a_token_id',
                    ],
                ],
                'default_token_manager' => [
                    'logout' => [
                        'enable_csrf' => true,
                        'csrf_token_id' => 'a_token_id',
                    ],
                ],
                'disabled_csrf' => [
                    'logout' => [
                        'enable_csrf' => false,
                    ],
                ],
                'empty' => [
                    'logout' => true,
                ],
            ],
        ];
        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);

        $assertions = [
            'custom_token_manager' => [true, 'a_token_manager'],
            'default_token_manager' => [true, 'security.csrf.token_manager'],
            'disabled_csrf' => [false, null],
            'empty' => [false, null],
        ];
        foreach ($assertions as $firewallName => [$enabled, $tokenManager]) {
            $this->assertEquals($enabled, $processedConfig['firewalls'][$firewallName]['logout']['enable_csrf']);
            if ($tokenManager) {
                $this->assertEquals($tokenManager, $processedConfig['firewalls'][$firewallName]['logout']['csrf_token_manager']);
                $this->assertEquals('a_token_id', $processedConfig['firewalls'][$firewallName]['logout']['csrf_token_id']);
            } else {
                $this->assertArrayNotHasKey('csrf_token_manager', $processedConfig['firewalls'][$firewallName]['logout']);
            }
        }
    }

    public function testLogoutDeleteCookies()
    {
        $config = [
            'firewalls' => [
                'stub' => [
                    'logout' => [
                        'delete_cookies' => [
                            'my_cookie' => [
                                'path' => '/',
                                'domain' => 'example.org',
                                'secure' => true,
                                'samesite' => 'none',
                                'partitioned' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);
        $this->assertArrayHasKey('delete_cookies', $processedConfig['firewalls']['stub']['logout']);
        $deleteCookies = $processedConfig['firewalls']['stub']['logout']['delete_cookies'];
        $this->assertSame('/', $deleteCookies['my_cookie']['path']);
        $this->assertSame('example.org', $deleteCookies['my_cookie']['domain']);
        $this->assertTrue($deleteCookies['my_cookie']['secure']);
        $this->assertSame('none', $deleteCookies['my_cookie']['samesite']);
        $this->assertTrue($deleteCookies['my_cookie']['partitioned']);
    }

    public function testDefaultUserCheckers()
    {
        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [static::$minimalConfig]);

        $this->assertEquals('security.user_checker', $processedConfig['firewalls']['stub']['user_checker']);
    }

    public function testUserCheckers()
    {
        $config = [
            'firewalls' => [
                'stub' => [
                    'user_checker' => 'app.henk_checker',
                ],
            ],
        ];
        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);

        $this->assertEquals('app.henk_checker', $processedConfig['firewalls']['stub']['user_checker']);
    }

    public function testConfigMergeWithAccessDecisionManager()
    {
        $config = [
            'access_decision_manager' => [
                'strategy' => MainConfiguration::STRATEGY_UNANIMOUS,
            ],
        ];
        $config = array_merge(static::$minimalConfig, $config);

        $config2 = [];

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config, $config2]);

        $this->assertSame(MainConfiguration::STRATEGY_UNANIMOUS, $processedConfig['access_decision_manager']['strategy']);
    }

    public function testFirewalls()
    {
        $factory = $this->createMock(AuthenticatorFactoryInterface::class);
        $factory->expects($this->once())->method('addConfiguration');
        $factory->method('getKey')->willReturn('key');

        $configuration = new MainConfiguration(['stub' => $factory], []);
        $configuration->getConfigTreeBuilder();
    }

    /**
     * @dataProvider provideHideUserNotFoundData
     */
    public function testExposeSecurityErrors(array $config, ExposeSecurityLevel $expectedExposeSecurityErrors)
    {
        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);

        $this->assertEquals($expectedExposeSecurityErrors, $processedConfig['expose_security_errors']);
        $this->assertArrayNotHasKey('hide_user_not_found', $processedConfig);
    }

    public static function provideHideUserNotFoundData(): iterable
    {
        yield [[], ExposeSecurityLevel::None];
        yield [['expose_security_errors' => ExposeSecurityLevel::None], ExposeSecurityLevel::None];
        yield [['expose_security_errors' => ExposeSecurityLevel::AccountStatus], ExposeSecurityLevel::AccountStatus];
        yield [['expose_security_errors' => ExposeSecurityLevel::All], ExposeSecurityLevel::All];
    }

    /**
     * @dataProvider provideHideUserNotFoundLegacyData
     *
     * @group legacy
     */
    public function testExposeSecurityErrorsWithLegacyConfig(array $config, ExposeSecurityLevel $expectedExposeSecurityErrors, ?bool $expectedHideUserNotFound)
    {
        $this->expectUserDeprecationMessage('Since symfony/security-bundle 7.3: The "hide_user_not_found" option is deprecated and will be removed in 8.0. Use the "expose_security_errors" option instead.');

        $config = array_merge(static::$minimalConfig, $config);

        $processor = new Processor();
        $configuration = new MainConfiguration([], []);
        $processedConfig = $processor->processConfiguration($configuration, [$config]);

        $this->assertEquals($expectedExposeSecurityErrors, $processedConfig['expose_security_errors']);
        $this->assertEquals($expectedHideUserNotFound, $processedConfig['hide_user_not_found']);
    }

    public static function provideHideUserNotFoundLegacyData(): iterable
    {
        yield [['hide_user_not_found' => true], ExposeSecurityLevel::None, true];
        yield [['hide_user_not_found' => false], ExposeSecurityLevel::All, false];
    }
}
