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
use ZfcRbac\Container\IsGrantedHelperFactory;
use ZfcRbac\Helper\IsGranted;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Service\AuthorizationServiceInterface;
use ZfcRbacTest\ContainerTrait;

/**
 * @covers \ZfcRbac\Container\IsGrantedViewHelperFactory
 */
class IsGrantedHelperFactoryTest extends TestCase
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
        $this->injectServiceInContainer(
            $this->container, AuthorizationService::class, $this->createMock(AuthorizationServiceInterface::class)
        );

        $factory = new IsGrantedHelperFactory();
        $isGranted = $factory($this->container->reveal());

        $this->assertInstanceOf(IsGranted::class, $isGranted);
    }
}