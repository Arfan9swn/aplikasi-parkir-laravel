<?php

namespace App\Http\Controllers;

class TicketController extends Controller
{
    /* Every ticketing page is rendered here; all dynamic data is then
       loaded client-side from the /api/* endpoints. */
    public function ticketMasuk()
    {
        return view('ticket.masuk');
    }

    /*-----------------------------------------------------------------
     | Check-out
     |---------------------------------------------------------------- */
    public function ticketKeluar()
    {
        return view('ticket.keluar');
    }

    public function ticketIndex()
    {
        return view('ticket.index');
    }

    public function ticketArea()
    {
        return view('ticket.area');
    }

    public function ticketKendaraan()
    {
        return view('ticket.kendaraan');
    }
}
