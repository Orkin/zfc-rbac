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

namespace ZfcRbac\Helper;

use ZfcRbac\Service\AuthorizationServiceInterface;

/**
 * Helper class that allows to test a permission
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class IsGrantedHelper
{
    /**
     * @var AuthorizationServiceInterface
     */
    private $authorizationService;

    /**
     * Constructor
     *
     * @param AuthorizationServiceInterface $authorizationService
     */
    public function __construct(AuthorizationServiceInterface $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * Check against the given permission
     *
     * @param  string $permission
     * @param  mixed  $context
     * @return bool
     */
    public function __invoke($permission, $context = null)
    {
        return $this->authorizationService->isGranted($permission, $context);
    }

    /**
     * Check against the given permission
     *
     * Proxies to __invoke().
     *
     * @param $permission
     * @param $context
     * @return mixed
     */
    public function isGranted($permission, $context = null)
    {
        return $this($permission, $context);
    }
}
