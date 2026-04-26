<?php
require_once __DIR__ . '/config.php';
requireRole('pharmacist');
redirect_to('pharmacist/pharmacist_dashboard.php');
