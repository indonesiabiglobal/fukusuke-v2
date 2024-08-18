<?php

namespace App\Http\Livewire\Administration;

use App\Models\User;
use App\Models\UserAccessRole;
use App\Models\Users;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddUserController extends Component
{
    public $email;
    public $username;
    public $password;
    public $selectAll;
    public $admin;
    public $isAdministrator;
    public $dashboard;
    public $isDashboard;
    public $dashboardSeitai;
    public $isDashboardSeitai;
    public $dashboardInfure;
    public $isDashboardInfure;
    public $dashboardPpic;
    public $isDashboardPpic;
    public $dashboardQc;
    public $isDashboardQc;
    public $order;
    public $isOrder;
    public $nippoInfure;
    public $isNippoInfure;
    public $nippoSeitai;
    public $isNippoSeitai;
    public $jamKerja;
    public $isJamKerja;
    public $kenpin;
    public $isKenpin;
    public $warehouse;
    public $isWarehouse;
    public $isReport;
    public $report;

    public function cancel()
    {
        return redirect()->route('security-management');
    }

    public function save()
    {
        // $this->validate();

        DB::beginTransaction();
        try {
            $lastId = User::max('id');

            $data = new Users();
            $data->id = $lastId + 1;
            $data->email = $this->email;
            $data->username = $this->username;
            $data->password = Hash::make($this->password);
            $data->save();

            if (isset($this->admin)) {
                $admin = new UserAccessRole();
                $admin->userid = $lastId + 1;
                $admin->roleid = 1;
                $isAdministrator = 'Read';
                if (isset($this->isAdministrator)) {
                    $isAdministrator = 'Write';
                }
                $admin->rolemode = $isAdministrator;
                $admin->save();
            }
            if (isset($this->order)) {
                $order = new UserAccessRole();
                $order->userid = $lastId + 1;
                $order->roleid = 5;
                $isOrder = 'Read';
                if (isset($this->isOrder)) {
                    $isOrder = 'Write';
                }
                $order->rolemode = $isOrder;
                $order->save();
            }
            if (isset($this->nippoInfure)) {
                $nippo = new UserAccessRole();
                $nippo->userid = $lastId + 1;
                $nippo->roleid = 3;
                $isNippoInfure = 'Read';
                if (isset($this->isNippoInfure)) {
                    $isNippoInfure = 'Write';
                }
                $nippo->rolemode = $isNippoInfure;
                $nippo->save();
            }
            if (isset($this->nippoSeitai)) {
                $seitai = new UserAccessRole();
                $seitai->userid = $lastId + 1;
                $seitai->roleid = 4;
                $isNippoSeitai = 'Read';
                if (isset($this->isNippoSeitai)) {
                    $isNippoSeitai = 'Write';
                }
                $seitai->rolemode = $isNippoSeitai;
                $seitai->save();
            }
            if (isset($this->jamKerja)) {
                $jamkerja = new UserAccessRole();
                $jamkerja->userid = $lastId + 1;
                $jamkerja->roleid = 6;
                $isJamKerja = 'Read';
                if (isset($this->isJamKerja)) {
                    $isJamKerja = 'Write';
                }
                $jamkerja->rolemode = $isJamKerja;
                $jamkerja->save();
            }
            if (isset($this->kenpin)) {
                $kenpin = new UserAccessRole();
                $kenpin->userid = $lastId + 1;
                $kenpin->roleid = 7;
                $isKenpin = 'Read';
                if (isset($this->isKenpin)) {
                    $isKenpin = 'Write';
                }
                $kenpin->rolemode = $isKenpin;
                $kenpin->save();
            }
            if (isset($this->warehouse)) {
                $warehouse = new UserAccessRole();
                $warehouse->userid = $lastId + 1;
                $warehouse->roleid = 8;
                $isWarehouse = 'Read';
                if (isset($this->isWarehouse)) {
                    $isWarehouse = 'Write';
                }
                $warehouse->rolemode = $isWarehouse;
                $warehouse->save();
            }
            if (isset($this->report)) {
                $report = new UserAccessRole();
                $report->userid = $lastId + 1;
                $report->roleid = 15;
                $isReport = 'Read';
                if (isset($this->isReport)) {
                    $isReport = 'Write';
                }
                $report->rolemode = $isReport;
                $report->save();
            }
            if (isset($this->dashboard)) {
                $dashboard = new UserAccessRole();
                $dashboard->userid = $lastId + 1;
                $dashboard->roleid = 10;
                $isDashboard = 'Read';
                if (isset($this->isDashboard)) {
                    $isDashboard = 'Write';
                }
                $dashboard->rolemode = $isDashboard;
                $dashboard->save();
            }
            if (isset($this->dashboardSeitai)) {
                $dashboardSeitai = new UserAccessRole();
                $dashboardSeitai->userid = $lastId + 1;
                $dashboardSeitai->roleid = 11;
                $isDashboardSeitai = 'Read';
                if (isset($this->isDashboardSeitai)) {
                    $isDashboardSeitai = 'Write';
                }
                $dashboardSeitai->rolemode = $isDashboardSeitai;
                $dashboardSeitai->save();
            }
            if (isset($this->dashboardInfure)) {
                $dashboardInfure = new UserAccessRole();
                $dashboardInfure->userid = $lastId + 1;
                $dashboardInfure->roleid = 12;
                $isDashboardInfure = 'Read';
                if (isset($this->isDashboardInfure)) {
                    $isDashboardInfure = 'Write';
                }
                $dashboardInfure->rolemode = $isDashboardInfure;
                $dashboardInfure->save();
            }
            if (isset($this->dashboardPpic)) {
                $dashboardPpic = new UserAccessRole();
                $dashboardPpic->userid = $lastId + 1;
                $dashboardPpic->roleid = 13;
                $isDashboardPpic = 'Read';
                if (isset($this->isDashboardPpic)) {
                    $isDashboardPpic = 'Write';
                }
                $dashboardPpic->rolemode = $isDashboardPpic;
                $dashboardPpic->save();
            }
            if (isset($this->dashboardQc)) {
                $dashboardQc = new UserAccessRole();
                $dashboardQc->userid = $lastId + 1;
                $dashboardQc->roleid = 14;
                $isDashboardQc = 'Read';
                if (isset($this->isDashboardQc)) {
                    $isDashboardQc = 'Write';
                }
                $dashboardQc->rolemode = $isDashboardQc;
                $dashboardQc->save();
            }
            if (isset($this->masterTable)) {
                $masterTable = new UserAccessRole();
                $masterTable->userid = $lastId + 1;
                $masterTable->roleid = 14;
                $isMasterTable = 'Read';
                if (isset($this->isMasterTable)) {
                    $isMasterTable = 'Write';
                }
                $masterTable->rolemode = $isMasterTable;
                $masterTable->save();
            }

            DB::commit();
            $this->dispatch('notification', ['type' => 'success', 'message' => 'User saved successfully.']);
            return redirect()->route('security-management');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notification', ['type' => 'error', 'message' => 'Failed to save the User: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.administration.add-user')->extends('layouts.master');
    }
}
