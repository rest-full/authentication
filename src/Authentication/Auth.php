<?php

namespace Restfull\Authentication;

use Restfull\Container\Instances;
use Restfull\Error\Exceptions;

/**
 *
 */
class Auth
{

    /**
     * @var array
     */
    private $auth = [];

    /**
     * @var Sessions
     */
    private $session;

    /**
     * @var Cookies
     */
    private $cookie;

    /**
     * @throws Exceptions
     */
    public function __construct()
    {
        $instance = new Instances();
        $this->session = $instance->resolveClass(
            $instance->assemblyClassOrPath(
                "%s" . DS_REVERSE . 'Authentication' . DS_REVERSE . 'Sessions',
                [ROOT_NAMESPACE]
            )
        );
        $this->session->sessionExpired();
        $this->cookie = $instance->resolveClass(
            $instance->assemblyClassOrPath(
                "%s" . DS_REVERSE . 'Authentication' . DS_REVERSE . 'Cookies',
                [ROOT_NAMESPACE]
            )
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getData(string $key = ''): array
    {
        if (!empty($key)) {
            $this->session->key = $key;
            return $this->session->get();
        }
        if (isset($this->auth)) {
            return $this->auth;
        }
        return [];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getSession(string $key = 'user'): array
    {
        $this->session->keys($key);
        if ($this->session->validety()) {
            return $this->session->get();
        }
        return [];
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    public function check(string $key = null): bool
    {
        if (!is_null($key)) {
            $this->session->keys($key);
            return $this->session->validety();
        }
        $this->setAuth('user');
        return empty($this->auth) ? false : true;
    }

    /**
     * @param bool $count
     *
     * @return array
     */
    public function getCookie(bool $count = false): array
    {
        if ($count) {
            return $this->cookie->keys();
        }
        if (isset($this->session->keys('cookie')->get()['key'])) {
            return $this->cookie->keys(
                $this->session->keys('cookie')->get()['key']
            )->get();
        }
        return [];
    }

    /**
     * @param string $key
     * @param string $object
     *
     * @return $this
     */
    public function key(string $key, string $object): Auth
    {
        if ($object == 'cookie') {
            if (in_array($key, $this->cookie->keys()) !== false) {
                $this->cookie->key = $key;
            }
        } else {
            if (in_array($key, $this->session->keys()) !== false) {
                $this->session->key = $key;
            }
        }
        return $this;
    }

    /**
     * @param string $identify
     *
     * @return array
     */
    public function getAuth(string $identify): array
    {
        return $this->session->keys($identify)->get();
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setAuth(string $key): Auth
    {
        if ($this->validCookie()) {
            $this->cookie->keys($key);
            if ($this->cookie->check()) {
                $session = $this->cookie->get();
                if ($session instanceof Sessions) {
                    $this->session = $session;
                }
                $this->auth = $this->getAuth($key);
                return $this;
            }
        }
        $this->auth = $this->getAuth($key);
        return $this;
    }

    /**
     * @return bool
     */
    public function validCookie(): bool
    {
        if (isset($this->session->keys('cookie')->get()['valid'])) {
            return $this->session->keys('cookie')->get()['valid'] !== true;
        }
        return false;
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function counts(string $key): int
    {
        if (isset($this->auth)) {
            return count($this->auth[$key]);
        }
        return 0;
    }

    /**
     * @param string $key
     * @param array $value
     * @param array|false[] $cookie
     *
     * @return $this
     */
    public function write(string $key, array $value, array $cookie = ['valid' => false]): Auth
    {
        $this->session->keys($key);
        $this->session->write($value);
        if ($cookie['valid']) {
            $this->session->changeTimeExpire($cookie['time']);
            if (!isset($cookie['value'])) {
                $cookie['value'] = $this->session;
            }
            $this->cookie->keys($cookie['key']);
            $this->cookie->write($cookie['value'], $cookie['time']);
        }
        $this->session->keys('cookie');
        $this->session->write(['valid' => $cookie['valid']]);
        return $this;
    }

    /**
     * @param string $session
     * @param array $cookie
     *
     * @return $this
     */
    public function destroy(string $session, array $cookie = []): Auth
    {
        $this->session->keys($session);
        $this->session->destroy();
        if ($cookie['valid']) {
            unset($cookie['valid']);
            $this->cookie->keys($cookie['key']);
            unset($cookie['key']);
            $this->cookie->destroy();
        }
        return $this;
    }

    /**
     * @param int|null $code
     *
     * @return mixed
     */
    public function twoSteps(int $code = null)
    {
        if (isset($code)) {
            return $this->twoSteps->validateCode($code);
        }
        return $this->twoSteps->getQrcode();
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->session->key);
        return $this;
    }

}
