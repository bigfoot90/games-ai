<?php

namespace Games\Tris\Bot;

enum Feedback: int
{
    case POSITIVE = 1;
    case NEGATIVE = -1;
    case NEUTRAL = 0;
}