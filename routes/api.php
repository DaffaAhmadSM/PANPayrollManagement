<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Menu\UserMenuController;
use App\Http\Controllers\Position\GradeControler;
use App\Http\Controllers\Position\PositionControler;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Competency\JobTaskController;
use App\Http\Controllers\leave\LeaveRequestController;
use App\Http\Controllers\Company\CompanyInfoController;
use App\Http\Controllers\Competency\JobSkillController;
use App\Http\Controllers\Employee\EmploymentController;
use App\Http\Controllers\leave\LeaveCategoryController;
use App\Http\Controllers\AppSetting\AppSettingController;
use App\Http\Controllers\Customer\CustomerRateController;
use App\Http\Controllers\leave\LeaveAdjustmentController;
use App\Http\Controllers\WorkingHour\WorkingHourController;
use App\Http\Controllers\Calendar\CalendarHolidayController;
use App\Http\Controllers\Employee\EmployeeAddressController;
use App\Http\Controllers\Competency\EducationLevelController;
use App\Http\Controllers\Customer\CustomerContractController;
use App\Http\Controllers\Employee\EmployeeCustomerController;
use App\Http\Controllers\GeneralSetup\GeneralSetupController;
use App\Http\Controllers\Competency\CertificateTypeController;
use App\Http\Controllers\UnitOfMeasure\UnitOfMeasureController;
use App\Http\Controllers\Competency\JobResponsibilityController;
use App\Http\Controllers\NumberSequence\NumberSequenceController;
use App\Http\Controllers\Customer\CustomerCalendarHolidayController;
use App\Http\Controllers\EmployeeCompetencies\EmployeeSkillController;
use App\Http\Controllers\Competency\CertificateClassificationController;
use App\Http\Controllers\Calculation\MultiplicationCalculationController;
use App\Http\Controllers\EmployeeCompetencies\EmployeeEducationController;
use App\Http\Controllers\Calculation\OvertimeMultiplicationSetupController;
use App\Http\Controllers\Classification\ClassificationOfTaxPayerController;
use App\Http\Controllers\Employee\EmploymentTypeController;
use App\Http\Controllers\EmployeeCompetencies\EmployeeCertificateController;
use App\Http\Controllers\EmployeeCompetencies\EmployeeProfessionalExperienceController;
use App\Http\Controllers\EmployeeCompetencies\EmployeeProjectExperienceController;
use App\Http\Controllers\ImportTimeSheetController;
use App\Http\Controllers\LeaveHistoryController;
use App\Http\Controllers\TempTimesheetLineController;
use App\Http\Controllers\WorkingHour\WorkingHoursDetailController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('menu', [MenuController::class, 'getMenu']);
    Route::get('menu/all/{user_id}', [MenuController::class, 'getAllMenu']);
    Route::get('menu-user-permission/menu/{id}', [UserMenuController::class, 'UserMenuPermission']);

    Route::post('update-menu', [UserMenuController::class, 'UpdateUserMenu']);

    Route::group(['prefix' => 'company'], function () {
        Route::post('create', [CompanyInfoController::class, 'create']);
        Route::get('list', [CompanyInfoController::class, 'list']);
        Route::get('detail/{id}', [CompanyInfoController::class, 'detail']);
        Route::post('update/{id}', [CompanyInfoController::class, 'update']);
        Route::post('delete/{id}', [CompanyInfoController::class, 'delete']);
        Route::post('search', [CompanyInfoController::class, 'search']);
    });

    Route::group(['prefix' => 'number-sequence'], function () {
        Route::post('create', [NumberSequenceController::class, 'create']);
        Route::get('list', [NumberSequenceController::class, 'list']);
        Route::get('all', [NumberSequenceController::class, 'getAll']);
        Route::get('detail/{id}', [NumberSequenceController::class, 'detail']);
        Route::post('update/{id}', [NumberSequenceController::class, 'update']);
        Route::post('delete/{id}', [NumberSequenceController::class, 'delete']);
        Route::post('search', [NumberSequenceController::class, 'search']);
    });

    Route::group(['prefix' => 'unit-of-measure'], function () {
        Route::post('create', [UnitOfMeasureController::class, 'create']);
        Route::get('all', [UnitOfMeasureController::class, 'list']);
        Route::get('detail/{id}', [UnitOfMeasureController::class, 'detail']);
        Route::post('update/{id}', [UnitOfMeasureController::class, 'update']);
        Route::post('delete/{id}', [UnitOfMeasureController::class, 'delete']);
        Route::post('search', [UnitOfMeasureController::class, 'search']);
    });

    Route::group(['prefix' => 'multiplication-calculation'], function () {
        Route::post('create', [MultiplicationCalculationController::class, 'create']);
        Route::get('all', [MultiplicationCalculationController::class, 'getList']);
        Route::get('detail/{id}', [MultiplicationCalculationController::class, 'detail']);
        Route::post('update/{id}', [MultiplicationCalculationController::class, 'update']);
        Route::post('delete/{id}', [MultiplicationCalculationController::class, 'delete']);
        Route::post('search', [MultiplicationCalculationController::class, 'search']);
    });

    Route::group(['prefix' => 'overtime-multiplication-setup'], function() {
        Route::post('create', [OvertimeMultiplicationSetupController::class, 'create']);
        Route::get('all', [OvertimeMultiplicationSetupController::class, 'list']);
        Route::get('detail/{id}', [OvertimeMultiplicationSetupController::class, 'detail']);
        Route::post('update/{id}', [OvertimeMultiplicationSetupController::class, 'update']);
        Route::post('delete/{id}', [OvertimeMultiplicationSetupController::class, 'delete']);
        Route::post('search', [OvertimeMultiplicationSetupController::class, 'search']);
    });

    Route::group(['prefix' => 'general-setup'], function () {
        Route::post('create', [GeneralSetupController::class, 'create']);
        Route::get('list', [GeneralSetupController::class, 'getAll']);
        Route::get('detail/{id}', [GeneralSetupController::class, 'detail']);
        Route::post('update/{id}', [GeneralSetupController::class, 'update']);
        Route::post('delete/{id}', [GeneralSetupController::class, 'delete']);
        Route::post('search', [GeneralSetupController::class, 'search']);
    });

    Route::group(['prefix' => 'working-hour'], function () {
        Route::post('create', [WorkingHourController::class, 'create']);
        Route::get('all', [WorkingHourController::class, 'getAll']);
        Route::get('list', [WorkingHourController::class, 'list']);
        Route::get('detail/{id}', [WorkingHourController::class, 'detail']);
        Route::post('update/{id}', [WorkingHourController::class, 'update']);
        Route::post('delete/{id}', [WorkingHourController::class, 'delete']);
        Route::post('search', [WorkingHourController::class, 'search']);
    });

    Route::group(['prefix' => 'working-hour-detail'], function () {
        Route::post('create', [WorkingHoursDetailController::class, 'create']);
        Route::get('list', [WorkingHoursDetailController::class, 'list']);
        Route::get('detail/{id}', [WorkingHoursDetailController::class, 'detail']);
        Route::post('update/{id}', [WorkingHoursDetailController::class, 'update']);
        Route::post('delete/{id}', [WorkingHoursDetailController::class, 'delete']);
        Route::post('search', [WorkingHoursDetailController::class, 'search']);
    });

    // route calendar holiday
    Route::group(['prefix' => 'calendar-holiday'], function () {
        Route::post('create', [CalendarHolidayController::class, 'create']);
        Route::get('list', [CalendarHolidayController::class, 'getList']);
        Route::get('detail/{id}', [CalendarHolidayController::class, 'detail']);
        Route::post('update/{id}', [CalendarHolidayController::class, 'update']);
        Route::post('delete/{id}', [CalendarHolidayController::class, 'delete']);
        Route::post('search', [CalendarHolidayController::class, 'search']);
    });

    // route classification of tax payer
    Route::group(['prefix' => 'classification-of-tax-payer'], function () {
        Route::post('create', [ClassificationOfTaxPayerController::class, 'create']);
        Route::get('list', [ClassificationOfTaxPayerController::class, 'getList']);
        Route::get('all', [ClassificationOfTaxPayerController::class, 'getAll']);
        Route::get('detail/{id}', [ClassificationOfTaxPayerController::class, 'detail']);
        Route::post('update/{id}', [ClassificationOfTaxPayerController::class, 'update']);
        Route::post('delete/{id}', [ClassificationOfTaxPayerController::class, 'delete']);
        Route::post('search', [ClassificationOfTaxPayerController::class, 'search']);
    });

    Route::group(['prefix' => 'education-level'], function () {
        Route::post('create', [EducationLevelController::class, 'create']);
        Route::get('list', [EducationLevelController::class, 'getList']);
        Route::get('detail/{id}', [EducationLevelController::class, 'detail']);
        Route::post('update/{id}', [EducationLevelController::class, 'update']);
        Route::post('delete/{id}', [EducationLevelController::class, 'delete']);
        Route::post('search', [EducationLevelController::class, 'search']);
    });
    
    Route::group(['prefix' => 'job-skill'], function () {
        Route::post('create', [JobSkillController::class, 'create']);
        Route::get('list', [JobSkillController::class, 'getList']);
        Route::get('detail/{id}', [JobSkillController::class, 'detail']);
        Route::post('update/{id}', [JobSkillController::class, 'update']);
        Route::post('delete/{id}', [JobSkillController::class, 'delete']);
        Route::post('search', [JobSkillController::class, 'search']);
    });

    Route::group(['prefix'=> 'app-setting'], function() {
        Route::get('all', [AppSettingController::class, 'getAll']);
        Route::post('create', [AppSettingController::class, 'create']);
        Route::post('update/{id}', [AppSettingController::class, 'update']);
        Route::post('delete/{id}', [AppSettingController::class, 'delete']);
    });

    Route::group(['prefix'=> 'user'], function() {
        Route::get('current', function(){
            return response()->json(Auth::user(), 200);
        });
        Route::post('create', [UserController::class, 'createUser']);
        Route::post('update/{id}', [UserController::class, 'updateUserId']);
        Route::post('delete/{id}', [UserController::class, 'deleteUser']);
        Route::get('list', [UserController::class, 'listUser']);
        Route::get('detail/{id}', [UserController::class, 'detailUser']);
        Route::post('search', [UserController::class, 'searchUser']);
    });

    Route::group(['prefix'=> 'customer'], function() {
        Route::post('create', [CustomerController::class, 'create']);
        Route::post('update/{id}', [CustomerController::class, 'update']);
        Route::post('delete/{id}', [CustomerController::class, 'delete']);
        Route::get('list', [CustomerController::class, 'list']);
        Route::get('all', [CustomerController::class, 'getAll']);
        Route::get('detail/{id}', [CustomerController::class, 'detail']);
        Route::post('search', [CustomerController::class, 'search']);
    });

    Route::group(['prefix' => 'customer-contract'], function () {
        Route::post('create', [CustomerContractController::class, 'create']);
        Route::post('update/{id}', [CustomerContractController::class, 'update']);
        Route::post('delete/{id}', [CustomerContractController::class, 'delete']);
        Route::get('list', [CustomerContractController::class, 'list']);
        Route::get('all', [CustomerContractController::class, 'getAll']);
        Route::get('detail/{id}', [CustomerContractController::class, 'detail']);
        Route::post('search', [CustomerContractController::class, 'search']);
    });

    Route::group(['prefix' => 'customer-calendar-holiday'], function () {
        Route::post('create', [CustomerCalendarHolidayController::class, 'create']);
        Route::post('update/{id}', [CustomerCalendarHolidayController::class, 'update']);
        Route::post('delete/{id}', [CustomerCalendarHolidayController::class, 'delete']);
        Route::get('list', [CustomerCalendarHolidayController::class, 'list']);
        Route::get('detail/{id}', [CustomerCalendarHolidayController::class, 'detail']);
        Route::post('search', [CustomerCalendarHolidayController::class, 'search']);
    });

    Route::group(['prefix' => 'customer-rate'], function () {
        Route::post('create', [CustomerRateController::class, 'create']);
        Route::post('update/{id}', [CustomerRateController::class, 'update']);
        Route::post('delete/{id}', [CustomerRateController::class, 'delete']);
        Route::get('list', [CustomerRateController::class, 'list']);
        Route::get('detail/{id}', [CustomerRateController::class, 'detail']);
        Route::post('search', [CustomerRateController::class, 'search']);
    });

    Route::group(['prefix' => 'certificate-classification'], function () {
        Route::post('create', [CertificateClassificationController::class, 'create']);
        Route::post('update/{id}', [CertificateClassificationController::class, 'update']);
        Route::post('delete/{id}', [CertificateClassificationController::class, 'delete']);
        Route::get('list', [CertificateClassificationController::class, 'list']);
        Route::get('detail/{id}', [CertificateClassificationController::class, 'detail']);
        Route::post('search', [CertificateClassificationController::class, 'search']);
    });

    Route::group(['prefix' => 'certificate-type'], function () {
        Route::post('create', [CertificateTypeController::class, 'create']);
        Route::post('update/{id}', [CertificateTypeController::class, 'update']);
        Route::post('delete/{id}', [CertificateTypeController::class, 'delete']);
        Route::get('list', [CertificateTypeController::class, 'list']);
        Route::get('all', [CertificateTypeController::class, 'getAll']);
        Route::get('detail/{id}', [CertificateTypeController::class, 'detail']);
        Route::post('search', [CertificateTypeController::class, 'search']);
    });

    Route::group(['prefix' => 'education-level'], function () {
        Route::post('create', [EducationLevelController::class, 'create']);
        Route::post('update/{id}', [EducationLevelController::class, 'update']);
        Route::post('delete/{id}', [EducationLevelController::class, 'delete']);
        Route::get('list', [EducationLevelController::class, 'list']);
        Route::get('all', [EducationLevelController::class, 'getAll']);
        Route::get('detail/{id}', [EducationLevelController::class, 'detail']);
        Route::post('search', [EducationLevelController::class, 'search']);
    });

    Route::group(['prefix' => 'job-skill'], function () {
        Route::post('create', [JobSkillController::class, 'create']);
        Route::post('update/{id}', [JobSkillController::class, 'update']);
        Route::post('delete/{id}', [JobSkillController::class, 'delete']);
        Route::get('list', [JobSkillController::class, 'list']);
        Route::get('all', [JobSkillController::class, 'getAll']);
        Route::get('detail/{id}', [JobSkillController::class, 'detail']);
        Route::post('search', [JobSkillController::class, 'search']);
    });

    Route::group(['prefix' => 'job-resposibility'], function () {
        Route::post('create', [JobResponsibilityController::class, 'create']);
        Route::post('update/{id}', [JobResponsibilityController::class, 'update']);
        Route::post('delete/{id}', [JobResponsibilityController::class, 'delete']);
        Route::get('list', [JobResponsibilityController::class, 'list']);
        Route::get('detail/{id}', [JobResponsibilityController::class, 'detail']);
        Route::post('search', [JobResponsibilityController::class, 'search']);
    });

    Route::group(['prefix' => 'job-task'], function () {
        Route::post('create', [JobTaskController::class, 'create']);
        Route::post('update/{id}', [JobTaskController::class, 'update']);
        Route::post('delete/{id}', [JobTaskController::class, 'delete']);
        Route::get('list', [JobTaskController::class, 'list']);
        Route::get('detail/{id}', [JobTaskController::class, 'detail']);
        Route::post('search', [JobTaskController::class, 'search']);
    });

    Route::group(['prefix' => 'position'], function () {
        Route::post('create', [PositionControler::class, 'create']);
        Route::get('list', [PositionControler::class, 'list']);
        Route::get('all', [PositionControler::class, 'getAll']);
        Route::get('detail/{id}', [PositionControler::class, 'detail']);
        Route::post('update/{id}', [PositionControler::class, 'update']);
        Route::post('delete/{id}', [PositionControler::class, 'delete']);
        Route::post('search', [PositionControler::class, 'search']);
    });
    
    Route::group(['prefix' => 'position'], function () {
        Route::post('create', [PositionControler::class, 'create']);
        Route::get('list', [PositionControler::class, 'list']);
        Route::get('all', [PositionControler::class, 'getAll']);
        Route::get('detail/{id}', [PositionControler::class, 'detail']);
        Route::post('update/{id}', [PositionControler::class, 'update']);
        Route::post('delete/{id}', [PositionControler::class, 'delete']);
        Route::post('search', [PositionControler::class, 'search']);
    });

    Route::group(['prefix' => 'grade'], function () {
        Route::post('create', [GradeControler::class, 'create']);
        Route::get('list', [GradeControler::class, 'list']);
        Route::get('all', [GradeControler::class, 'getAll']);
        Route::get('detail/{id}', [GradeControler::class, 'detail']);
        Route::post('update/{id}', [GradeControler::class, 'update']);
        Route::post('delete/{id}', [GradeControler::class, 'delete']);
        Route::post('search', [GradeControler::class, 'search']);
    });

    Route::group(['prefix' => 'employee-data'], function () {
        Route::post('create', [EmployeeController::class, 'create']);
        Route::get('list', [EmployeeController::class, 'list']);
        Route::get('all', [EmployeeController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeController::class, 'detail']);
        Route::post('update/{id}', [EmployeeController::class, 'update']);
        Route::post('delete/{id}', [EmployeeController::class, 'delete']);
        Route::post('search', [EmployeeController::class, 'search']);
    });

    Route::group(['prefix' => 'employee-address'], function () {
        Route::post('create', [EmployeeAddressController::class, 'create']);
        Route::get('list', [EmployeeAddressController::class, 'list']);
        Route::get('all', [EmployeeAddressController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeAddressController::class, 'detail']);
        Route::post('update/{id}', [EmployeeAddressController::class, 'update']);
        Route::post('delete/{id}', [EmployeeAddressController::class, 'delete']);
        Route::post('search', [EmployeeAddressController::class, 'search']);
    });

    Route::group(['prefix' => 'employee-customer'], function () {
        Route::post('create', [EmployeeCustomerController::class, 'create']);
        Route::get('list', [EmployeeCustomerController::class, 'list']);
        Route::get('all', [EmployeeCustomerController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeCustomerController::class, 'detail']);
        Route::post('update/{id}', [EmployeeCustomerController::class, 'update']);
        Route::post('delete/{id}', [EmployeeCustomerController::class, 'delete']);
        Route::post('search', [EmployeeCustomerController::class, 'search']);
    });

    Route::group(['prefix' => 'employment'], function () {
        Route::post('create', [EmploymentController::class, 'create']);
        Route::get('list', [EmploymentController::class, 'list']);
        Route::get('all', [EmploymentController::class, 'getAll']);
        Route::get('detail/{id}', [EmploymentController::class, 'detail']);
        Route::post('update/{id}', [EmploymentController::class, 'update']);
        Route::post('delete/{id}', [EmploymentController::class, 'delete']);
        Route::post('search', [EmploymentController::class, 'search']);
    });

    Route::group(['prefix' => 'employment-type'], function () {
        Route::post('create', [EmploymentTypeController::class, 'create']);
        Route::get('list', [EmploymentTypeController::class, 'list']);
        Route::get('all', [EmploymentTypeController::class, 'getAll']);
        Route::get('detail/{id}', [EmploymentTypeController::class, 'detail']);
        Route::post('update/{id}', [EmploymentTypeController::class, 'update']);
        Route::post('delete/{id}', [EmploymentTypeController::class, 'delete']);
        Route::post('search', [EmploymentTypeController::class, 'search']);
    });

    

    Route::group(['prefix' => 'employee-certificate'], function () {
        Route::post('create', [EmployeeCertificateController::class, 'create']);
        Route::get('list', [EmployeeCertificateController::class, 'list']);
        Route::get('all', [EmployeeCertificateController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeCertificateController::class, 'detail']);
        Route::post('update/{id}', [EmployeeCertificateController::class, 'update']);
        Route::post('delete/{id}', [EmployeeCertificateController::class, 'delete']);
        Route::post('search', [EmployeeCertificateController::class, 'search']);
    });

    Route::group(['prefix' => 'employee-education'], function () {
        Route::post('create', [EmployeeEducationController::class, 'create']);
        Route::get('list', [EmployeeEducationController::class, 'list']);
        Route::get('all', [EmployeeEducationController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeEducationController::class, 'detail']);
        Route::post('update/{id}', [EmployeeEducationController::class, 'update']);
        Route::post('delete/{id}', [EmployeeEducationController::class, 'delete']);
        Route::post('search', [EmployeeEducationController::class, 'search']);
    });

    Route::group(['prefix' => 'employee-skill'], function () {
        Route::post('create', [EmployeeSkillController::class, 'create']);
        Route::get('list', [EmployeeSkillController::class, 'list']);
        Route::get('all', [EmployeeSkillController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeSkillController::class, 'detail']);
        Route::post('update/{id}', [EmployeeSkillController::class, 'update']);
        Route::post('delete/{id}', [EmployeeSkillController::class, 'delete']);
        Route::post('search', [EmployeeSkillController::class, 'search']);
    });
    
    Route::group(['prefix' => 'employee-professional-experience'], function () {
        Route::post('create', [EmployeeProfessionalExperienceController::class, 'create']);
        Route::get('list', [EmployeeProfessionalExperienceController::class, 'list']);
        Route::get('all', [EmployeeProfessionalExperienceController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeProfessionalExperienceController::class, 'detail']);
        Route::post('update/{id}', [EmployeeProfessionalExperienceController::class, 'update']);
        Route::post('delete/{id}', [EmployeeProfessionalExperienceController::class, 'delete']);
        Route::post('search', [EmployeeProfessionalExperienceController::class, 'search']);
    });
    
    Route::group(['prefix' => 'employee-project-experience'], function () {
        Route::post('create', [EmployeeProjectExperienceController::class, 'create']);
        Route::get('list', [EmployeeProjectExperienceController::class, 'list']);
        Route::get('all', [EmployeeProjectExperienceController::class, 'getAll']);
        Route::get('detail/{id}', [EmployeeProjectExperienceController::class, 'detail']);
        Route::post('update/{id}', [EmployeeProjectExperienceController::class, 'update']);
        Route::post('delete/{id}', [EmployeeProjectExperienceController::class, 'delete']);
        Route::post('search', [EmployeeProjectExperienceController::class, 'search']);
    });


    Route::group(['prefix' => 'leave-category'], function () {
        Route::post('create', [LeaveCategoryController::class, 'create']);
        Route::get('list', [LeaveCategoryController::class, 'list']);
        Route::get('all', [LeaveCategoryController::class, 'getAll']);
        Route::get('all/employee/{employee_id}', [LeaveCategoryController::class, 'getEmployeeLeaveCategories']);
        Route::get('detail/{id}', [LeaveCategoryController::class, 'detail']);
        Route::post('update/{id}', [LeaveCategoryController::class, 'update']);
        Route::post('delete/{id}', [LeaveCategoryController::class, 'delete']);
        Route::post('search', [LeaveCategoryController::class, 'search']);
    });

    Route::group(['prefix' => 'leave-adjustment'], function () {
        Route::post('create', [LeaveAdjustmentController::class, 'create']);
        Route::get('list', [LeaveAdjustmentController::class, 'list']);
        Route::get('all', [LeaveAdjustmentController::class, 'getAll']);
        Route::get('detail/{id}', [LeaveAdjustmentController::class, 'detail']);
        Route::post('update/{id}', [LeaveAdjustmentController::class, 'update']);
        Route::post('delete/{id}', [LeaveAdjustmentController::class, 'delete']);
        Route::post('search', [LeaveAdjustmentController::class, 'search']);
    });

    Route::group(['prefix' => 'leave-request'], function () {
        Route::post('create', [LeaveRequestController::class, 'create']);
        Route::get('list', [LeaveRequestController::class, 'list']);
        Route::get('all', [LeaveRequestController::class, 'getAll']);
        Route::get('detail/{id}', [LeaveRequestController::class, 'detail']);
        Route::post('update/{id}', [LeaveRequestController::class, 'update']);
        Route::post('delete/{id}', [LeaveRequestController::class, 'delete']);
        Route::post('search', [LeaveRequestController::class, 'search']);
    });

    Route::group(['prefix' => 'leave-history'], function () {
        Route::post('create', [LeaveHistoryController::class, 'create']);
        Route::get('list', [LeaveHistoryController::class, 'list']);
        Route::get('all', [LeaveHistoryController::class, 'getAll']);
        Route::get('detail/{id}', [LeaveHistoryController::class, 'detail']);
        Route::post('update/{id}', [LeaveHistoryController::class, 'update']);
        Route::post('delete/{id}', [LeaveHistoryController::class, 'delete']);
        Route::post('search', [LeaveHistoryController::class, 'search']);
    });

    Route::group(['prefix' => 'timesheet'], function () {
        Route::post('import-to-temp-mcd', [ImportTimeSheetController::class, 'importToTempMcd']);
        Route::post('import-to-temp-pns', [ImportTimeSheetController::class, 'importToTempPns']);
        Route::post('create-temp-timesheet', [ImportTimeSheetController::class, 'createTempTimesheet']);
        Route::get('detail-temp-timesheet/{slug}', [ImportTimeSheetController::class, 'detailTempTimesheet']);
        Route::post('compare-pns-mcd', [ImportTimeSheetController::class, 'comparePnsMcd']);
        Route::get('list-diff/{temp_timesheet_id}', [ImportTimeSheetController::class, 'difflist']);
        Route::get('list-pns-temp/{temp_timesheet_id}', [ImportTimeSheetController::class, 'listPnsTemp']);
        Route::get('list-mcd-temp/{temp_timesheet_id}', [ImportTimeSheetController::class, 'listMcdTemp']);
        Route::get('list-compared-pns-mcd', [ImportTimeSheetController::class, 'listComparedPnsMcd']);
        Route::post('resolve-compare-conflict/id/{id}', [ImportTimeSheetController::class, 'resolveConflict']);
        Route::post('edit-compare-conflict-value', [ImportTimeSheetController::class, 'editConflictValue']);
        Route::get('detail-pns-mcd-diff/id/{id}', [ImportTimeSheetController::class, 'getDetailDiff']);
        Route::get('list-pns-mcd', [ImportTimeSheetController::class, 'list']);
        Route::post('update-pns-mcd/{id}', [ImportTimeSheetController::class, 'update']);
        Route::post('delete-pns-mcd/{id}', [ImportTimeSheetController::class, 'delete']);
        Route::post('search-pns/{temp_timesheet_id}', [ImportTimeSheetController::class, 'searchPNS']);
        Route::post('search-mcd/{temp_timesheet_id}', [ImportTimeSheetController::class, 'searchMCD']);
        Route::post('move-to-timesheet/{temp_timesheet_id}', [ImportTimeSheetController::class, 'moveToTimesheet']);
    });

    Route::group(['prefix' => 'temp-timesheet'], function () {
        Route::get('overtime-list/{temp_timesheet_str}', [TempTimesheetLineController::class, 'overtimeList']);
        Route::post('calculate-overtime/{temp_timesheet_str}', [TempTimesheetLineController::class, 'calculateOvertime']);
        Route::get('overtime-detail/{id}', [TempTimesheetLineController::class, 'overtimeDetail']);
        Route::post('overtime-verify/{temp_timesheet_str}', [TempTimesheetLineController::class, 'overtimeVerify']);
    });


    

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('update-self', [UserController::class, 'updateUserSelf']);

    Route::get('check-auth', function () {
        return response()->json([
            'message' => 'Success',
            'data' => 'Authenticated'
        ], 200);
    });
});

Route::post('login', [AuthController::class, 'login']);
Route::get('validation-error', function () {
    return response()->json([
        "message" => "Validation error"
    ], 401);
})->name('login');
