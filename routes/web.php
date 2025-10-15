<?php

use App\Http\Controllers\Dashboard\CardController;
use App\Http\Controllers\Dashbaord\EmployeeTaskController;
use App\Http\Controllers\Dashboard\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\BookingEditRequestController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\CashFlowController;
use App\Http\Controllers\Dashboard\RateReasonController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\BillController;
use App\Http\Controllers\Dashboard\BillReturnController;
use App\Http\Controllers\Dashboard\BillTypeController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\CafeteriaOrderController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CityController;
use App\Http\Controllers\Dashboard\CustodyController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DriverController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\EmployeeShiftController;
use App\Http\Controllers\Dashboard\GeneratedSalaryController;
use App\Http\Controllers\Dashboard\JobController;
use App\Http\Controllers\Dashboard\ListedOrderController;
use App\Http\Controllers\Dashboard\LoyaltyController;
use App\Http\Controllers\Dashboard\MediaController;
use App\Http\Controllers\Dashboard\ModelRecordController;
use App\Http\Controllers\Dashboard\MunicipalController;
use App\Http\Controllers\Dashboard\NationalityController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OrderServicePostponeController;
use App\Http\Controllers\Dashboard\OrderServiceReturnController;
use App\Http\Controllers\Dashboard\OrderServiceSessionController;
use App\Http\Controllers\Dashboard\PackageController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SalaryController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\ShiftController;
use App\Http\Controllers\Dashboard\StateController;
use App\Http\Controllers\Dashboard\StockController;
use App\Http\Controllers\Dashboard\StockWithdrawalController;
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\SystemDataController;
use App\Http\Controllers\Dashboard\TaskController;
use App\Http\Controllers\Dashboard\TransferController;
use App\Http\Controllers\Dashboard\TranslationController;
use App\Http\Controllers\Dashboard\UnitController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\VacationController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\OrderServiceController;
use App\Http\Controllers\Dashboard\RateController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\TripController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dashboard\SliderController;
use App\Http\Controllers\TempAttendanceController;
use App\Http\Controllers\UrwayController;

