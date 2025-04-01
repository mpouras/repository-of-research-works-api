<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserLibraryController;
use App\Http\Controllers\User\UserNotificationController;
use App\Http\Controllers\VolumeController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('auth.login');
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/logout', 'logout')->name('auth.logout');
});

Route::controller(EmailVerificationController::class)->group(function () {
    Route::get('/email/verify/{id}/{hash}', 'verify')->name('verification.verify');
    Route::post('/email/resend', 'resendVerificationEmail')->name('verification.resend');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/user', 'show')->name('user');
    Route::post('/user/update-profile', 'update')->name('user.profile.update');
    Route::put('/user/change-password', 'changePassword')->name('user.changePassword');
    Route::put('/user/change-email', 'changeEmail')->name('user.changeEmail');
    Route::put('/user/deactivate', 'deactivate')->name('user.deactivate');
    Route::delete('/user/delete', 'destroy')->name('user.destroy');
});

Route::controller(AdminController::class)->group(function () {
    Route::get('/admin/users', 'index')->name('users.index');
    Route::get('/admin/users/{id}', 'show')->name('users.show');
    Route::get('/admin/dashboard', 'dashboard')->name('dashboard');
    Route::post('/admin/create-user', 'storeUser')->name('user.store');
    Route::put('/admin/update-user/{id}', 'updateUser')->name('user.update');
    Route::delete('/admin/delete-user/{id}', 'deleteUser')->name('user.delete');
});

Route::controller(UserNotificationController::class)->group(function () {
    Route::get('/user/notifications', 'index')->name('notifications.index');
    Route::put('/user/notifications/{id}/read', 'markAsRead')->name('notifications.read');
});


Route::controller(UserLibraryController::class)->group(function () {
    Route::get('/user-libraries', 'index')->name('user.library.index');
    Route::get('/user/library', 'show')->name('user.library.show');
    Route::post('/user/library', 'store')->name('user.library.store');
    Route::delete('/user/library/{id}', 'destroy')->name('user.library.destroy');
});

Route::controller(ScraperController::class)->group(function () {
   Route::prefix('scraper')->group(function () {
       Route::get('/publications/{scraper}', 'indexPublications')->name('scraper.publications.index');
       Route::post('/publications', 'storeManyPublications')->name('scraper.publications.store');
       Route::put('/publications', 'updateManyPublications')->name('scraper.publications.store');
       Route::post('/publications/{publicationId}/{volumeNumber}/{issueName}/articles', 'storeManyArticles')->name('scraper.articles.store');
   });
});

Route::controller(PublisherController::class)->group(function () {
    Route::get('/publishers', 'index')->name('publishers.index');
    Route::post('/publishers', 'store')->name('publishers.store');
    Route::get('/publishers/{id}', 'show')->name('publishers.show');
    Route::put('/publishers/{id}', 'update')->name('publishers.update');
    Route::delete('/publishers/{id}', 'destroy')->name('publishers.destroy');

    Route::get('/publishers/{id}/publications', 'showPublications')->name('publishers.publications');
});

Route::controller(PublicationController::class)->group(function () {
    Route::get('/publications', 'index')->name('publications.index');
    Route::post('/publications', 'store')->name('publications.store');
    Route::get('/publication/{id}', 'show')->name('publications.show');
    Route::put('/publications/{id}', 'update')->name('publications.update');
    Route::delete('/publications/{id}', 'destroy')->name('publications.destroy');

    Route::post('/publications/{id}/publishers/{action}', 'syncPublisher')->where('action', 'attach|detach')->name('publications.publishers.sync');
});

Route::controller(VolumeController::class)->group(function () {
    Route::prefix('publications/{publicationId}')->group(function () {
        Route::get('/volumes', 'index')->name('volumes.index');
        Route::post('/volumes', 'store')->name('volumes.store');
        Route::get('/{id}', 'show')->name('volumes.show');
        Route::put('/{id}', 'update')->name('volumes.update');
        Route::delete('/{id}', 'destroy')->name('volumes.destroy');
    });
});

Route::controller(IssueController::class)->group(function () {
    Route::prefix('publications/{publicationId}/{volumeNumber}')->group(function () {
        Route::get('issues','index')->name('issues.index');
        Route::get('{issueName}', 'show')->name('issues.show');
        Route::post('issues', 'store')->name('issues.store');
        Route::put('{issueName}', 'update')->name('issues.update');
        Route::delete('{issueName}','destroy')->name('issues.destroy');
    });
});

Route::controller(ArticleController::class)->group(function () {
    Route::prefix('publications/{publicationId}/{volumeNumber}/{issueName}/')->group(function () {
        Route::get('articles','indexIssueArticles')->name('issue.articles.index');
        Route::get('{id}','showIssueArticles')->name('issue.articles.show');
        Route::post('articles', 'storeIssueArticles')->name('issue.articles.store');
    });

    Route::get('articles', 'index')->name('articles.index');
    Route::get('articles/{id}', 'show')->name('articles.show');
    Route::put('articles/{id}', 'update')->name('articles.update');
    Route::delete('articles/{id}', 'destroy')->name('articles.destroy');

    Route::post('articles/{id}/authors/{action}', 'syncAuthor')->where('action', 'attach|detach')->name('article.authors.sync');
    Route::post('articles/{id}/keywords/{action}', 'syncKeyword')->where('action', 'attach|detach')->name('article.keywords.sync');
});

Route::controller(AuthorController::class)->group(function () {
    Route::get('/authors', 'index')->name('authors.index');
    Route::post('/authors', 'store')->name('authors.store');
    Route::get('/authors/{id}', 'show')->name('authors.show');
    Route::put('/authors/{id}', 'update')->name('authors.update');
    Route::delete('/authors/{id}', 'destroy')->name('authors.destroy');
});

Route::controller(KeywordController::class)->group(function () {
    Route::get('/keywords', 'index')->name('keywords.index');
    Route::post('/keywords', 'store')->name('keywords.store');
    Route::get('/keywords/{name}', 'show')->name('keywords.show');
    Route::put('/keywords/{id}', 'update')->name('keywords.update');
    Route::delete('/keywords/{name}', 'destroy')->name('keywords.destroy');
});

Route::controller(SearchController::class)->group(function () {
    Route::get('search', 'search')->name('search');
    Route::get('search-entities', 'searchEntities')->name('search.entities');
});
