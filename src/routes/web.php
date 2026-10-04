<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|   web/site.php         -> halaman publik (guest boleh akses)
|   web/auth.php         -> login, register, logout
|   web/member.php       -> aksi member yang sudah login
|   web/organizer.php    -> panel organizer (klub, aktivitas, kompetisi)
|   web/venue-owner.php  -> panel pengelola venue
|   web/admin.php        -> panel admin
*/

require __DIR__.'/web/site.php';
require __DIR__.'/web/auth.php';
require __DIR__.'/web/member.php';
require __DIR__.'/web/organizer.php';
require __DIR__.'/web/venue-owner.php';
require __DIR__.'/web/admin.php';
