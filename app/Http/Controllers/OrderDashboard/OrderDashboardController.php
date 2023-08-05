<?php

namespace App\Http\Controllers\OrderDashboard;

use App\Http\Controllers\Controller;
use App\Models\OrderDashboard\OrderDashboard;
use Illuminate\Http\Request;

class OrderDashboardController extends Controller
{
    public $order_dashboard;

    public function __construct()
    {
        $this->order_dashboard = new OrderDashboard();
    }

    //get category wise order count
    public function getCategoryWiseOrderCount()
    {
        $response = $this->order_dashboard->getCountForEachCat();

        return $response;
    }

    //essential checkout orders
    public function getRecentEssentialOrders()
    {
        $response = $this->order_dashboard->getEssentialRecentOrders();

        return $response;
    }

    //nonessential checkout orders
    public function getRecentNonEssentialOrders()
    {
        $response = $this->order_dashboard->getNonEssentialRecentOrders();

        return $response;
    }

    //lifestyle checkout orders
    public function getRecentLifeStyleOrders()
    {
        $response = $this->order_dashboard->getLifeStyleRecentOrders();

        return $response;
    }

    //education checkout orders
    public function getRecentEducationOrders()
    {
        $response = $this->order_dashboard->getEducationRecentOrders();

        return $response;
    }


    //essential non essential all checkout orders
    public function getEssNonEsAllOrders()
    {
        $response = $this->order_dashboard->getAllEssentialNonEssentialOrders();

        return $response;
    }

    //fetch order payment transaction details
    public function getOrderPayTransaction(Request $request)
    {

        $paycat = $request['pay_category'];
        $orderid = $request['order_id'];

        $response = $this->order_dashboard->getOrderPaymentTransactionData($paycat, $orderid);

        return $response;
    }

    //change the status of order
    public function changeOrderStatus(Request $request)
    {

        $value = $request['value'];
        $orderid = $request['order_id'];

        $response = $this->order_dashboard->changeDeliveryStatus($value, $orderid);

        return $response;
    }

    //get flight reservations all
    public function fetchAllFLightReservations()
    {
        $response = $this->order_dashboard->getAllFLightReservations();

        return $response;
    }
}
