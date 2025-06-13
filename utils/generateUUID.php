<?php
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        random_int(0, 0xFFFF), random_int(0, 0xFFFF),
        random_int(0, 0xFFFF),
        random_int(0, 0xFFF) | 0x4000,   // Version 4 UUID
        random_int(0, 0x3FFF) | 0x8000,  // Variant 1
        random_int(0, 0xFFFF), random_int(0, 0xFFFF), random_int(0, 0xFFFF)
    );
}
