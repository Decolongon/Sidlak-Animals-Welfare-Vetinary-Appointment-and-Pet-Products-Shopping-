@component('mail::message')
# Order Status Update

Hello {{ ucwords($user->name) }},

We wanted to update you on your order **#{{ $order->order_num }}**. The current status is:  
**<span style="color: #2d3748; font-weight: bold;">{{ $order_status_label }}</span>**

---

## 📦 Order Summary

<div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; max-width: 100%; overflow: hidden; box-sizing: border-box;">
    <table style="width: 100%; min-width: 300px; border-collapse: collapse;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 8px 0; vertical-align: top; width: 40%;"><strong>Order Number:</strong></td>
            <td style="padding: 8px 0; vertical-align: top;">#{{ $order->order_num }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; vertical-align: top;"><strong>Order Date:</strong></td>
            <td style="padding: 8px 0; vertical-align: top;">{{ $order->created_at->format('F j, Y') }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; vertical-align: top;"><strong>Status:</strong></td>
            <td style="padding: 8px 0; vertical-align: top;">
                <span style="background: #e2e8f0; padding: 4px 12px; border-radius: 20px; font-weight: bold; display: inline-block;">
                    {{ $order_status_label }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; vertical-align: top;"><strong>Total Amount:</strong></td>
            <td style="padding: 8px 0; vertical-align: top; font-size: 18px; font-weight: bold; color: #2d3748;">
                ₱{{ number_format($total, 2) }}
            </td>
        </tr>
    </table>
</div>

@if ($order->payment_status == 'completed')
<div style="background: #f0fff4; padding: 12px; border-radius: 8px; border-left: 4px solid #48bb78; margin: 15px 0; max-width: 100%; box-sizing: border-box;">
    ✅ <strong>Payment Completed</strong> - Your order has been fully paid. Thank you!
</div>
@else
<div style="background: #fffaf0; padding: 12px; border-radius: 8px; border-left: 4px solid #ed8936; margin: 15px 0; max-width: 100%; box-sizing: border-box;">
    💳 <strong>Payment Pending</strong> - Please prepare: <strong>₱{{ number_format($total, 2) }}</strong>
</div>
@endif

## 🛍️ Your Order Items

@foreach($order->orderItems as $item)
<div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 10px 0; background: white; max-width: 100%; overflow: hidden; box-sizing: border-box;">
    <table style="width: 100%; min-width: 250px; border-collapse: collapse;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60px; vertical-align: top; padding-right: 10px;">
                @if($item->product->primary_image && $item->product->primary_image->url)
                    <img src="{{ asset(Storage::url($item->product->primary_image->url)) }}" alt="{{ $item->product->prod_name }}" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; max-width: 100%; display: block;">
                @else
                    <div style="width: 50px; height: 50px; background: #f7fafc; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                        <span style="color: #a0aec0; font-size: 10px; text-align: center;">No Image</span>
                    </div>
                @endif
            </td>
            <td style="vertical-align: top; padding: 0 10px; width: auto;">
                <div style="font-weight: bold; color: #2d3748; margin-bottom: 8px; word-wrap: break-word; line-height: 1.4;">
                    {{ $item->product->prod_name }}
                </div>
                <div style="color: #718096; font-size: 14px; word-wrap: break-word; line-height: 1.5;">
                    <div style="margin-bottom: 4px;">Quantity: {{ number_format($item->quantity, 0) }}</div>
                    @if($item->product->prod_unit == 'kg' || $item->product->prod_unit == 'g')
                        <div style="margin-bottom: 4px;">Weight: {{ $item->product->prod_weight }} {{ $item->product->prod_unit }}</div>
                    @endif
                    @if($item->product->prod_unit == 'diff_size')
                        <div style="margin-bottom: 4px;">Size: {{ $item->size }}</div>
                    @endif
                </div>
            </td>
            <td style="vertical-align: top; text-align: right; font-weight: bold; white-space: nowrap; padding-left: 10px; width: 80px;">
                ₱{{ number_format($item->quantity * $item->price, 2) }}
            </td>
        </tr>
    </table>
</div>
@endforeach

## 📋 Order Status Details

<div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0; max-width: 100%; box-sizing: border-box;">
    @switch($order_status)
        @case('pending')
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px; line-height: 1;">⏳</div>
                <h3 style="color: #2d3748; margin-bottom: 10px; font-size: 20px; line-height: 1.3;">Order Received</h3>
                <p style="color: #4a5568; line-height: 1.6; font-size: 16px; margin: 0;">
                    We've received your order and will begin processing it shortly.<br>
                    Thank you for your patience!
                </p>
            </div>
        @break

        @case('processing')
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px; line-height: 1;">🔧</div>
                <h3 style="color: #2d3748; margin-bottom: 10px; font-size: 20px; line-height: 1.3;">Processing Your Order</h3>
                <p style="color: #4a5568; line-height: 1.6; font-size: 16px; margin: 0;">
                    Your order is now being processed.<br>
                    We're carefully preparing your items for shipment.
                </p>
            </div>
        @break

        @case('shipped')
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px; line-height: 1;">🚚</div>
                <h3 style="color: #2d3748; margin-bottom: 10px; font-size: 20px; line-height: 1.3;">On the Way!</h3>
                <p style="color: #4a5568; line-height: 1.6; font-size: 16px; margin: 0;">
                    Great news! Your order has been shipped and is on its way to you.<br>
                    Get ready to receive your package soon!
                </p>
            </div>
        @break

        @case('delivered')
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px; line-height: 1;">✅</div>
                <h3 style="color: #2d3748; margin-bottom: 10px; font-size: 20px; line-height: 1.3;">Delivered Successfully</h3>
                <p style="color: #4a5568; line-height: 1.6; font-size: 16px; margin: 0;">
                    Your order has been delivered! We hope you enjoy your purchase.<br>
                    Thank you for shopping with us!
                </p>
            </div>
        @break

        @case('cancelled')
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 10px; line-height: 1;">❌</div>
                <h3 style="color: #2d3748; margin-bottom: 10px; font-size: 20px; line-height: 1.3;">Order Cancelled</h3>
                <p style="color: #4a5568; line-height: 1.6; font-size: 16px; margin: 0;">
                    Your order has been cancelled as requested.<br>
                    If this was a mistake, please contact our support team immediately.
                </p>
            </div>
        @break
    @endswitch
</div>

<div style="background: #ebf8ff; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; max-width: 100%; box-sizing: border-box;">
    <strong>Need Help?</strong><br>
    If you have any questions about your order, our support team is here to help!
</div>

Thank you for choosing us,<br>
**The {{ config('app.name') }} Team**

@endcomponent