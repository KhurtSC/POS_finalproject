<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; padding: 32px; }

        .header { text-align: center; margin-bottom: 28px; border-bottom: 2px solid #0d9488; padding-bottom: 16px; }
        .header h1 { font-size: 22px; font-weight: 700; color: #0d9488; }
        .header p { font-size: 11px; color: #64748b; margin-top: 4px; }

        .summary-grid { display: table; width: 100%; margin-bottom: 24px; }
        .summary-box { display: table-cell; width: 33%; padding: 14px 16px; background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 6px; text-align: center; }
        .summary-box + .summary-box { margin-left: 8px; }
        .summary-box .label { font-size: 10px; color: #0f766e; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
        .summary-box .value { font-size: 18px; font-weight: 700; color: #134e4a; margin-top: 4px; }

        .section-title { font-size: 13px; font-weight: 700; color: #0f766e; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: .4px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #0d9488; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f0fdfa; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }

        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PointSale — Sales Report</h1>
        <p>Period: {{ $from }} to {{ $to }} &nbsp;|&nbsp; Generated: {{ now()->format('M j, Y g:i A') }}</p>
    </div>

    {{-- Summary cards --}}
    <div class="summary-grid">
        <div class="summary-box">
            <div class="label">Total Transactions</div>
            <div class="value">{{ number_format($totalSales) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Revenue</div>
            <div class="value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Best Product</div>
            <div class="value" style="font-size:13px;">{{ $bestProduct }}</div>
        </div>
    </div>

    {{-- Top 5 Products --}}
    <div class="section-title">Top 5 Products</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Units Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td class="center">{{ number_format($item->total_qty) }}</td>
                <td class="right">₱{{ number_format($item->total_revenue, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="center">No data for this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Daily Breakdown --}}
    <div class="section-title">Daily Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transactions</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dailyBreakdown as $row)
            <tr>
                <td>{{ $row->date }}</td>
                <td class="center">{{ number_format($row->transactions) }}</td>
                <td class="right">₱{{ number_format($row->revenue, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="center">No data for this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">PointSale POS System &nbsp;|&nbsp; Confidential</div>

</body>
</html>