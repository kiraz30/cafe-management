<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        //antrian pesanana
        $pending_orders = Order::with(['items.menu', 'table'])
                        ->where('status','pending')
                        ->orderBy('created_at','asc')
                        ->get();

        $processing_orders = Order::with(['items.menu', 'table'])
                        ->where('status','processing')
                        ->orderBy('created_at','asc')
                        ->get();


        //summary
        $summary=[
            'total_pending' => Order::where('status','pending')->count(),
            'total_processing' => Order::where('status','processing')->count(),
            'total_completed' => Order::where('status','completed')
                                        ->whereDate('created_at', today())
                                        ->count(),
        ];

        return view('barista.dashboard', compact(
            'pending_orders','processing_orders','summary'
        ));
    }

    //update status order
    public function updateStatusOrder(Order $order){
        $newStatus = match($order->status){
            'pending' =>'processing',
            'processing' => 'completed',
            default =>$order->status
        };
        $order->update(['status'=>$newStatus]);
        return back()->with('success','Status Pesanan Berhasil di Update');
    }


    public function updateStatus(Order $order)
    {
        $newStatus = match($order->status) {
            'pending'    => 'processing',
            'processing' => 'completed',
            default      => $order->status,
        };

        DB::beginTransaction();
        try {
            $order->update(['status' => $newStatus]);

            // Bebaskan meja saat order completed
            if ($newStatus === 'completed' && $order->table_id) {
                \App\Models\Table::find($order->table_id)
                                ->update(['status' => 'available']);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update status.');
        }

        return back()->with('success', 'Status pesanan berhasil diupdate.');
    }


    
}


