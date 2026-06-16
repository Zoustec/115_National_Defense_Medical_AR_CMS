<?php

declare(strict_types=1);

namespace App\Constants;

class Pagination
{
    /**
     * Default number of items per page
     * Change this value to adjust default pagination size
     */
    public const PER_PAGE = 30;

    /**
     * Available per page options
     * Change these values to adjust available pagination options
     */
    public const OPTIONS = [20, 30, 50, 100];
}
