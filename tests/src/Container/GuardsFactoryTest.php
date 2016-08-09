<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfcRbacTest\Container;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ZfcRbac\Container\GuardsFactory;
use ZfcRbac\Guard\ControllerGuard;
use ZfcRbac\Guard\ControllerPermissionsGuard;
use ZfcRbac\Guard\GuardPluginManager;
use ZfcRbac\Guard\RouteGuard;
use ZfcRbac\Guard\RoutePermissionsGuard;
use ZfcRbac\Options\ModuleOptions;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Service\RoleService;
use ZfcRbacTest\ContainerTrait;

/**
 * @covers \ZfcRbac\Container\GuardsFactory
 */
class GuardsFactoryTest extends TestCase
{

    use ContainerTrait;

    /**
     * @var ObjectProphecy|ContainerInterface
     */
    protected $container;

    public function setUp()
    {
        $this->container = $this->mockContainerInterface();
    }

    public function testFactory()
    {
        $moduleOptions = new ModuleOptions(
            [
                'guards' => [
                    RouteGuard::class                 => [
                        'admin/*' => 'role1'
                    ],
                    RoutePermissionsGuard::class      => [
                        'admin/post' => 'post.manage'
                    ],
                    ControllerGuard::class            => [
                        [
                            'controller' => 'MyController',
                            'actions'    => ['index', 'edit'],
                            'roles'      => ['role']
                        ]
                    ],
                    ControllerPermissionsGuard::class => [
                        [
                            'controller'  => 'PostController',
                            'actions'     => ['index', 'edit'],
                            'permissions' => ['post.read']
                        ]
                    ]
                ]
            ]
        );

        $pluginManager = new GuardPluginManager($this->container->reveal());

        $this->injectServiceInContainer($this->container, ModuleOptions::class, $moduleOptions);
        $this->injectServiceInContainer($this->container, GuardPluginManager::class, $pluginManager);
        $this->injectServiceInContainer($this->container, RoleService::class, $this->createMock(RoleService::class));
        $this->injectServiceInContainer(
            $this->container, AuthorizationService::class, $this->createMock(AuthorizationService::class)
        );

        $factory = new GuardsFactory();
        /** @var array $guards */
        $guards = $factory($this->container->reveal());

        $this->assertInternalType('array', $guards);

        $this->assertCount(4, $guards);
        $this->assertInstanceOf(RouteGuard::class, $guards[0]);
        $this->assertInstanceOf(RoutePermissionsGuard::class, $guards[1]);
        $this->assertInstanceOf(ControllerGuard::class, $guards[2]);
        $this->assertInstanceOf(ControllerPermissionsGuard::class, $guards[3]);
    }

    public function testReturnArrayIfNoConfig()
    {
        $moduleOptions = new ModuleOptions(
            [
                'guards' => []
            ]
        );

        $pluginManager = new GuardPluginManager($this->container->reveal());

        $this->injectServiceInContainer($this->container, ModuleOptions::class, $moduleOptions);
        $this->injectServiceInContainer($this->container, GuardPluginManager::class, $pluginManager);

        $factory = new GuardsFactory();
        $guards  = $factory($this->container->reveal());

        $this->assertInternalType('array', $guards);

        $this->assertEmpty($guards);
    }
}
