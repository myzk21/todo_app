<?php
namespace App\Enums;

enum TodoTimerStatus: string
{
    case Start = 'start';
    case Stop = 'stop';
    case Finish = 'finish';
}
