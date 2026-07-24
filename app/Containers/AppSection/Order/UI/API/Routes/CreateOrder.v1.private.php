<?php

/**
 * @apiGroup           Order
 * @apiName            CreateOrder
 *
 * @api                {POST} /v1/orders Create Order
 * @apiDescription     Create a new Order with 1 to 100 items. Supports existing customer ID or creating/snapshotting customer details.
 *
 * @apiVersion         1.0.0
 * @apiPermission      Authenticated ['permissions' => '', 'roles' => '']
 *
 * @apiHeader          {String} accept=application/json
 * @apiHeader          {String} authorization=Bearer
 *
 * @apiBody            {String} [customer_id] Existing Customer Hashed ID
 * @apiBody            {String} [customer_name_snapshot] Required if customer_id not present (max 150 chars)
 * @apiBody            {String} [customer_phone_snapshot] Required if customer_id not present (E.164 format)
 * @apiBody            {String} [customer_address_snapshot] Required if customer_id not present (max 500 chars)
 * @apiBody            {Number} payment_method Payment method: 1=COD, 2=CASH, 3=BANK_TRANSFER, 4=DEBT
 * @apiBody            {String} [delivery_date] Date YYYY-MM-DD
 * @apiBody            {String} [shipping_carrier] Carrier name (max 100 chars)
 * @apiBody            {String} [bank_name] Required if payment_method=3
 * @apiBody            {String} [bank_account_number] Required if payment_method=3
 * @apiBody            {Number} [credit_days] Required if payment_method=4 (1..365)
 * @apiBody            {Number} [shipping_fee] Decimal shipping fee
 * @apiBody            {Number} [advance_payment] Decimal advance payment
 * @apiBody            {Object[]} items Array of 1 to 100 order items
 * @apiBody            {String} items.product_id Active Product Hashed ID
 * @apiBody            {Number} items.quantity Quantity integer (1..1000000)
 * @apiBody            {Number} [items.unit_price] Custom unit price decimal
 * @apiBody            {String} [items.price_override_reason] Required if unit_price differs from product list price
 *
 * @apiSuccessExample  {json} Success-Response:
 * HTTP/1.1 201 Created
 * {
 *     "data": {
 *         "object": "Order",
 *         "id": "...",
 *         "code": "ORD-...",
 *         "status": 1
 *     }
 * }
 */

use App\Containers\AppSection\Order\UI\API\Controllers\CreateOrderController;
use Illuminate\Support\Facades\Route;

Route::post('orders', [CreateOrderController::class, 'createOrder'])
    ->middleware(['auth:api']);
