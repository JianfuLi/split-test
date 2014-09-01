<?php

namespace Gram\SplitTest\User;

/**
 * Class DefaultUserProvider
 *
 * @package Gram\SplitTest
 */
class DefaultUserProvider implements IUserProvider
{
    function getUserId()
    {
        return 0;
    }
}