Route::get('test', [HomeController::class, 'test']);

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/privacy_policy', [HomeController::class, 'privacy'])->name('privacy_policy');
Route::get('/terms_conditions', [HomeController::class, 'terms'])->name('terms_conditions');
Route::get('login', [AuthController::class, 'show_login'])->name('show_login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('swap');

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('/system-data', [SystemDataController::class, 'getSystemData'])->name('system-data');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');


    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('customer', CustomerController::class);
    Route::get('customer-fetch', [CustomerController::class, 'fetch'])->name('customer.fetch');
    Route::get('customer-points/{customer_id}', [CustomerController::class, 'points'])->name('customer.points');
    Route::get('customer-search', [CustomerController::class, 'search'])->name('customer.search');
    Route::get('customer-orders/{customer_id}', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('customer-history/{customer_id}', [CustomerController::class, 'service_and_product_history'])->name('customer.history');

    Route::resource('bill', BillController::class)->except('destroy');
    Route::get('bill-received/{bill_id}', [BillController::class, 'received'])->name('bill.received');
    Route::get('bill-destroy/{bill_id}', [BillController::class, 'destroy'])->name('bill.destroy');
    Route::get('bill-fetch', [BillController::class, 'fetch'])->name('bill.fetch');
    Route::get('bill-debt', [BillController::class, 'debt'])->name('bill.debt');
    Route::get('bill-debt-fetch', [BillController::class, 'debt_fetch'])->name('bill.debt.fetch');
    Route::get('bill-search', [BillController::class, 'search'])->name('bill.search');
    Route::put('bill-payment/{bill_id}', [BillController::class, 'store_payment'])->name('bill.payment.store');

    Route::resource('bill-return', BillReturnController::class)->except('delete');
    Route::get('bill-return-fetch', [BillReturnController::class, 'fetch'])->name('bill-return.fetch');

    Route::resource('stock-withdrawal', StockWithdrawalController::class)->except('delete');
    Route::get('stock-withdrawal-fetch', [StockWithdrawalController::class, 'fetch'])->name('stock.withdrawal.fetch');

    Route::resource('bill-type', BillTypeController::class);
    Route::get('bill-type-fetch', [BillTypeController::class, 'fetch'])->name('bill.type.fetch');
    Route::get('bill-type-search', [BillTypeController::class, 'search'])->name('bill.type.search');

    Route::resource('custody', CustodyController::class);
    Route::get('custody-fetch', [CustodyController::class, 'fetch'])->name('custody.fetch');
    Route::post('custody-waste/{custody_id}', [CustodyController::class, 'waste'])->name('custody.waste');
    Route::post('custody-return/{custody_id}', [CustodyController::class, 'return'])->name('custody.return');

    Route::resource('driver', DriverController::class);
    Route::get('driver-fetch', [DriverController::class, 'fetch'])->name('driver.fetch');
    Route::get('driver-search', [DriverController::class, 'search'])->name('driver.search');

    Route::resource('trip', TripController::class);
    Route::get('trip-fetch', [TripController::class, 'fetch'])->name('trip.fetch');
    Route::get('trip-search', [TripController::class, 'search'])->name('trip.search');

    Route::resource('supplier', SupplierController::class);
    Route::get('supplier-fetch', [SupplierController::class, 'fetch'])->name('supplier.fetch');
    Route::get('supplier-search', [SupplierController::class, 'search'])->name('supplier.search');

    Route::resource('unit', UnitController::class);
    Route::get('unit-fetch', [UnitController::class, 'fetch'])->name('unit.fetch');
    Route::get('unit-search', [UnitController::class, 'search'])->name('unit.search');

    Route::resource('card', CardController::class);
    Route::get('card-fetch', [CardController::class, 'fetch'])->name('card.fetch');
    Route::get('card-search', [CardController::class, 'search'])->name('card.search');

    Route::resource('nationality', NationalityController::class);
    Route::get('nationality-fetch', [NationalityController::class, 'fetch'])->name('nationality.fetch');
    Route::get('nationality-search', [NationalityController::class, 'search'])->name('nationality.search');

    Route::resource('job', JobController::class);
    Route::get('job-fetch', [JobController::class, 'fetch'])->name('job.fetch');
    Route::get('job-search', [JobController::class, 'search'])->name('job.search');

    Route::resource('branch', BranchController::class);
    Route::get('branch-fetch', [BranchController::class, 'fetch'])->name('branch.fetch');
    Route::get('branch-search', [BranchController::class, 'search'])->name('branch.search');

    Route::resource('coupon', CouponController::class);
    Route::get('coupon-fetch', [CouponController::class, 'fetch'])->name('coupon.fetch');
    Route::get('coupon-search', [CouponController::class, 'search'])->name('coupon.search');

    Route::resource('admin', AdminController::class);
    Route::get('admin-fetch', [AdminController::class, 'fetch'])->name('admin.fetch');

    Route::get('user-search', [UserController::class, 'search'])->name('user.search');

    Route::resource('notification', NotificationController::class);
    Route::get('notification-user-create/{user_id}', [NotificationController::class, 'create_user_notification'])->name('notification.user.create');
    Route::get('notification-fetch', [NotificationController::class, 'fetch'])->name('notification.fetch');
    Route::get('update-fcm-token', [NotificationController::class, 'updateFcmToken'])->name('notification.updateFcmTokenAdmin');

    Route::resource('task', TaskController::class);
    Route::get('task-fetch', [TaskController::class, 'fetch'])->name('task.fetch');

    Route::resource('employee-task', EmployeeTaskController::class);

    Route::resource('employee-shift', EmployeeShiftController::class)->except('destroy');
    Route::get('employee-shift-fetch', [EmployeeShiftController::class, 'fetch'])->name('employee-shift.fetch');
    Route::get('employee-shift-search', [EmployeeShiftController::class, 'search'])->name('employee-shift.search');
    Route::get('employee-shift-destroy/{id}', [EmployeeShiftController::class, 'destroy'])->name('employee-shift.delete');

    Route::resource('shift', ShiftController::class);
    Route::get('shift-fetch', [ShiftController::class, 'fetch'])->name('shift.fetch');
    Route::get('shift-search', [ShiftController::class, 'search'])->name('shift.search');
    Route::get('shift-duplicate/{id}', [ShiftController::class, 'duplicate'])->name('shift.duplicate');

    Route::resource('attendance', AttendanceController::class);
    Route::post('attendance-import', [AttendanceController::class, 'import'])->name('attendance.import');
    Route::get('attendance-fetch', [AttendanceController::class, 'fetch'])->name('attendance.fetch');

    // temp attendance
    Route::resource('temp-attendance', TempAttendanceController::class);
    Route::post('temp-attendance-import', [TempAttendanceController::class, 'import'])->name('attendance.temp.import');
    Route::get('temp-attendance-clear', [TempAttendanceController::class, 'clear'])->name('attendance.temp.clear');
    Route::get('temp-attendance-fetch', [TempAttendanceController::class, 'fetch'])->name('attendance.temp.fetch');

    Route::resource('vacation', VacationController::class);
    Route::post('vacation-round/{employee}', [VacationController::class, 'round'])->name('vacation.round');
    Route::get('vacation-fetch', [VacationController::class, 'fetch'])->name('vacation.fetch');
    Route::get('vacation-status/{id}', [VacationController::class, 'update_status'])->name('vacation.update_status');

    Route::resource('salary', SalaryController::class);
    Route::get('salary-fetch', [SalaryController::class, 'fetch'])->name('salary.fetch');
    Route::get('salary-close/{id}', [SalaryController::class, 'close'])->name('salary.close');

    Route::resource('generated-salary', GeneratedSalaryController::class);
    Route::get('generated-salary-fetch', [GeneratedSalaryController::class, 'fetch'])->name('salary.generated.fetch');

    Route::resource('cash-flow', CashFlowController::class);
    Route::get('cash-flow-fetch', [CashFlowController::class, 'fetch'])->name('cash_flow.fetch');

    Route::resource('employee', EmployeeController::class);
    Route::get('employee-fetch', [EmployeeController::class, 'fetch'])->name('employee.fetch');
    Route::get('employee-search', [EmployeeController::class, 'search'])->name('employee.search');
    Route::get('employee-settings', [EmployeeController::class, 'settings'])->name('employee.settings');
    Route::post('employee-settings', [EmployeeController::class, 'update_settings'])->name('employee.settings.update');
    Route::post('employee-import', [EmployeeController::class, 'import'])->name('employee.import');

    Route::resource('driver', DriverController::class);
    Route::get('driver-fetch', [DriverController::class, 'fetch'])->name('driver.fetch');
    Route::get('driver-search', [DriverController::class, 'search'])->name('driver.search');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile-dashboard', [ProfileController::class, 'dashboard'])->name('profile.dashboard');
    Route::post('profile', [ProfileController::class, 'store'])->name('profile.store');

    Route::resource('state', StateController::class);
    Route::get('state-fetch', [StateController::class, 'fetch'])->name('state.fetch');

    Route::resource('city', CityController::class);
    Route::get('city-fetch', [CityController::class, 'fetch'])->name('city.fetch');
    Route::get('city-search', [CityController::class, 'search'])->name('city.search');

    Route::resource('municipal', MunicipalController::class);
    Route::get('municipal-fetch', [MunicipalController::class, 'fetch'])->name('municipal.fetch');
    Route::get('municipal-search', [MunicipalController::class, 'search'])->name('municipal.search');

    Route::resource('roles', RoleController::class);
    Route::get('roles-search', [RoleController::class, 'search'])->name('role.search');

    Route::resource('product', ProductController::class);
    Route::get('product-fetch', [ProductController::class, 'fetch'])->name('product.fetch');
    Route::get('product-search', [ProductController::class, 'search'])->name('product.search');
    Route::get('product-barcode/{product_id}', [ProductController::class, 'barcode'])->name('product.barcode');
    Route::post('product-import', [ProductController::class, 'import'])->name('product.import');

    Route::resource('stock', StockController::class);
    Route::get('stock-fetch', [StockController::class, 'fetch'])->name('stock.fetch');
    Route::get('stock-search', [StockController::class, 'search'])->name('stock.search');
    Route::post('stock-import', [StockController::class, 'import'])->name('stock.import');

    Route::resource('transfer', TransferController::class);
    Route::get('transfer-fetch', [TransferController::class, 'fetch'])->name('transfer.fetch');

    Route::resource('loyalty', LoyaltyController::class);
    Route::get('loyalty-fetch', [LoyaltyController::class, 'fetch'])->name('loyalty.fetch');
    Route::get('loyalty-settings', [LoyaltyController::class, 'settings'])->name('loyalty.settings');
    Route::post('loyalty-settings', [LoyaltyController::class, 'update_settings'])->name('loyalty.settings.update');


    Route::resource('model-record', ModelRecordController::class);
    Route::get('model-record-fetch', [ModelRecordController::class, 'fetch'])->name('model_record.fetch');

    Route::resource('booking', BookingController::class);
    Route::get('today-booking', [BookingController::class, 'today'])->name('booking.today');
    Route::get('pending-booking', [BookingController::class, 'pending'])->name('booking.pending');
    Route::get('booking-fetch', [BookingController::class, 'fetch'])->name('booking.fetch');
    Route::get('booking-search', [BookingController::class, 'search'])->name('booking.search');

    Route::resource('rate', RateController::class);
    Route::get('rate-fetch', [RateController::class, 'fetch'])->name('rate.fetch');

    Route::resource('rate-reason', RateReasonController::class);
    Route::get('rate-reason-fetch', [RateReasonController::class, 'fetch'])->name('rate.reason.fetch');
    Route::get('rate-reason-search', [RateReasonController::class, 'search'])->name('rate.reason.search');

    Route::resource('package', PackageController::class);
    Route::get('package-fetch', [PackageController::class, 'fetch'])->name('package.fetch');
    Route::get('package-search', [PackageController::class, 'search'])->name('package.search');
    Route::get('package-items/{package_id}', [PackageController::class, 'items'])->name('package.items');

    Route::resource('booking-edit-request', BookingEditRequestController::class);
    Route::get('booking-edit-request-fetch', [BookingEditRequestController::class, 'fetch'])->name('booking-edit-request.fetch');
    Route::get('booking-edit-request-delete', [BookingEditRequestController::class, 'index_delete'])->name('booking-edit-request-delete.index');

    Route::resource('order-service-return', OrderServiceReturnController::class);
    Route::get('order-service-return-fetch', [OrderServiceReturnController::class, 'fetch'])->name('order-service-return.fetch');

    Route::resource('order-service-postpone', OrderServicePostponeController::class);
    Route::get('order-service-postpone-fetch', [OrderServicePostponeController::class, 'fetch'])->name('order-service-postpone.fetch');
    Route::get('order-service-postpone-return/{id}', [OrderServicePostponeController::class, 'return'])->name('order-service-postpone.return');
    Route::get('order-service-postpone-complete/{id}', [OrderServicePostponeController::class, 'complete'])->name('order-service-postpone.complete');

    Route::post('order_service_update_employee', [OrderServiceController::class, 'update_employee'])->name('order_service.update_employee');

    Route::resource('order', OrderController::class);
    Route::get('order-fetch', [OrderController::class, 'fetch'])->name('order.fetch');
    Route::get('order-search', [OrderController::class, 'search'])->name('order.search');
    Route::get('order-complete/{id}', [OrderController::class, 'complete'])->name('order.complete');
    Route::get('order-pdf/{id}', [OrderController::class, 'pdf'])->name('order.pdf');
    Route::get('order-test/{id}', [OrderController::class, 'test_pdf'])->name('order.test_pdf');
    Route::get('order-return/{id}', [OrderController::class, 'return'])->name('order.return');
    Route::get('order-postpone/{id}', [OrderController::class, 'postpone'])->name('order.postpone');
    Route::get('order-rate/{id}', [OrderController::class, 'rate'])->name('order.rate');
    Route::put('order-rate/{id}', [OrderController::class, 'submit_rate'])->name('order.rate.submit');
    // Route::put('order-postpone/{id}', [OrderController::class, 'submit_postpone'])->name('order.postpone.submit');

    Route::resource('listed-order', ListedOrderController::class);
    Route::get('listed-order-fetch', [ListedOrderController::class, 'fetch'])->name('listed-order.fetch');

    Route::resource('order-service-session', OrderServiceSessionController::class);
    Route::post('order-service-session-update-employee', [OrderServiceSessionController::class, 'update_employee'])->name('order-service-session.update-employee');
    Route::get('order-service-session-pdf/{id}', [OrderServiceSessionController::class, 'pdf'])->name('order-service-session.pdf');

    Route::resource('payment', PaymentController::class);
    Route::get('payment-fetch', [PaymentController::class, 'fetch'])->name('payment.fetch');

    Route::resource('setting', SettingController::class);

    Route::resource('slider', SliderController::class);

    Route::resource('service', ServiceController::class);
    Route::get('service-fetch', [ServiceController::class, 'fetch'])->name('service.fetch');
    Route::get('service-search', [ServiceController::class, 'search'])->name('service.search');
    Route::get('service-products/{id}', [ServiceController::class, 'products'])->name('service.products');
    Route::post('service-import', [ServiceController::class, 'import'])->name('service.import');

    Route::resource('brand', BrandController::class);
    Route::get('brand-fetch', [BrandController::class, 'fetch'])->name('brand.fetch');
    Route::get('brand-search', [BrandController::class, 'search'])->name('brand.search');

    Route::resource('category', CategoryController::class);
    Route::get('category-fetch', [CategoryController::class, 'fetch'])->name('category.fetch');
    Route::get('category-search', [CategoryController::class, 'search'])->name('category.search');
    Route::get('category-visible/{id}', [CategoryController::class, 'visible'])->name('category.visible');

    Route::resource('cafeteria-order', CafeteriaOrderController::class);
    Route::get('cafeteria-order-fetch', [CafeteriaOrderController::class, 'fetch'])->name('cafeteria.order.fetch');

    Route::group(['prefix' => 'translation', 'as' => 'translation.'], function () {
        Route::controller(TranslationController::class)->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('update/{key}', 'update')->name('update');
            Route::post('delete/{id}', 'destroy')->name('destroy');
            Route::get('translate_all', 'translate_all')->name('translate_all');
        });
    });
    // Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {});
    Route::prefix('report')->as('report.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('order', [ReportController::class, 'order'])->name('order');
        Route::get('order-fetch', [ReportController::class, 'order_fetch'])->name('order.fetch');
        Route::get('service', [ReportController::class, 'service'])->name('service');
        Route::get('service-fetch', [ReportController::class, 'service_fetch'])->name('service.fetch');
        Route::get('employee', [ReportController::class, 'employee'])->name('employee');
        Route::get('employee-fetch', [ReportController::class, 'employee_fetch'])->name('employee.fetch');
        Route::get('supplier', [ReportController::class, 'supplier'])->name('supplier');
        Route::get('supplier-fetch', [ReportController::class, 'supplier_fetch'])->name('supplier.fetch');
        Route::get('expense', [ReportController::class, 'expense'])->name('expense');
        Route::get('expense-fetch', [ReportController::class, 'expense_fetch'])->name('expense.fetch');
        Route::get('trial', [ReportController::class, 'trial'])->name('trial');
        Route::get('ledger', [ReportController::class, 'ledger'])->name('ledger');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('financial-center', [ReportController::class, 'financialCenter'])->name('financial.center');
        Route::get('finance', [ReportController::class, 'finance'])->name('finance');
        Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('offer', [ReportController::class, 'offer'])->name('offer');
        Route::get('offer-fetch', [ReportController::class, 'offer_fetch'])->name('offer.fetch');
        Route::get('income', [ReportController::class, 'income'])->name('income');
    });

    Route::resource('account', AccountController::class);
    Route::get('account-transaction/{account}', [AccountController::class, 'transaction'])->name('account.transaction');
    Route::get('account-search', [AccountController::class, 'search'])->name('account.search');
    Route::resource('transaction', TransactionController::class)->except('destroy');
    Route::get('transaction-fetch', [TransactionController::class, 'fetch'])->name('transaction.fetch');
    Route::get('transaction-destroy/{id}', [TransactionController::class, 'destroy'])->name('transaction.destroy');

    Route::get('urway-test', [UrwayController::class, 'test'])->name('urway.test');

    Route::get('media', [MediaController::class, 'index'])->name('media.index');

    Route::delete('/delete-object/{objectId}/{objectType}/{actionType}', DeleteController::class)->name('delete_object');
});

Route::get('urway-index', [UrwayController::class, 'index'])->name('urway.index');

Route::get('test-mail', function () {
    $payment = App\Models\Payment::latest()->first();
    return view('front.payment-page', compact('payment'));
});
