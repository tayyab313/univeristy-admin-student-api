<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\staffController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('job')->group(function(){
    Route::controller(JobController::class)->group(function(){
        Route::post('/store','save')->name('job.save');
        Route::get('/show','index')->name('job.show');
    });
});
// student card api

Route::prefix('student')->group(function(){
    Route::controller(StudentController::class)->group(function(){
        Route::post('/card-create', 'cardCreate')->name('student.cardCreate');
        Route::get('/get-card-detail/{id}', 'cardDetail')->name('student.cardDetail');
    });
});


Route::prefix('course')->group(function(){
    Route::controller(CourseController::class)->group(function(){
        Route::post('/create',  'store'); // Admin creates course
        Route::get('/list',  'index'); // Students view courses
    });
});


Route::post('/login', [AuthController::class, 'login']);

// Routes under 'upload' prefix protected by Sanctum middleware
Route::middleware(['auth:sanctum'])->group(function () {
Route::prefix('course-registeration')->group(function(){
    Route::controller(CourseRegistrationController::class)->group(function(){
        Route::post('/{course_id}/register',  'store'); //student add course
        Route::post('/{course}/approve',  'approve'); //admin approved course
        Route::get('/pending-approvals', 'pendingApprovals'); // Admin views pending approvals
        Route::get('/courses/{student_id}/approved', 'approvedCourses'); // Student views approved courses
    });
});
Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('upload')->group(function(){
        Route::controller(FileUploadController::class)->group(function(){
            Route::post('/grades', 'uploadGrades');
            Route::post('/fees', 'uploadFees');
            Route::get('/fetch-fees/{filename}',  'fetchFees');
        });
    });
    Route::prefix('admin/student')->controller(StudentController::class)->group(function(){
        Route::post('/create', 'store')->name('admin.student.create');
        Route::post('/update/{id}', 'update')->name('admin.student.update');
        Route::get('/show/{id}', 'show')->name('admin.student.show');
        Route::get('/list',  'index'); // Students view courses
        Route::delete('/delete/{id}',  'destroy'); // Students view courses

    });
    Route::prefix('admin/staff')->controller(staffController::class)->group(function(){
        Route::post('/create', 'store')->name('admin.staff.create');
        Route::post('/update/{id}', 'update')->name('admin.staff.update');
        Route::get('/show/{id}', 'show')->name('admin.staff.show');
        Route::get('/list',  'index'); // staffs view courses
        Route::delete('/delete/{id}',  'destroy');
        Route::post('/{staff}/assign-courses', 'assignCourses');
        Route::get('/{staff}/courses','getCourses');

    });
    Route::prefix('student')->controller(AuthController::class)->group(function(){
        Route::post('/change-password', 'updatePassword')->name('student.updatePassword');
    });
    Route::prefix('profile')->controller(AuthController::class)->group(function(){
        Route::get('/edit/{id}', 'getProfileDetail')->name('profile.getProfileDetail');
        Route::post('/update/{id}', 'updateProfileDetail')->name('profile.updateProfileDetail');
    });
    Route::prefix('admin/chat')->controller(MessageController::class)->group(function(){
        Route::get('/students', 'listStudents');
    });
    Route::post('send/messages', [MessageController::class, 'sendMessage']);
    Route::get('get/messages/{user}', [MessageController::class, 'getMessages']);
    Route::get('get/admin/detail', [MessageController::class, 'getAdmin']);
    Route::controller(FileUploadController::class)->group(function(){
        Route::get('/fetch-grade/student/{user}',  'fetchGradebyStudent');
    });
    // attendance staff to student api
    Route::prefix('staff')->controller(staffController::class)->group(function(){
        Route::get('/assign-courses', 'getAssignCourses')->name('staff.getAssignCourses');
        Route::get('/getCourseStudent/{course}', 'getCourseStudent')->name('staff.getCourseStudent');
        Route::post('/mark-student-attendance','markAttendance')->name('staff.markAttendance');
    });
    Route::prefix('book')->controller(BookController::class)->group(function(){
        Route::get('/get', 'index');
        Route::get('/bookDetail/{id}', 'show');
        Route::post('/store', 'store');
        Route::post('/edit/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });
});
