<?php

namespace App\Enum;

enum MessageEnum: string
{
    const ERROR_EXECPTION = 'Unexpected error during %s | %s';

    const FAILED_MESSAGE = 'Failed to %s | %s';

    const SUCCESS_MESSAGE = 'Successfully %s';
}