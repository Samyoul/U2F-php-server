<?php
/**
 * Created by IntelliJ IDEA.
 * User: samuel
 * Date: 09/12/2016
 * Time: 15:14
 */

namespace Samyoul;


class SignRequest
{
    /** Protocol version */
    protected $version = U2F::VERSION;

    /** Authentication challenge */
    protected $challenge;

    /** Key handle of a registered authenticator */
    protected $keyHandle;

    /** Application id */
    protected $appId;

    public function __construct(array $parameters)
    {
        $this->challenge = $parameters['challenge'];
        $this->keyHandle = $parameters['keyHandle'];
        $this->appId = $parameters['appId'];
    }

    /**
     * @return string
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function challenge()
    {
        return $this->challenge;
    }

    /**
     * @return string
     */
    public function keyHandle()
    {
        return $this->keyHandle;
    }

    /**
     * @return string
     */
    public function appId()
    {
        return $this->appId;
    }

}