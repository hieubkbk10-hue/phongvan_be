<?php

/**
 * @apiGroup           Order
 * @apiName            UpdateOrder
 *
 * @api                {PATCH} /v1/orders/:id Update Order
 * @apiDescription     Update an existing Pending (1) Order. Cannot update status, code, subtotal, total, or completed/cancelled orders.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiParam           {String} id Order Hashed ID
 * @apiBody            {String} [customer_id] Existing Customer Hashed ID
 * @apiBody            {String} [customer_name_snapshot] Override customer name (max 150 chars)
 * @apiBody            {String} [customer_phone_snapshot] Override customer phone (E.164 format)
 * @apiBody            {String} [customer_address_snapshot] Override customer address (max 500 chars)
 * @apiBody            {Number} [payment_method] Payment method: 1=COD, 2=CASH, 3=BANK_TRANSFER, 4=DEBT
 * @apiBody            {String} [delivery_date] Date YYYY-MM-DD
 * @apiBody            {String} [shipping_carrier] Carrier name (max 100 chars)
 * @apiBody            {String} [bank_name] Required if payment_method=3
 * @apiBody            {String} [bank_account_number] Required if payment_method=3
 * @apiBody            {Number} [credit_days] Required if payment_method=4 (1..365)
 * @apiBody            {Number} [shipping_fee] Decimal shipping fee
 * @apiBody            {Number} [advance_payment] Decimal advance payment
 * @apiBody            {Object[]} [items] Array of 1 to 100 order items replacing existing items
 * @apiBody            {String} items.product_id Active Product Hashed ID
 * @apiBody            {Number} items.quantity Quantity integer (1..1000000)
 * @apiBody            {Number} [items.unit_price] Custom unit price decimal
 * @apiBody            {String} [items.price_override_reason] Required if unit_price differs from product list price
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 200 OK
 * {
 *     "data": {
 *         "object": "Order",
 *         "id": "...",
 *         "code": "ORD-...",
 *         "status": 1
 *     }
 * }
 */

use App\Containers\AppSection\Order\UI\API\Controllers\UpdateOrderController;
use Illuminate\Support\Facades\Route;

Route::patch('orders/{id}', [UpdateOrderController::class, 'updateOrder'])
    ->middleware(['auth:api']);
