<?php
if (empty($configuration['ZOOM_ACCOUNT_ID'])) {
    throw new Exception('ZOOM_ACCOUNT_ID is required');
}

if (empty($configuration['ZOOM_CLIENT_ID'])) {
    throw new Exception('ZOOM_CLIENT_ID is required');
}

if (empty($configuration['ZOOM_CLIENT_SECRET'])) {
    throw new Exception('ZOOM_CLIENT_SECRET is required');
}

echo "✓ Configuration validation passed\n";
?>