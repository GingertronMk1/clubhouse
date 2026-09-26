<?php

namespace App;

enum Permission: string
{
    case SUPERADMIN = 'superadmin';
    case CLUB_ADMIN = 'club_admin';
}
