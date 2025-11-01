<?php

namespace App\Filament\Widgets;

use App\Helpers\YearFilterHelper;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class SalesTracking extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Sales Tracking';
    protected static ?int $sort = 5;
    protected static string $color = 'primary';
    public ?string $filter = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
   // protected static ?string $pollingInterval = '60s';

    private YearFilterHelper $helper;

    public function __construct()
    {
        //$this->filter = app(YearFilterHelper::class)->getYear();
        $this->helper = app(YearFilterHelper::class);
        $this->filter = $this->helper->getYear();
    }

    public function updatedFilter()
    {
        //app(YearFilterHelper::class)->setYear($this->filter);
        $this->helper->setYear($this->filter);
    }
    public function getDescription(): ?string
    {
        $salesData = $this->getSalesData();
        $totalSales = array_sum($salesData);

        return 'The sales tracking chart shows the monthly sales for the year ' . $this->filter . '.';
    }

    protected function getFilters(): ?array
    {
        $availableYears = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year')
            ->where('payment_status', 'completed')
            ->where('order_status', 'delivered')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return array_combine($availableYears, $availableYears);
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $this->getSalesData(),
                    // 'borderColor' => '#4CAF50',
                    'fill' => true,
                ],
            ],
            'labels' => $this->getLabels(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getSalesData(): array
    {
        // Query orders directly without joining order_items
        $sales = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total_sales')
            ->where('payment_status', 'completed')
            ->where('order_status', 'delivered')
            ->whereYear('created_at', $this->filter)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month')
            ->toArray();

        // Initialize all months with 0 and fill with actual data
        $monthlySales = array_fill(1, 12, 0);
        foreach ($sales as $month => $amount) {
            $monthlySales[$month] = $amount;
        }

        return array_values($monthlySales);
    }

    protected function getLabels(): array
    {
        return [
            'January',
            'Febuary',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
    }


    


}
