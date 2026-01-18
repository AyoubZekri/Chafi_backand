<?php

use App\Http\Controllers\AppLication\Differents;
use App\Http\Controllers\AppLication\Institutions;
use App\Http\Controllers\AppLication\IsRead;
use App\Http\Controllers\AppLication\NotificationUser\Delete as NotificationUserDelete;
use App\Http\Controllers\AppLication\NotificationUser\Show as NotificationUserShow;
use App\Http\Controllers\AppLication\TaxsAndApps;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuth;
use App\Http\Controllers\Dashboard\activitys\Add as ActivitysAdd;
use App\Http\Controllers\Dashboard\activitys\Delete as ActivitysDelete;
use App\Http\Controllers\Dashboard\activitys\Edit as ActivitysEdit;
use App\Http\Controllers\Dashboard\activitys\Show as ActivitysShow;
use App\Http\Controllers\Dashboard\appointments\Add as AppointmentsAdd;
use App\Http\Controllers\Dashboard\appointments\Delete as AppointmentsDelete;
use App\Http\Controllers\Dashboard\appointments\Edit as AppointmentsEdit;
use App\Http\Controllers\Dashboard\appointments\Show as AppointmentsShow;
use App\Http\Controllers\Dashboard\Auth\Login;
use App\Http\Controllers\Dashboard\Auth\Rejester;
use App\Http\Controllers\Dashboard\Auth\ResetPassword;
use App\Http\Controllers\Dashboard\Categories\Add as CategoriesAdd;
use App\Http\Controllers\Dashboard\Categories\Delete as CategoriesDelete;
use App\Http\Controllers\Dashboard\Categories\Edit as CategoriesEdit;
use App\Http\Controllers\Dashboard\Categories\Show as CategoriesShow;
use App\Http\Controllers\Dashboard\Differents\Add as DifferentsAdd;
use App\Http\Controllers\Dashboard\Differents\Delete as DifferentsDelete;
use App\Http\Controllers\Dashboard\Differents\Edit as DifferentsEdit;
use App\Http\Controllers\Dashboard\Differents\Show as DifferentsShow;
use App\Http\Controllers\Dashboard\institution\Add;
use App\Http\Controllers\Dashboard\institution\Delete;
use App\Http\Controllers\Dashboard\institution\Edit;
use App\Http\Controllers\Dashboard\institution\Show;
use App\Http\Controllers\Dashboard\Law\Add as LawAdd;
use App\Http\Controllers\Dashboard\Law\Delete as LawDelete;
use App\Http\Controllers\Dashboard\Law\Edit as LawEdit;
use App\Http\Controllers\Dashboard\Law\Show as LawShow;
use App\Http\Controllers\Dashboard\Mypaths\Add as MypathsAdd;
use App\Http\Controllers\Dashboard\Mypaths\Delete as MypathsDelete;
use App\Http\Controllers\Dashboard\Mypaths\Edit as MypathsEdit;
use App\Http\Controllers\Dashboard\Mypaths\Show as MypathsShow;
use App\Http\Controllers\Dashboard\Nataire_activitys\Add as Nataire_activitysAdd;
use App\Http\Controllers\Dashboard\Nataire_activitys\Delete as Nataire_activitysDelete;
use App\Http\Controllers\Dashboard\Nataire_activitys\Edit as Nataire_activitysEdit;
use App\Http\Controllers\Dashboard\Nataire_activitys\Show as Nataire_activitysShow;
use App\Http\Controllers\Dashboard\Notification\Add as NotificationAdd;
use App\Http\Controllers\Dashboard\Notification\Delete as NotificationDelete;
use App\Http\Controllers\Dashboard\Notification\Edit as NotificationEdit;
use App\Http\Controllers\Dashboard\Notification\Show as NotificationShow;
use App\Http\Controllers\Dashboard\Posts\Add as PostsAdd;
use App\Http\Controllers\Dashboard\Posts\Delete as PostsDelete;
use App\Http\Controllers\Dashboard\Posts\Edit as PostsEdit;
use App\Http\Controllers\Dashboard\Posts\Show as PostsShow;
use App\Http\Controllers\Dashboard\taxs_and_apps\Add as Taxs_and_appsAdd;
use App\Http\Controllers\Dashboard\taxs_and_apps\Delete as Taxs_and_appsDelete;
use App\Http\Controllers\Dashboard\taxs_and_apps\Edit as Taxs_and_appsEdit;
use App\Http\Controllers\Dashboard\taxs_and_apps\Shwo;

