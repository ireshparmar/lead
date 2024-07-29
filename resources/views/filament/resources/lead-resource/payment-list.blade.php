@php
    use Carbon\Carbon;
@endphp

<style>
    .larazeus-popover-content {
        font-family: Arial, sans-serif;
        color: #333;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 16px;
        max-width: 300px;
    }

    .payment-detail {
        border-bottom: 1px solid #e5e7eb;
        padding: 10px 0;
        display: flex;
        justify-content: space-between;
    }

    .payment-detail:last-child {
        border-bottom: none;
    }

    .payment-amount {
        font-weight: bold;
        color: #2d3748;
        font-size: 16px;
    }

    .payment-date {
        color: #718096;
        font-size: 14px;
    }

    .payment-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 12px;
    }

    .payment-total {
        font-size: 16px;
        font-weight: bold;
        color: #2d3748;
        margin-top: 10px;
    }
</style>


<div class="larazeus-popover-content">
    @foreach ($record->payments as $payment)
        <div class="payment-detail">
            <div class="payment-amount">₹{{ number_format($payment->amount, 2) }}</div>&nbsp
            <div class="payment-date">{{ Carbon::parse($payment->payment_date)->format('d M Y') }}</div>
        </div>

    @endforeach


</div>
