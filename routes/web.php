<?php
Route::middleware(['auth'])->prefix('external-apps/zoom')->group(function () {
    Route::post('/create-meeting', 'Controllers\ZoomController@createMeeting');
    Route::get('/meeting/{id}', 'Controllers\ZoomController@showMeeting');
    Route::post('/test-connection', 'Controllers\ZoomController@testConnection');
});
?>