Route::post('/google-login', [GoogleAuth::class, 'GoogleLogin']);
Route::post('/login', [Login::class, 'login']);
Route::post('/Register', [Rejester::class, 'Register']);



Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/update', [GoogleAuth::class, 'update']);
    Route::post('/logout', [GoogleAuth::class, 'logout']);
    Route::delete('/user/delete', [GoogleAuth::class, 'destroy']);
    Route::post('/admin/ResetPassword', [ResetPassword::class, 'ResetPassword']);

    Route::post('/institution/add', [Add::class, 'addinstitution']);
    Route::post('/institution/Edit', [Edit::class, 'Editinstitution']);
    Route::post('/institution/Show', [Show::class, 'show']);
    Route::post('/institution/Delete', [Delete::class, 'delete']);

    Route::post('/Law/add', [LawAdd::class, 'addLaw']);
    Route::post('/Law/Edit', [LawEdit::class, 'editLaw']);
    Route::get('/Law/Show', [LawShow::class, 'show']);
    Route::post('/Law/Delete', [LawDelete::class, 'delete']);

    Route::post('/Category/add', [CategoriesAdd::class, 'addCategories']);
    Route::post('/Category/Edit', [CategoriesEdit::class, 'editCategories']);
    Route::post('/Category/Show', [CategoriesShow::class, 'show']);
    Route::post('/Category/Delete', [CategoriesDelete::class, 'delete']);

    Route::post('/TaxAndApp/add', [Taxs_and_appsAdd::class, 'addtaxs_and_apps']);
    Route::post('/TaxAndApp/Edit', [Taxs_and_appsEdit::class, 'EditTaxs_and_apps']);
    Route::post('/TaxAndApp/Show', [Shwo::class, 'show']);
    Route::post('/TaxAndApp/Delete', [Taxs_and_appsDelete::class, 'delete']);

    Route::post('/Different/add', [DifferentsAdd::class, 'addDifferents']);
    Route::post('/Different/Edit', [DifferentsEdit::class, 'EditDifferents']);
    Route::post('/Different/Show', [DifferentsShow::class, 'show']);
    Route::post('/Different/Delete', [DifferentsDelete::class, 'delete']);

    Route::post('/Appointments/add', [AppointmentsAdd::class, 'addappointments']);
    Route::post('/Appointments/Edit', [AppointmentsEdit::class, 'Editappointments']);
    Route::post('/Appointments/Show', [AppointmentsShow::class, 'show']);
    Route::post('/Appointments/Delete', [AppointmentsDelete::class, 'delete']);

    Route::post('/Activitys/add', [ActivitysAdd::class, 'addActivitys']);
    Route::post('/Activitys/Edit', [ActivitysEdit::class, 'EditActivitys']);
    Route::post('/Activitys/Show', [ActivitysShow::class, 'show']);
    Route::post('/Activitys/Delete', [ActivitysDelete::class, 'delete']);

    Route::post('/NataireActivitys/add', [Nataire_activitysAdd::class, 'addNataireActivity']);
    Route::post('/NataireActivitys/Edit', [Nataire_activitysEdit::class, 'EditNataireActivity']);
    Route::post('/NataireActivitys/Show', [Nataire_activitysShow::class, 'show']);
    Route::post('/NataireActivitys/Delete', [Nataire_activitysDelete::class, 'delete']);

    Route::post('/Mypath/add', [MypathsAdd::class, 'addMypath']);
    Route::post('/Mypath/Edit', [MypathsEdit::class, 'EditMypath']);
    Route::post('/Mypath/Show', [MypathsShow::class, 'show']);
    Route::post('/Mypath/Delete', [MypathsDelete::class, 'delete']);

    Route::post('/Post/add', [PostsAdd::class, 'addPost']);
    Route::post('/Post/Edit', [PostsEdit::class, 'EditPost']);
    Route::post('/Post/Show', [PostsShow::class, 'show']);
    Route::post('/Post/Delete', [PostsDelete::class, 'delete']);

    Route::post('/Notification/add', [NotificationAdd::class, 'addNotification']);
    Route::post('/Notification/Edit', [NotificationEdit::class, 'EditNotification']);
    Route::post('/Notification/Show', [NotificationShow::class, 'show']);
    Route::post('/Notification/Delete', [NotificationDelete::class, 'delete']);


    //user

    Route::post('/NotificationUser/Show', [NotificationUserShow::class, 'show']);
    Route::post('/NotificationUser/Delete', [NotificationUserDelete::class, 'delete']);


    Route::post('/isread/{table}', [IsRead::class, 'IsRead']);


    Route::post('/user/Different/Show', [Differents::class, 'show']);
    Route::post('/user/Institution/Show', [Institutions::class, 'show']);
    Route::post('/user/TaxsAndApps/Show', [TaxsAndApps::class, 'show']);


});
