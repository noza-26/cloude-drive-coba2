<?php
// Proteksi akses langsung ke folder uploads
header("HTTP/1.0 403 Forbidden");
exit("Access denied. Direct access to upload directory is not allowed.");
?>