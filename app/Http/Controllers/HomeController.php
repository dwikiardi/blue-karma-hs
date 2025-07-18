<?php

namespace App\Http\Controllers;

use App\Pear2\Net\RouterOS\DataFlowException;
use App\Pear2\Net\RouterOS\SocketException;
use App\Session;
use App\User;
use App\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\ErrorHandler\Error\FatalError;

class HomeController extends Controller
{
    protected $router;

    public function __construct()
    {
        if (get_router_info() != false) {
            try {
                $this->router = new RouterController(RouterController::login(
                    get_router_info()->host, get_router_info()->user, get_router_info()->pass, get_router_info()->port
                ));
            } catch (FatalError | DataFlowException | SocketException $exception) {
                $this->router = null;
            }
        }
    }

    public function qrcode(Request $request)
    {
        $users = $this->router->get_users();

        foreach ($users as $user) {
            if ($user['name'] != $request->username) {
                continue;
            } else {
                $data = [
                    'username'=> $user['name'],
                    'password'=> $user['password'],
                    'limit_uptime'=> $user['limit-uptime'],
                    'limit_bytes'=> $user['limit-bytes']
                ];
            }
        }

        return view('qr-code.index', $data);
    }

    public function showIndex()
    {
        return $this->dataTables($this->router->active_user());
    }

    public function index(Request $request)
    {
        if ($request->input('cnt')) {
            return json($this->router->card_index());
        }

        if ($request->input('del_id')) {
            $return = $this->router->del_activeUser($request->del_id, $request->del_name);

            if ($return['status']) {
                return json_swal($return['message'],'Berhasil!','success');
            } else {
                return json_swal($return['message'],'Gagal!!','error', false);
            }
        }

        return view('home.index', array('quote'=> quote_list()));
    }

    public function showUsers()
    {
        return $this->dataTables($this->router->get_users());
    }

    public function users(Request $request)
    {
        if ($request->input('add_id')) {
            $return = $this->router->put_users($request->all());

            if ($return['status']) {
                return redirect()->back()->with('success', $return['message']);
            } else {
                return redirect()->back()->with('error', $return['message']);
            }
        }

        if ($request->input('del_id')) {
            $return = $this->router->del_users($request->del_id, $request->del_desc);

            if ($return['status']) {
                return json_swal($return['message'],'Berhasil!','success');
            } else {
                return json_swal($return['message'],'Gagal!!','error', false);
            }
        }

        return view('user.index', ['profile'=> $this->router->get_profile()]);
    }

    public function showPacket()
    {
        return $this->dataTables($this->router->get_packet());
    }

    public function packet(Request $request)
    {
        if ($request->input('add_id')) {
            $return = $this->router->put_packet($request->all());

            if ($return['status']) {
                return redirect()->back()->with('success', $return['message']);
            } else {
                return redirect()->back()->with('error', $return['message']);
            }
        }

        if ($request->input('del_id')) {
            $return = $this->router->del_packet($request->del_id);

            if ($return['status']) {
                return json_swal($return['message'],'Berhasil!','success');
            } else {
                return json_swal($return['message'],'Gagal!!','error', false);
            }
        }

        return view('packet.index');
    }

    public function showClient()
    {
        return $this->dataTables($this->router->get_client());
    }

    public function client(Request $request)
    {
        if ($request->input('del_id')) {
            $return = $this->router->del_client($request->del_id, $request->del_name);

            if ($return['status']) {
                return json_swal($return['message'],'Berhasil!','success');
            } else {
                return json_swal($return['message'],'Gagal!!','error', false);
            }
        }

        return view('client.index');
    }

    public function showSession()
    {
        $data = [];
        $session = Session::all();

        if ($session) {
            foreach ($session as $key=> $item) {
                $user = User::query()->find($item->id)->first();
                if ($user) {
                    $last_log = $user->last_login;
                } else {
                    $last_log = null;
                }

                $data[] = [
                    'id'=> $item->id,
                    'hosts'=> $item->hosts,
                    'username'=> $item->username,
                    'port'=> $item->port,
                    'last_log'=> date("D, M j Y h:i:s A T", strtotime($last_log))
                ];
            }
        }

        return $this->dataTables($data);
    }

    public function session(Request $request)
    {
        if ($request->input('del_id')) {
            try {
                $session = Session::query()->find($request->del_id)->first();
                $session->delete();
                return json_swal('Session berhasil dihapus','Berhasil!','success');
            } catch (\Exception $exception) {
                return json_swal($exception->getMessage(),'Berhasil!','success');
            }
        }

        return view('session.index');
    }

    public function email(){

        return view('email.index');
    }

    public function showEmail()
    {
        // Ambil data tamu yang hanya terkait dengan user yang sedang login
        $data = Tamu::where('user_id', Auth::id())->get();

        return response()->json(['data' => $data]);
    }


    public function deleteEmail(Request $request)
    {
        if ($request->input('del_id')) {
            try {
                // Cari data tamu berdasarkan ID
                $tamu = Tamu::find($request->input('del_id'));

                // Jika data ditemukan, hapus
                if ($tamu) {
                    $tamu->delete();
                    return json_swal('Email berhasil dihapus', 'Berhasil!', 'success');
                } else {
                    return json_swal('Email tidak ditemukan', 'Gagal!', 'error', false);
                }
            } catch (\Exception $exception) {
                return json_swal($exception->getMessage(), 'Internal Server Error', 'error', false);
            }
        }

        return response()->json(['message' => 'ID tidak ditemukan'], 400);
    }

    public function router(Request $request)
    {
        //
    }
}
