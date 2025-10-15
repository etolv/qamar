<?php

namespace App\Enums;


enum DeleteableModelEnum: string
{
    case Attendance = "Attendance";
    case Nationality = "Nationality";
    case Category = "Category";
    case cash_flow = "cash_flow";
    case Brand = "Brand";
    case Product = "Product";
    case Stock = "Stock";
    case State = "State";
    case City = "City";
    case Municipal = "Municipal";
    case Notification = "Notification";
    case Role = "Role";
    case Slider = "Slider";
    case Job = "Job";
    case Branch = "Branch";
    case User = "User";
    case Service = "Service";
    case Supplier = "Supplier";
    case Unit = "Unit";
    case booking_edit_request = "booking_edit_request";
    case RateReason = "RateReason";
    case EmployeeShift = "EmployeeShift";
    case Task = "Task";
}
