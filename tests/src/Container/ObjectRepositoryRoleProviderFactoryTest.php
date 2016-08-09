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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceManager;
use ZfcRbac\Exception\RuntimeException;
use ZfcRbac\Role\ObjectRepositoryRoleProvider;
use ZfcRbac\Role\RoleProviderPluginManager;
use ZfcRbacTest\ContainerTrait;

/**
 * @covers \ZfcRbac\Container\ObjectRepositoryRoleProviderFactory
 */
class ObjectRepositoryRoleProviderFactoryTest extends TestCase
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

    public function testFactoryUsingObjectRepository()
    {
        $pluginManager = new RoleProviderPluginManager($this->container->reveal());

        $options = [
            'role_name_property' => 'name',
            'object_repository'  => 'RoleObjectRepository'
        ];

        $this->injectServiceInContainer(
            $this->container, 'RoleObjectRepository', $this->createMock(ObjectRepository::class)
        );

        $roleProvider = $pluginManager->get(ObjectRepositoryRoleProvider::class, $options);
        $this->assertInstanceOf(ObjectRepositoryRoleProvider::class, $roleProvider);
    }

    public function testFactoryUsingObjectManager()
    {
        $pluginManager = new RoleProviderPluginManager($this->container->reveal());

        $options = [
            'role_name_property' => 'name',
            'object_manager'     => 'ObjectManager',
            'class_name'         => 'Role'
        ];

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->once())
                      ->method('getRepository')
                      ->with($options['class_name'])
                      ->will($this->returnValue($this->createMock(ObjectRepository::class)));

        $this->injectServiceInContainer($this->container, 'ObjectManager', $objectManager);

        $roleProvider = $pluginManager->get(ObjectRepositoryRoleProvider::class, $options);
        $this->assertInstanceOf(ObjectRepositoryRoleProvider::class, $roleProvider);
    }

    /**
     * This is required due to the fact that the ServiceManager catches ALL exceptions and throws it's own...
     */
    public function testThrowExceptionIfNoRoleNamePropertyIsSet()
    {
        try {
            $pluginManager  = new RoleProviderPluginManager($this->container->reveal());

            $pluginManager->get(ObjectRepositoryRoleProvider::class, []);
        } catch (ServiceNotCreatedException $smException) {
            while ($e = $smException->getPrevious()) {
                if ($e instanceof RuntimeException) {
                    return true;
                }
            }
        }

        $this->fail(
            'ZfcRbac\Factory\ObjectRepositoryRoleProviderFactory::createService() :: '
            . 'ZfcRbac\Exception\RuntimeException was not found in the previous Exceptions'
        );
    }

    /**
     * This is required due to the fact that the ServiceManager catches ALL exceptions and throws it's own...
     */
    public function testThrowExceptionIfNoObjectManagerNorObjectRepositoryIsSet()
    {
        try {
            $pluginManager  = new RoleProviderPluginManager($this->container->reveal());

            $pluginManager->get(
                'ZfcRbac\Role\ObjectRepositoryRoleProvider', [
                    'role_name_property' => 'name'
                ]
            );
        } catch (ServiceNotCreatedException $smException) {

            while ($e = $smException->getPrevious()) {
                if ($e instanceof RuntimeException) {
                    return true;
                }
            }
        }

        $this->fail(
            'ZfcRbac\Factory\ObjectRepositoryRoleProviderFactory::createService() :: '
            . 'ZfcRbac\Exception\RuntimeException was not found in the previous Exceptions'
        );
    }
}