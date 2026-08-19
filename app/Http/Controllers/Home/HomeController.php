<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Home\DashboardHome;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Tanggal server dalam format YYYY-MM-DD
        $serverNow = Carbon::now()->format('Y-m-d');

        // Ambil filter tanggal.
        // Default: hari ini sampai hari ini.
        $start = $request->input('start_date', $serverNow);
        $end = $request->input('end_date', $serverNow);

        // Jika tanggal awal lebih besar, tukar posisinya.
        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        // Ambil data dashboard kunjungan.
        $perDayAll = DashboardHome::perDayAll($start, $end);
        $gender = DashboardHome::genderTotals($start, $end);
        $payment = DashboardHome::paymentTotals($start, $end);
        $visit = DashboardHome::visitTotals($start, $end);
        $referral = DashboardHome::referralTotals($start, $end);
        $topDiseases = DashboardHome::topDiseases($start, $end, 10);

        /*
         * Membuka:
         * resources/js/Pages/Dashboard.vue
         *
         * Dashboard.vue berisi:
         * - Dashboard Kunjungan
         * - Dashboard PTM
         * - Dashboard MTBM + MTBS
         */
        return Inertia::render('Dashboard', [
            'serverNow' => $serverNow,

            'filters' => [
                'start_date' => $start,
                'end_date' => $end,
            ],

            'perDayAll' => $perDayAll,
            'gender' => $gender,
            'payment' => $payment,
            'visit' => $visit,
            'referral' => $referral,
            'topDiseases' => $topDiseases,
        ]);
    }
}