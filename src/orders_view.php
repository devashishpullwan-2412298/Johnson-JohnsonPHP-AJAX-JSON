<?php
require_once __DIR__ . '/config.php';
requireRole('admin');
redirect_to('admin/admin_orders.php');
